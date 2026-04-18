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
    $baseUrl = '/alunos/' . $aluno['id'];

    return [
    'cells' => [
        $aluno['id'],
        $aluno['nome'],
        $aluno['email'],
        $aluno['turma_id']
    ],
    'actions' => [
        'showUrl'      => $baseUrl,
        'editId'       => 'editarAluno-' . $aluno['id'],
        'editFetchUrl' => $baseUrl . '/edit',
        'deleteUrl'    => $baseUrl,
    ]];
}, $alunos);

component('table', [
    'columns' => ['#', 'Nome', 'Email', 'Turma'],
    'rows' => $rows
]);

?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
