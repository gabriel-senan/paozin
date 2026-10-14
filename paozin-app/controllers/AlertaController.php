<?php

class AlertaController {
    private const COOLDOWN_MINUTOS = 30;

    public function dispararFornada(): void {
        $padariaId = (int)($_SESSION['padaria_id'] ?? 1);

        $padaria = Padaria::buscarPorId($padariaId);
        if (!$padaria || $padaria['status_assinatura'] !== 'ativo') {
            header("Location: /painel?status=inativo");
            exit;
        }

        // 1. Verificação de Cooldown (Anti-Flood)
        $cooldown = Padaria::verificarCooldown($padariaId, self::COOLDOWN_MINUTOS);
        if ($cooldown['em_cooldown']) {
            header("Location: /painel?status=cooldown&minutos=" . $cooldown['minutos_restantes']);
            exit;
        }

        // 2. Disparo do Webhook para o n8n
        $n8nUrl = getenv('N8N_WEBHOOK_URL') ?: 'http://localhost:5678/webhook/fornada-pronta';
        $payload = json_encode([
            'padaria_id'   => $padariaId,
            'nome_padaria' => $padaria['nome'],
            'preco_atual'  => (float)$padaria['preco_pao_quilo'],
            'timestamp'    => date('Y-m-d H:i:s')
        ]);

        $ch = curl_init($n8nUrl);
        curl_setopt_array($ch, [
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 5
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Se n8n aceitou ou em modo de simulação local (se n8n não estiver rodando no momento)
        if ($httpCode === 200 || $httpCode === 201) {
            Padaria::registrarFornada($padariaId, (float)$padaria['preco_pao_quilo']);
            header("Location: /painel?status=sucesso");
            exit;
        }

        // Permite simular em desenvolvimento se passar ?simular=1 ou avisa falha
        if (isset($_GET['simular']) && $_GET['simular'] === '1') {
            Padaria::registrarFornada($padariaId, (float)$padaria['preco_pao_quilo']);
            header("Location: /painel?status=sucesso_simulado");
            exit;
        }

        header("Location: /painel?status=erro");
        exit;
    }
}
