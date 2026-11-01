<?php

class AuthController {
    public function loginPadaria(): void {
        $telefone  = trim($_POST['telefone'] ?? '');
        $padariaId = (int)($_POST['padaria_id'] ?? 0);
        $senha     = trim($_POST['senha'] ?? '');

        $padaria = null;
        if (!empty($telefone)) {
            $padaria = Padaria::buscarPorTelefone($telefone);
            if (!$padaria) {
                header("Location: /?modal=padaria_cadastro&tel=" . urlencode($telefone));
                exit;
            }
        } elseif ($padariaId > 0) {
            $padaria = Padaria::buscarPorId($padariaId);
        }

        if (!$padaria) {
            header("Location: /?modal=padaria&status=erro_login_padaria");
            exit;
        }

        if (!Padaria::validarSenha($padaria, $senha)) {
            $paramTel = !empty($telefone) ? '&tel=' . urlencode($telefone) : '';
            header("Location: /?modal=padaria&status=senha_incorreta" . $paramTel);
            exit;
        }

        $_SESSION['padaria_id']   = $padaria['id'];
        $_SESSION['padaria_nome'] = $padaria['nome'];
        $_SESSION['user_type']    = 'padaria';

        header("Location: /painel?status=bem_vindo");
        exit;
    }

    public function cadastrarPadaria(): void {
        $nome           = trim($_POST['nome'] ?? '');
        $telefone       = trim($_POST['telefone'] ?? '');
        $senha          = trim($_POST['senha'] ?? '');
        $confirmarSenha = trim($_POST['confirmar_senha'] ?? '');
        $endereco       = trim($_POST['endereco'] ?? '');
        $precoQuilo     = (float)str_replace(',', '.', $_POST['preco_pao_quilo'] ?? '15.90');
        $precoUnidade   = isset($_POST['preco_pao_unidade']) && $_POST['preco_pao_unidade'] !== ''
            ? (float)str_replace(',', '.', $_POST['preco_pao_unidade'])
            : 0.95;
        $horarioFunc     = trim($_POST['horario_funcionamento'] ?? '06:00 às 20:00');
        $horariosFornada = trim($_POST['horarios_fornada'] ?? '06:30, 16:30');

        if (empty($nome) || empty($telefone)) {
            header("Location: /?modal=padaria_cadastro&status=campos_obrigatorios");
            exit;
        }

        if (!empty($senha) && !empty($confirmarSenha) && $senha !== $confirmarSenha) {
            header("Location: /?modal=padaria_cadastro&status=senhas_diferentes&tel=" . urlencode($telefone));
            exit;
        }

        $padariaExistente = Padaria::buscarPorTelefone($telefone);
        if ($padariaExistente) {
            if (!Padaria::validarSenha($padariaExistente, $senha)) {
                header("Location: /?modal=padaria&status=senha_incorreta&tel=" . urlencode($telefone));
                exit;
            }
            $_SESSION['padaria_id']   = $padariaExistente['id'];
            $_SESSION['padaria_nome'] = $padariaExistente['nome'];
            $_SESSION['user_type']    = 'padaria';
            header("Location: /painel?status=bem_vindo");
            exit;
        }

        $novoId = Padaria::cadastrar([
            'nome'                  => $nome,
            'telefone'              => $telefone,
            'senha'                 => $senha ?: 'paozin123',
            'endereco'              => $endereco ?: 'Bairro Central',
            'preco_pao_quilo'       => $precoQuilo,
            'preco_pao_unidade'     => $precoUnidade,
            'horario_funcionamento' => $horarioFunc,
            'horarios_fornada'      => $horariosFornada,
        ]);

        if (!$novoId) {
            header("Location: /?modal=padaria_cadastro&status=erro");
            exit;
        }

        $_SESSION['padaria_id']   = $novoId;
        $_SESSION['padaria_nome'] = $nome;
        $_SESSION['user_type']    = 'padaria';

        header("Location: /painel?status=cadastro_sucesso");
        exit;
    }

    public function salvarPadaria(): void {
        if (empty($_SESSION['padaria_id'])) {
            header("Location: /?status=login_necessario");
            exit;
        }

        $id           = (int)$_SESSION['padaria_id'];
        $nome         = trim($_POST['nome'] ?? '');
        $endereco     = trim($_POST['endereco'] ?? '');
        $telefone     = trim($_POST['telefone'] ?? '');
        $senha        = trim($_POST['senha'] ?? '');
        $precoQuilo   = (float)str_replace(',', '.', $_POST['preco_pao_quilo'] ?? '15.90');
        $precoUnidade = isset($_POST['preco_pao_unidade']) && $_POST['preco_pao_unidade'] !== ''
            ? (float)str_replace(',', '.', $_POST['preco_pao_unidade'])
            : null;
        $horarioFunc     = trim($_POST['horario_funcionamento'] ?? '');
        $horariosFornada = trim($_POST['horarios_fornada'] ?? '');

        if (empty($nome)) {
            header("Location: /painel?status=erro_dados");
            exit;
        }

        $dadosAtualizar = [
            'nome'                  => $nome,
            'endereco'              => $endereco,
            'telefone'              => $telefone,
            'preco_pao_quilo'       => $precoQuilo,
            'preco_pao_unidade'     => $precoUnidade,
            'horario_funcionamento' => $horarioFunc,
            'horarios_fornada'      => $horariosFornada,
        ];
        if (!empty($senha)) {
            $dadosAtualizar['senha'] = $senha;
        }

        $sucesso = Padaria::atualizarDados($id, $dadosAtualizar);

        if ($sucesso) {
            $_SESSION['padaria_nome'] = $nome;
            header("Location: /painel?status=dados_atualizados");
            exit;
        }

        header("Location: /painel?status=erro_atualizar");
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
            header("Location: /?modal=cadastro&tel=" . urlencode($whatsapp));
            exit;
        }

        $_SESSION['cliente_id']      = $cliente['id'];
        $_SESSION['cliente_nome']    = $cliente['nome'];
        $_SESSION['cliente_whatsapp'] = $cliente['whatsapp'];
        $_SESSION['user_type']        = 'cliente';

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

        $_SESSION['cliente_id']      = $cliente['id'];
        $_SESSION['cliente_nome']    = $cliente['nome'];
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

        $clienteId       = (int)$_SESSION['cliente_id'];
        $padariasSeguidas = Cliente::listarPadariasSeguidas($clienteId);
        $todasPadarias   = Padaria::listarTodas();
        $idsSeguidas     = array_column($padariasSeguidas, 'id');

        require_once __DIR__ . '/../views/minha-conta.php';
    }

    public function cancelarAlerta(): void {
        if (empty($_SESSION['cliente_id'])) {
            header("Location: /");
            exit;
        }

        $padariaId = (int)($_POST['padaria_id'] ?? 0);
        $clienteId = (int)$_SESSION['cliente_id'];

        if ($padariaId <= 0) {
            header("Location: /minha-conta");
            exit;
        }

        Cliente::removerVinculo($clienteId, $padariaId);

        header("Location: /minha-conta?status=alerta_removido");
        exit;
    }
}
