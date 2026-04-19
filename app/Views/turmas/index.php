<?php

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/nav.php';
include __DIR__ . '/../layout/banner.php';

component('modal-trigger', [
    'id'       => 'criarTurma',
    'label'    => 'Criar Turma',
    'variant'  => 'primary',
    'fetchUrl' => '/turmas/create',
]);

$rows = array_map(function($turma) {
    $baseUrl = '/turmas/' . $turma->id;

    return [
        'cells' => [$turma->id, $turma->nome, $turma->ano],
        'actions' => [
            'showUrl'      => $baseUrl,
            'editId'       => 'editarTurma-' . $turma->id,
            'editFetchUrl' => $baseUrl . '/edit',
            'deleteUrl'    => $baseUrl,
        ],
    ];
}, $pagination['data']);

component('table', [
    'columns'    => ['#', 'Nome', 'Ano'],
    'rows'       => $rows,
    'pagination' => $pagination,
]);
?>

<?php include __DIR__ . '/../layout/footer.php'; ?>