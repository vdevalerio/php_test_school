<?php

use App\Models\Aluno;

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/nav.php';

/**
 * @var Aluno $aluno
 */

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
    $baseUrl = '/notas/' . $nota->id;

    return [
        'cells' => [
            $nota->id,
            $nota->disciplina,
            $nota->nota,
            ['value' => $nota->data_lancamento, 'format' => 'd/m/Y']
        ],
        'actions' => [
            'editId'       => 'editarNota-' . $nota->id,
            'editFetchUrl' => $baseUrl . '/edit',
            'deleteUrl'    => $baseUrl,
        ]];
}, $pagination['data']);

$columns = [
    ['label' => '#',                  'sort' => 'id'],
    ['label' => 'Disciplina',         'sort' => 'disciplina'],
    ['label' => 'Nota',               'sort' => 'nota'],
    ['label' => 'Data de Lançamento', 'sort' => 'data_lancamento'],
];

component('table', [
    'columns'    => $columns,
    'rows'       => $rows,
    'pagination' => $pagination,
    'sort'       => $sort,
    'direction'  => $direction,
]);
?>

<?php include __DIR__ . '/../layout/footer.php'; ?>