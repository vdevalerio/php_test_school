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
}, $notas);

component('table', [
    'columns'    => [
        '#',
        'Aluno',
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
