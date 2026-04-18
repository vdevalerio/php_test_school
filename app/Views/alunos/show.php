<?php

use App\Models\Aluno;

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/nav.php';

/**
 * @var Aluno $aluno
 */

$notas = $aluno->notas();
$title = "#{$aluno->id} - {$aluno->nome} - {$aluno->turma()->nome}";
?>

<h1><?= $title ?></h1>
<h4><?= $aluno->email ?></h4>

<?php
component('modal-trigger', [
    'id'       => 'criarNota',
    'label'    => 'Criar Nota',
    'variant'  => 'primary',
    'fetchUrl' => '/notas/create',
]);

$rows = array_map(function($nota) {
    $baseUrl = '/notas/' . $nota['id'];

    return [
        'cells' => [
            $nota['id'],
            $nota['disciplina'],
            $nota['nota'],
            $nota['data_lancamento']
        ],
        'actions' => [
            'editId'       => 'editarNota-' . $nota['id'],
            'editFetchUrl' => $baseUrl . '/edit',
            'deleteUrl'    => $baseUrl,
        ]];
}, $notas);

component('table', [
    'columns' => ['#', 'Disciplina', 'Nota', 'Data de Lançamento'],
    'rows' => $rows
]);
?>

<?php include __DIR__ . '/../layout/footer.php'; ?>