<?php

require "helpers.php";
# require "../app/Views/index.view.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routes = [
    '/' => '../app/Views/index.view.php',
    '/alunos' => '../app/Controllers/AlunoController.php',
    '/notas' => '../app/Controllers/NotaController.php',
    '/turmas' => '../app/Controllers/TurmaController.php',
];

if (isset($routes[$uri])) {
    require $routes[$uri];
}
