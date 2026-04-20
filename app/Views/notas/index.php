<?php

use App\Models\Aluno;

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/nav.php';
include __DIR__ . '/../layout/banner.php';

component('modal-trigger', [
    'id'              => 'criarNota',
    'label'           => 'Criar Nota',
    'variant'         => 'primary',
    'fetchUrl'        => '/notas/create',
    'disabled'        => Aluno::count() === 0,
    'disabledMessage' => 'Cadastre um aluno antes de criar notas',
]);

$columns = [
    ['label' => '#',                  'sort' => 'id'],
    ['label' => 'Aluno',              'sort' => 'aluno_nome'],
    ['label' => 'Turma',              'sort' => 'turma_nome'],
    ['label' => 'Disciplina',         'sort' => 'disciplina'],
    ['label' => 'Nota',               'sort' => 'nota'],
    ['label' => 'Média',              'sort' => 'media_aluno'],
    ['label' => 'Data de Lançamento', 'sort' => 'data_lancamento'],
];

if ($grouped) {
    $rows = array_map(function ($nota) {
        $baseUrl = '/notas/' . $nota->id;
        return [
            'nota'  => $nota,
            'cells' => [
                $nota->id,
                $nota->aluno_nome,
                $nota->turma_nome,
                $nota->disciplina,
                $nota->nota,
                [
                    'value' => number_format((float) ($nota->media_aluno ?? 0), 2),
                    'rowspan' => true
                ],
                ['value' => $nota->data_lancamento, 'format' => 'd/m/Y'],
            ],
            'actions' => [
                'editId'       => 'editarNota-' . $nota->id,
                'editFetchUrl' => $baseUrl . '/edit',
                'deleteUrl'    => $baseUrl,
            ],
        ];
    }, $pagination['data']);

    component('notas-table', [
        'columns'    => $columns,
        'rows'       => $rows,
        'pagination' => $pagination,
        'sort'       => $sort,
        'direction'  => $direction,
        'filters'    => $filters,
        'filterFields' => $filterFields,
    ]);
} else {
    $rows = array_map(function ($nota) {
        $baseUrl = '/notas/' . $nota->id;
        return [
            'cells' => [
                $nota->id,
                $nota->aluno_nome,
                $nota->turma_nome,
                $nota->disciplina,
                $nota->nota,
                number_format((float) ($nota->media_aluno ?? 0), 2),
                ['value' => $nota->data_lancamento, 'format' => 'd/m/Y'],
            ],
            'actions' => [
                'editId'       => 'editarNota-' . $nota->id,
                'editFetchUrl' => $baseUrl . '/edit',
                'deleteUrl'    => $baseUrl,
            ],
        ];
    }, $pagination['data']);

    component('table', [
        'columns'    => $columns,
        'rows'       => $rows,
        'pagination' => $pagination,
        'sort'       => $sort,
        'direction'  => $direction,
        'filters'    => $filters,
        'filterFields' => $filterFields,
    ]);
}
?>

<?php include __DIR__ . '/../layout/footer.php'; ?>