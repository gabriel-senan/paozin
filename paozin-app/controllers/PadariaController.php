<?php

class PadariaController {
    public function home(): void {
        try {
            $padarias         = Padaria::listarTodas();
            $padariaDestaque  = Padaria::listarDestaques();
        } catch (Exception $e) {
            $padarias        = [];
            $padariaDestaque = [];
        }

        require_once __DIR__ . '/../views/home.php';
    }

    public function inscreverCliente(): void {
        $nome      = trim($_POST['nome'] ?? '');
        $whatsapp  = trim($_POST['whatsapp'] ?? '');
        $padariaId = (int)($_POST['padaria_id'] ?? 1);
        $redirect  = trim($_POST['redirect'] ?? '');

        // Destino seguro: só aceita /minha-conta ou / (evita open redirect)
        $destino = ($redirect === '/minha-conta') ? '/minha-conta' : '/';

        if (empty($nome) || empty($whatsapp) || $padariaId <= 0) {
            header("Location: " . $this->urlStatus($destino, 'erro_campos'));
            exit;
        }

        $sucesso = Cliente::cadastrar($nome, $whatsapp, $padariaId);
        if ($sucesso) {
            if (empty($_SESSION['cliente_id'])) {
                $cliente = Cliente::buscarPorWhatsapp($whatsapp);
                if ($cliente) {
                    $_SESSION['cliente_id']      = $cliente['id'];
                    $_SESSION['cliente_nome']     = $cliente['nome'];
                    $_SESSION['cliente_whatsapp'] = $cliente['whatsapp'];
                    $_SESSION['user_type']        = 'cliente';
                }
            }
            header("Location: " . $this->urlStatus($destino, 'inscrito_sucesso'));
            exit;
        }

        header("Location: " . $this->urlStatus($destino, 'erro_cadastro'));
        exit;
    }

    private function urlStatus(string $base, string $status): string {
        $sep = str_contains($base, '?') ? '&' : '?';
        return $base . $sep . 'status=' . $status;
    }
}
