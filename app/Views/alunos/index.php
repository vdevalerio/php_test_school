<?php

use App\Models\Turma;

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/nav.php';
include __DIR__ . '/../layout/banner.php';

component('modal-trigger', [
    'id'              => 'criarAluno',
    'label'           => 'Criar Aluno',
    'variant'         => 'primary',
    'fetchUrl'        => '/alunos/create',
    'disabled'        => Turma::count() === 0,
    'disabledMessage' => 'Cadastre uma turma antes de criar alunos',
]);

$rows = array_map(function($aluno) {
    $baseUrl = '/alunos/' . $aluno->id;

    return [
    'cells' => [
        $aluno->id,
        $aluno->nome,
        $aluno->email,
        $aluno->turma()->nome,
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
    ['label' => '#',         'sort' => 'alunos.id'],
    ['label' => 'Nome',      'sort' => 'alunos.nome'],
    ['label' => 'Email',     'sort' => 'alunos.email'],
    ['label' => 'Turma',     'sort' => 'turmas.nome'],
    ['label' => 'Criado Em', 'sort' => 'alunos.criado_em'],
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
