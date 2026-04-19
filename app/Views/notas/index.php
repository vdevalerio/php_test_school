<?php

use App\Models\Aluno;
use App\Models\Turma;

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/nav.php';
include __DIR__ . '/../layout/banner.php';

component('modal-trigger', [
    'id'       => 'criarNota',
    'label'    => 'Criar Nota',
    'variant'  => 'primary',
    'fetchUrl' => '/notas/create',
]);


$rows = array_map(function($nota) {
    $baseUrl = '/notas/' . $nota->id;

    return [
        'cells'   => [
            $nota->id,
            Aluno::find($nota->aluno_id)->nome,
            Turma::find(Aluno::find($nota->aluno_id)->turma_id)->nome,
            $nota->disciplina,
            $nota->nota,
            ['value' => $nota->data_lancamento, 'format' => 'd/m/Y'],
        ],
        'actions' => [
            'editId'       => 'editarNota-' . $nota->id,
            'editFetchUrl' => $baseUrl . '/edit',
            'deleteUrl'    => $baseUrl,
        ]
    ];
}, $pagination['data']);

$columns = [
    ['label' => '#',                  'sort' => 'id'],
    ['label' => 'Aluno',              'sort' => 'aluno_id'],
    ['label' => 'Turma'],
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

        'Turma',
        'Disciplina',
        'Nota',
        'Data'
    ],
    'rows'       => $rows,
    'pagination' => $pagination,
]);
?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
