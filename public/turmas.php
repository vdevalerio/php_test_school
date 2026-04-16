<?php

$turmas = [
    [
        'nome' => 'Turma 1',
        'ano' => '2025'
    ],
    [
        'nome' => 'Turma 2',
        'ano' => '2026'
    ]
];

$turmasFiltradas = array_filter($turmas, function ($turma) {
    return $turma['ano'] == '2025';
});

$heading = 'Turmas';
require "../app/Views/turmas/turmas.view.php";