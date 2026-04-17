<?php

use App\Models\Aluno;

include '../app/Views/layout/header.php';
include '../app/Views/layout/nav.php';
include '../app/Views/layout/banner.php';

?>

<?php component('modal-trigger', [
    'id'       => 'criarNota',
    'label'    => 'Criar Nota',
    'variant'  => 'primary',
    'fetchUrl' => '/notas/create',
]) ?>

<?php
$rows = array_map(fn($nota) => [
    'cells'   => [
        $nota['id'],
        Aluno::find($nota['aluno_id'])->nome,
        $nota['disciplina'],
        $nota['nota'],
        $nota['data_lancamento'],
    ],
    'actions' => [
        'editId'       => 'editarNota-' . $nota['id'],
        'editFetchUrl' => '/notas/' . $nota['id'] . '/edit',
        'deleteUrl'    => '/notas/' . $nota['id'],
    ],
], $notas);
?>

<?php component('table', ['columns' => ['#', 'Aluno', 'Disciplina', 'Nota', 'Data de Lançamento'], 'rows' => $rows]) ?>

<?php include '../app/Views/layout/footer.php'; ?>
