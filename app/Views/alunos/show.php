<?php

include '../app/Views/layout/header.php';
include '../app/Views/layout/nav.php';

$notas = $aluno->notas();
?>

<h1>#<?= $aluno->id ?> - <?= $aluno->nome ?> - <?= $aluno->turma()->nome ?></h1>
<h4><?= $aluno->email ?></h4>

<?php component('modal-trigger', [
    'id' => 'criarNota',
    'label' => 'Criar Nota',
    'variant' => 'primary',
    'fetchUrl' => '/notas/create',
]) ?>

<?php
$rows = array_map(fn($nota) => [
    'cells' => [$nota['id'], $nota['disciplina'], $nota['nota'], $nota['data_lancamento']],
    'actions' => [
        'showUrl' => '/notas/' . $nota['id'],
        'editId' => 'editarNota-' . $nota['id'],
        'editFetchUrl' => '/notas/' . $nota['id'] . '/edit',
        'deleteUrl' => '/notas/' . $nota['id'],
    ],
], $notas);
?>

<?php component('table', ['columns' => ['#', 'Disciplina', 'Nota', 'Data de Lançamento'], 'rows' => $rows]) ?>

<?php include '../app/Views/layout/footer.php'; ?>