<?php

use App\Core\Response;
use App\Exceptions\DatabaseException;
use App\Exceptions\ValidationException;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/helpers.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

set_exception_handler(function (\Throwable $e) {
    if ($e instanceof DatabaseException) {
        http_response_code(500);
        Response::view(
            'errors/500',
            ['message' => $e->getMessage()]
        )->send();
    }

    if ($e instanceof ValidationException) {
        http_response_code(422);
        Response::view(
            'errors/500',
            ['message' => implode(' ', $e->errors())]
        )->send();
    }

    error_log(sprintf(
        '[%s] %s in %s:%d',
        get_class($e),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));

    http_response_code(500);
    Response::view(
        'errors/500',
        ['message' => 'Erro inesperado. Tente novamente mais tarde.']
    )->send();
});

require_once __DIR__ . '/../routes/web.php';