<?php

use App\Models\Turma;

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/nav.php';

/**
 * @var Turma $turma
 */

$title = "#{$turma->id} - {$turma->nome} - {$turma->ano}";
?>

<h1><?= $title ?></h1>

<?php
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
    ['label' => 'E-mail',    'sort' => 'email'],
    ['label' => 'Criado em', 'sort' => 'criado_em'],
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
