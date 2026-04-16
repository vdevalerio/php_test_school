<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routes = [
    '/' => '../app/Views/index.view.php',
    '/alunos' => '../app/Controllers/AlunoController.php',
    '/notas' => '../app/Controllers/NotaController.php',
    '/turmas' => '../app/Controllers/TurmaController.php',
];

function routeToController($uri, $routes) {
    if (isset($routes[$uri])) {
        require $routes[$uri];
    } else {
        abort();
    }
}

function abort($code = 404) {
    http_response_code($code);
    require "../app/Views/{$code}.view.php";
    die();
}

routeToController($uri, $routes);