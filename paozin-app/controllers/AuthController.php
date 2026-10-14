<?php

class AuthController {
    public function loginPadaria(): void {
        $padariaId = (int)($_POST['padaria_id'] ?? 0);
        if ($padariaId <= 0) {
            header("Location: /?status=erro_login_padaria");
            exit;
        }

        $padaria = Padaria::buscarPorId($padariaId);
        if (!$padaria) {
            header("Location: /?status=padaria_nao_encontrada");
            exit;
        }

        // Salva na sessão
        $_SESSION['padaria_id'] = $padaria['id'];
        $_SESSION['padaria_nome'] = $padaria['nome'];
        $_SESSION['user_type'] = 'padaria';

        header("Location: /painel?status=bem_vindo");
        exit;
    }

    public function loginCliente(): void {
        $whatsapp = trim($_POST['whatsapp'] ?? '');
        if (empty($whatsapp)) {
            header("Location: /?status=erro_login_cliente");
            exit;
        }

        $cliente = Cliente::buscarPorWhatsapp($whatsapp);
        if (!$cliente) {
            // Se não encontrou, avisa que o cliente pode se cadastrar na vitrine
            header("Location: /?status=cliente_novo&tel=" . urlencode($whatsapp) . "#padarias");
            exit;
        }

        $_SESSION['cliente_id'] = $cliente['id'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        $_SESSION['cliente_whatsapp'] = $cliente['whatsapp'];
        $_SESSION['user_type'] = 'cliente';

        header("Location: /minha-conta");
        exit;
    }

    public function logout(): void {
        session_destroy();
        header("Location: /?status=desconectado");
        exit;
    }

    public function minhaConta(): void {
        if (empty($_SESSION['cliente_id'])) {
            header("Location: /?status=login_necessario");
            exit;
        }

        $clienteId = (int)$_SESSION['cliente_id'];
        $padariasSeguidas = Cliente::listarPadariasSeguidas($clienteId);

        require_once __DIR__ . '/../views/minha-conta.php';
    }

    public function cancelarAlerta(): void {
        if (empty($_SESSION['cliente_id'])) {
            header("Location: /");
            exit;
        }

        $padariaId = (int)($_POST['padaria_id'] ?? 0);
        $clienteId = (int)$_SESSION['cliente_id'];

        if ($padariaId > 0) {
            Cliente::removerVinculo($clienteId, $padariaId);
        }

        header("Location: /minha-conta?status=alerta_removido");
        exit;
    }
}
