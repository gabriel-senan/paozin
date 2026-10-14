<?php

class Cliente {
    public static function cadastrar(string $nome, string $whatsapp, int $padariaId): bool {
        $db = Database::getConnection();
        
        // Limpa formatação do número de telefone (mantém apenas dígitos)
        $cleanWhatsapp = preg_replace('/\D/', '', $whatsapp);
        if (strlen($cleanWhatsapp) < 10) {
            return false;
        }

        // Garante DDI 55 se o usuário digitou apenas DDD + número
        if (strlen($cleanWhatsapp) === 10 || strlen($cleanWhatsapp) === 11) {
            $cleanWhatsapp = '55' . $cleanWhatsapp;
        }

        $db->beginTransaction();
        try {
            // Busca ou insere o cliente
            $stmt = $db->prepare("SELECT id FROM clientes WHERE whatsapp = ?");
            $stmt->execute([$cleanWhatsapp]);
            $clienteId = $stmt->fetchColumn();

            if (!$clienteId) {
                $stmtInsert = $db->prepare("INSERT INTO clientes (whatsapp, nome, ativo) VALUES (?, ?, 1)");
                $stmtInsert->execute([$cleanWhatsapp, trim($nome)]);
                $clienteId = $db->lastInsertId();
            } else {
                // Se já existia, reativa caso estivesse inativo e atualiza nome
                $stmtUpdate = $db->prepare("UPDATE clientes SET ativo = 1, nome = ? WHERE id = ?");
                $stmtUpdate->execute([trim($nome), $clienteId]);
            }

            // Vincula à padaria se ainda não estiver vinculado
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

    public static function desativar(string $whatsapp): bool {
        $db = Database::getConnection();
        $cleanWhatsapp = preg_replace('/\D/', '', $whatsapp);
        $stmt = $db->prepare("UPDATE clientes SET ativo = 0 WHERE whatsapp = ?");
        return $stmt->execute([$cleanWhatsapp]);
    }

    public static function buscarPorWhatsapp(string $whatsapp): ?array {
        $db = Database::getConnection();
        $cleanWhatsapp = preg_replace('/\D/', '', $whatsapp);
        if (strlen($cleanWhatsapp) === 10 || strlen($cleanWhatsapp) === 11) {
            $cleanWhatsapp = '55' . $cleanWhatsapp;
        }
        $stmt = $db->prepare("SELECT * FROM clientes WHERE whatsapp = ? OR whatsapp LIKE ?");
        $stmt->execute([$cleanWhatsapp, '%' . substr($cleanWhatsapp, -9)]);
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

        $cleanWhatsapp = preg_replace('/\D/', '', $whatsapp);
        if (strlen($cleanWhatsapp) < 10) {
            return false;
        }
        if (strlen($cleanWhatsapp) === 10 || strlen($cleanWhatsapp) === 11) {
            $cleanWhatsapp = '55' . $cleanWhatsapp;
        }

        try {
            $stmt = $db->prepare("SELECT id FROM clientes WHERE whatsapp = ?");
            $stmt->execute([$cleanWhatsapp]);
            $existingId = $stmt->fetchColumn();

            if ($existingId) {
                // Reativa e atualiza nome caso já exista
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
