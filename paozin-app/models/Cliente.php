<?php

class Cliente {
    /**
     * Normaliza um número de WhatsApp para o formato E.164 com DDI 55.
     * Retorna null se o número tiver menos de 10 dígitos após limpeza.
     */
    private static function normalizarWhatsapp(string $whatsapp): ?string {
        $clean = preg_replace('/\D/', '', $whatsapp);
        if (strlen($clean) < 10) {
            return null;
        }
        if (strlen($clean) === 10 || strlen($clean) === 11) {
            $clean = '55' . $clean;
        }
        return $clean;
    }

    public static function cadastrar(string $nome, string $whatsapp, int $padariaId): bool {
        $db = Database::getConnection();

        $cleanWhatsapp = self::normalizarWhatsapp($whatsapp);
        if ($cleanWhatsapp === null) {
            return false;
        }

        $db->beginTransaction();
        try {
            $stmt = $db->prepare("SELECT id FROM clientes WHERE whatsapp = ?");
            $stmt->execute([$cleanWhatsapp]);
            $clienteId = $stmt->fetchColumn();

            if (!$clienteId) {
                $stmtInsert = $db->prepare("INSERT INTO clientes (whatsapp, nome, ativo) VALUES (?, ?, 1)");
                $stmtInsert->execute([$cleanWhatsapp, trim($nome)]);
                $clienteId = $db->lastInsertId();
            } else {
                $stmtUpdate = $db->prepare("UPDATE clientes SET ativo = 1, nome = ? WHERE id = ?");
                $stmtUpdate->execute([trim($nome), $clienteId]);
            }

            $stmtCheck = $db->prepare("SELECT id FROM padaria_cliente WHERE padaria_id = ? AND cliente_id = ?");
            $stmtCheck->execute([$padariaId, $clienteId]);
            if (!$stmtCheck->fetchColumn()) {
                $stmtLink = $db->prepare("INSERT INTO padaria_cliente (padaria_id, cliente_id) VALUES (?, ?)");
                $stmtLink->execute([$padariaId, $clienteId]);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    public static function buscarPorWhatsapp(string $whatsapp): ?array {
        $db = Database::getConnection();

        $cleanWhatsapp = self::normalizarWhatsapp($whatsapp);
        if ($cleanWhatsapp === null) {
            return null;
        }

        $stmt = $db->prepare("SELECT * FROM clientes WHERE whatsapp = ? OR whatsapp LIKE ?");
        $stmt->execute([$cleanWhatsapp, '%' . substr($cleanWhatsapp, -8)]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function listarPadariasSeguidas(int $clienteId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT p.* 
            FROM padarias p
            INNER JOIN padaria_cliente pc ON p.id = pc.padaria_id
            WHERE pc.cliente_id = ?
        ");
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll();
    }

    public static function removerVinculo(int $clienteId, int $padariaId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM padaria_cliente WHERE cliente_id = ? AND padaria_id = ?");
        return $stmt->execute([$clienteId, $padariaId]);
    }

    /**
     * Cadastra um cliente sem vinculá-lo a nenhuma padaria.
     * Usado no fluxo de sign-up da landing page.
     */
    public static function cadastrarSemPadaria(string $nome, string $whatsapp): bool {
        $db = Database::getConnection();

        $cleanWhatsapp = self::normalizarWhatsapp($whatsapp);
        if ($cleanWhatsapp === null) {
            return false;
        }

        try {
            $stmt = $db->prepare("SELECT id FROM clientes WHERE whatsapp = ?");
            $stmt->execute([$cleanWhatsapp]);
            $existingId = $stmt->fetchColumn();

            if ($existingId) {
                $upd = $db->prepare("UPDATE clientes SET ativo = 1, nome = ? WHERE id = ?");
                return $upd->execute([trim($nome), $existingId]);
            }

            $ins = $db->prepare("INSERT INTO clientes (whatsapp, nome, ativo) VALUES (?, ?, 1)");
            return $ins->execute([$cleanWhatsapp, trim($nome)]);
        } catch (Exception $e) {
            return false;
        }
    }
}
