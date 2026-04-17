<?php

$router = new App\Core\Router();

$router->get('/turmas', 'TurmaController@index');
$router->get('/turmas/create', 'TurmaController@create');
$router->post('/turmas', 'TurmaController@store');
$router->get('/turmas/{id}', 'TurmaController@show');
$router->get('/turmas/{id}/edit', 'TurmaController@edit');
$router->put('/turmas/{id}', 'TurmaController@update');
$router->delete('/turmas/{id}', 'TurmaController@destroy');

$router->get('/alunos', 'AlunoController@index');
$router->get('/alunos/create', 'AlunoController@create');
$router->post('/alunos', 'AlunoController@store');
$router->get('/alunos/{id}', 'AlunoController@show');
$router->get('/alunos/{id}/edit', 'AlunoController@edit');
$router->put('/alunos/{id}', 'AlunoController@update');
$router->delete('/alunos/{id}', 'AlunoController@destroy');

$router->dispatch();