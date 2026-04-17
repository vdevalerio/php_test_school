<?php

include '../app/Views/layout/header.php';
include '../app/Views/layout/nav.php';
include '../app/Views/layout/banner.php';

?>

<?php component('modal-trigger', [
    'id' => 'criarAluno',
    'label' => 'Criar Aluno',
    'variant' => 'primary',
    'fetchUrl' => '/alunos/create',
]) ?>

<?php
$rows = array_map(fn($aluno) => [
    'cells' => [$aluno['id'], $aluno['nome'], $aluno['email'], $aluno['turma_id']],
    'actions' => [
        'showUrl' => '/alunos/' . $aluno['id'],
        'editId' => 'editarAluno-' . $aluno['id'],
        'editFetchUrl' => '/alunos/' . $aluno['id'] . '/edit',
        'deleteUrl' => '/alunos/' . $aluno['id'],
    ],
], $alunos);
?>

<?php component('table', ['columns' => ['#', 'Nome', 'Email', 'Turma'], 'rows' => $rows]) ?>

<?php include '../app/Views/layout/footer.php'; ?>
