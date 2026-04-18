<?php

use App\Models\Turma;

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/nav.php';

/**
 * @var Turma $turma
 */

$alunos = $turma->alunos();
$title = "#{$turma->id} - {$turma->nome} - {$turma->ano}";
?>

<h1><?= $title ?></h1>

<?php
component('modal-trigger', [
    'id'       => 'criarAluno',
    'label'    => 'Criar Aluno',
    'variant'  => 'primary',
    'fetchUrl' => '/alunos/create',
]);

$rows = array_map(function($aluno) {
    $baseUrl = '/alunos/' . $aluno['id'];

    return [
        'cells' => [
            $aluno['id'],
            $aluno['nome'],
            $aluno['email'],
            $aluno['criado_em']
        ],
        'actions' => [
            'showUrl'      => $baseUrl,
            'editId'       => 'editarAluno-' . $aluno['id'],
            'editFetchUrl' => $baseUrl . '/edit',
            'deleteUrl'    => $baseUrl,
        ]];
}, $alunos);

component('table', [
    'columns' => ['#', 'Nome', 'E-mail', 'Criado em'],
    'rows' => $rows
]);
?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
