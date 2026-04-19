<?php

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/nav.php';
include __DIR__ . '/../layout/banner.php';

component('modal-trigger', [
    'id'       => 'criarAluno',
    'label'    => 'Criar Aluno',
    'variant'  => 'primary',
    'fetchUrl' => '/alunos/create',
]);

$rows = array_map(function($aluno) {
    $baseUrl = '/alunos/' . $aluno->id;

    return [
    'cells' => [
        $aluno->id,
        $aluno->nome,
        $aluno->email,
        $aluno->turma_id,
        ['value' => $aluno->criado_em, 'format' => 'd/m/Y']
    ],
    'actions' => [
        'showUrl'      => $baseUrl,
        'editId'       => 'editarAluno-' . $aluno->id,
        'editFetchUrl' => $baseUrl . '/edit',
        'deleteUrl'    => $baseUrl,
    ]];
}, $pagination['data']);

$columns = [
    ['label' => '#',         'sort' => 'id'],
    ['label' => 'Nome',      'sort' => 'nome'],
    ['label' => 'Email',     'sort' => 'email'],
    ['label' => 'Turma',     'sort' => 'turma_id'],
    ['label' => 'Criado Em', 'sort' => 'criado_em'],
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
