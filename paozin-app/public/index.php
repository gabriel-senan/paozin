<?php
session_start();

// Autoload simples baseado nas convenções do MVC
spl_autoload_register(function ($class_name) {
    $directories = [
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/../config/',
    ];

    foreach ($directories as $dir) {
        $file = $dir . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Captura da rota solicitada
$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Tabela de rotas: [uri, method] => callable que recebe nenhum argumento
$routes = [
    ['GET',  '/',                    fn() => (new PadariaController())->home()],
    ['POST', '/inscrever',           fn() => (new PadariaController())->inscreverCliente()],
    ['POST', '/login-padaria',       fn() => (new AuthController())->loginPadaria()],
    ['POST', '/cadastrar-padaria',   fn() => (new AuthController())->cadastrarPadaria()],
    ['POST', '/painel/salvar',       fn() => (new AuthController())->salvarPadaria()],
    ['POST', '/login-cliente',       fn() => (new AuthController())->loginCliente()],
    ['POST', '/cadastrar-cliente',   fn() => (new AuthController())->cadastrarCliente()],
    ['POST', '/definir-senha-cliente', fn() => (new AuthController())->definirSenhaCliente()],
    ['GET',  '/minha-conta',         fn() => (new AuthController())->minhaConta()],
    ['POST', '/minha-conta/salvar',  fn() => (new AuthController())->atualizarCliente()],
    ['POST', '/cancelar-alerta',     fn() => (new AuthController())->cancelarAlerta()],
    ['GET',  '/logout',              fn() => (new AuthController())->logout()],
    ['POST', '/logout',              fn() => (new AuthController())->logout()],
    ['POST', '/painel/fornada-pronta', fn() => (new AlertaController())->dispararFornada()],
];

// Rota especial: /painel requer sessão ativa de padaria
if ($uri === '/painel' && $method === 'GET') {
    if (empty($_SESSION['padaria_id'])) {
        header('Location: /');
        exit;
    }
    require_once __DIR__ . '/../views/painel.php';
    exit;
}

// Despacho das rotas da tabela
foreach ($routes as [$routeMethod, $routeUri, $handler]) {
    if ($uri === $routeUri && $method === $routeMethod) {
        $handler();
        exit;
    }
}

// Rota não encontrada — volta para a landing page
header('Location: /');
exit;
