<?php

class Padaria {
    public static function buscarPorId(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM padarias WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function listarTodas(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM padarias WHERE status_assinatura = 'ativo' ORDER BY nota_media DESC");
        return $stmt->fetchAll();
    }

    public static function verificarCooldown(int $id, int $limiteMinutos = 30): array {
        $padaria = self::buscarPorId($id);
        if (!$padaria) {
            return ['em_cooldown' => false, 'minutos_restantes' => 0];
        }

        if (empty($padaria['ultima_fornada'])) {
            return ['em_cooldown' => false, 'minutos_restantes' => 0];
        }

        $timestampUltima = strtotime($padaria['ultima_fornada']);
        $minutosPassados = (time() - $timestampUltima) / 60;

        if ($minutosPassados < $limiteMinutos) {
            return [
                'em_cooldown' => true,
                'minutos_restantes' => (int)ceil($limiteMinutos - $minutosPassados),
                'ultima_fornada' => $padaria['ultima_fornada']
            ];
        }

        return ['em_cooldown' => false, 'minutos_restantes' => 0];
    }

    public static function registrarFornada(int $id, float $preco): bool {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // Atualiza carimbo na padaria
            $stmt1 = $db->prepare("UPDATE padarias SET ultima_fornada = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt1->execute([$id]);

            // Conta total de assinantes no momento
            $stmtCount = $db->prepare("SELECT COUNT(*) FROM padaria_cliente pc INNER JOIN clientes c ON c.id = pc.cliente_id WHERE pc.padaria_id = ? AND c.ativo = 1");
            $stmtCount->execute([$id]);
            $totalAvisados = (int)$stmtCount->fetchColumn();

            // Grava histórico da fornada
            $stmt2 = $db->prepare("INSERT INTO fornadas (padaria_id, preco_quilo_momento, total_avisados) VALUES (?, ?, ?)");
            $stmt2->execute([$id, $preco, $totalAvisados]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}
