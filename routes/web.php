<?php

$router = new App\Core\Router();

$router->get('/turmas', 'TurmaController@index');
$router->get('/turmas/create', 'TurmaController@create');
$router->post('/turmas', 'TurmaController@store');
$router->get('/turmas/{id}', 'TurmaController@show');
$router->get('/turmas/{id}/edit', 'TurmaController@edit');
$router->put('/turmas/{id}', 'TurmaController@update');
$router->delete('/turmas/{id}', 'TurmaController@destroy');

$router->dispatch();