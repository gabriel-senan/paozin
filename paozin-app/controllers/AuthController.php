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
            header("Location: /?modal=cliente&status=erro");
            exit;
        }

        $cliente = Cliente::buscarPorWhatsapp($whatsapp);
        if (!$cliente) {
            // Número não encontrado — redireciona para o modal de cadastro com o telefone preenchido
            header("Location: /?modal=cadastro&tel=" . urlencode($whatsapp));
            exit;
        }

        $_SESSION['cliente_id'] = $cliente['id'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        $_SESSION['cliente_whatsapp'] = $cliente['whatsapp'];
        $_SESSION['user_type'] = 'cliente';

        header("Location: /minha-conta");
        exit;
    }

    public function cadastrarCliente(): void {
        $nome     = trim($_POST['nome'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');

        if (empty($nome) || empty($whatsapp)) {
            header("Location: /?modal=cadastro&status=campos_obrigatorios");
            exit;
        }

        $ok = Cliente::cadastrarSemPadaria($nome, $whatsapp);
        if (!$ok) {
            header("Location: /?modal=cadastro&status=erro");
            exit;
        }

        $cliente = Cliente::buscarPorWhatsapp($whatsapp);
        if (!$cliente) {
            header("Location: /?modal=cadastro&status=erro");
            exit;
        }

        $_SESSION['cliente_id']       = $cliente['id'];
        $_SESSION['cliente_nome']     = $cliente['nome'];
        $_SESSION['cliente_whatsapp'] = $cliente['whatsapp'];
        $_SESSION['user_type']        = 'cliente';

        header("Location: /minha-conta?status=bem_vindo");
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

        // Todas as padarias ativas com coordenadas, para o mapa
        try {
            $todasPadarias = Padaria::listarTodas();
        } catch (Exception $e) {
            $todasPadarias = [];
        }

        // IDs das padarias que o cliente já segue (para marcar no mapa)
        $idsSeguidas = array_column($padariasSeguidas, 'id');

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
