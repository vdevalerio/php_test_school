<?php

$router = new App\Core\Router();

$router->get('/turmas', 'TurmaController@index');
$router->get('/turmas/create', 'TurmaController@create');
$router->post('/turmas', 'TurmaController@store');

$router->dispatch();