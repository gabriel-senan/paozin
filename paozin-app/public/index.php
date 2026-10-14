<?php
session_start();

// Autoload simples baseado nas convenções do MVC
spl_autoload_register(function ($class_name) {
    $directories = [
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/../config/'
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
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($uri === '/' && $method === 'GET') {
    $controller = new PadariaController();
    $controller->home();
} elseif ($uri === '/inscrever' && $method === 'POST') {
    $controller = new PadariaController();
    $controller->inscreverCliente();
} elseif ($uri === '/login-padaria' && $method === 'POST') {
    $controller = new AuthController();
    $controller->loginPadaria();
} elseif ($uri === '/login-cliente' && $method === 'POST') {
    $controller = new AuthController();
    $controller->loginCliente();
} elseif ($uri === '/cadastrar-cliente' && $method === 'POST') {
    $controller = new AuthController();
    $controller->cadastrarCliente();
} elseif ($uri === '/minha-conta' && $method === 'GET') {
    $controller = new AuthController();
    $controller->minhaConta();
} elseif ($uri === '/cancelar-alerta' && $method === 'POST') {
    $controller = new AuthController();
    $controller->cancelarAlerta();
} elseif ($uri === '/logout' && $method === 'GET') {
    $controller = new AuthController();
    $controller->logout();
} elseif ($uri === '/painel' && $method === 'GET') {
    require_once __DIR__ . '/../views/painel.php';
} elseif ($uri === '/painel/fornada-pronta' && $method === 'POST') {
    $controller = new AlertaController();
    $controller->dispararFornada();
} else {
    // Redireciona para a landing page
    header("Location: /");
    exit;
}
