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
        $nome = trim($_POST['nome'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');
        $padariaId = (int)($_POST['padaria_id'] ?? 1);
        $redirect = trim($_POST['redirect'] ?? '');

        // Destino seguro: só aceita /minha-conta ou / (evita open redirect)
        $destino = ($redirect === '/minha-conta') ? '/minha-conta' : '/';
        $sufixoSucesso = ($destino === '/minha-conta') ? '/minha-conta?status=inscrito_sucesso' : '/?status=inscrito_sucesso';
        $sufixoErro    = ($destino === '/minha-conta') ? '/minha-conta?status=erro_cadastro'    : '/?status=erro_cadastro';
        $sufixoCampos  = ($destino === '/minha-conta') ? '/minha-conta?status=erro_campos'      : '/?status=erro_campos';

        if (empty($nome) || empty($whatsapp) || $padariaId <= 0) {
            header("Location: $sufixoCampos");
            exit;
        }

        $sucesso = Cliente::cadastrar($nome, $whatsapp, $padariaId);
        if ($sucesso) {
            // Se o cliente está logado, atualiza a sessão com o ID caso ainda não esteja
            if (!empty($whatsapp) && empty($_SESSION['cliente_id'])) {
                $cliente = Cliente::buscarPorWhatsapp($whatsapp);
                if ($cliente) {
                    $_SESSION['cliente_id']       = $cliente['id'];
                    $_SESSION['cliente_nome']      = $cliente['nome'];
                    $_SESSION['cliente_whatsapp']  = $cliente['whatsapp'];
                    $_SESSION['user_type']         = 'cliente';
                }
            }
            header("Location: $sufixoSucesso");
            exit;
        }

        header("Location: $sufixoErro");
        exit;
    }
}
