<?php

include '../app/Views/layout/header.php';
include '../app/Views/layout/nav.php';
include '../app/Views/layout/banner.php';

?>

<?php component('modal-trigger', [
    'id' => 'criarTurma',
    'label' => 'Criar Turma',
    'variant' => 'primary',
    'fetchUrl' => '/turmas/create',
]) ?>

<?php
$rows = array_map(fn($turma) => [
    'cells' => [$turma['id'], $turma['nome'], $turma['ano']],
    'actions' => [
        'showUrl' => '/turmas/' . $turma['id'],
        'editId' => 'editarTurma-' . $turma['id'],
        'editFetchUrl' => '/turmas/' . $turma['id'] . '/edit',
        'deleteUrl' => '/turmas/' . $turma['id'],
    ],
], $turmas);
?>

<?php component('table', ['columns' => ['#', 'Nome', 'Ano'], 'rows' => $rows]) ?>

<?php include '../app/Views/layout/footer.php'; ?>