<?php

class PadariaController {
    public function home(): void {
        try {
            $padarias = Padaria::listarTodas();
        } catch (Exception $e) {
            $padarias = [];
        }

        require_once __DIR__ . '/../views/home.php';
    }

    public function inscreverCliente(): void {
        $nome = trim($_POST['nome'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');
        $padariaId = (int)($_POST['padaria_id'] ?? 1);

        if (empty($nome) || empty($whatsapp) || $padariaId <= 0) {
            header("Location: /?status=erro_campos");
            exit;
        }

        $sucesso = Cliente::cadastrar($nome, $whatsapp, $padariaId);
        if ($sucesso) {
            header("Location: /?status=inscrito_sucesso");
            exit;
        }

        header("Location: /?status=erro_cadastro");
        exit;
    }
}
