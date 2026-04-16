<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Turma;

class TurmaController
{
    public function index()
    {
        $turmas = Turma::all();

        $heading = 'Turmas';
        require "../app/Views/turmas/index.php";
    }

    public function create(): void
    {
        $action      = '/turmas';
        $method      = 'POST';
        $turma       = null;
        $submitLabel = 'Criar turma';
        require "../app/Views/turmas/_form.php";
    }

    public function store(): void
    {
        $nome = trim($_POST['nome'] ?? '');
        $ano  = trim($_POST['ano'] ?? '');

        if (empty($nome) || empty($ano)) {
            header('Location: /turmas?error=campos_obrigatorios');
            exit;
        }
        
        Turma::create([
            'nome' => $nome,
            'ano' => $ano
        ]);

        header('Location: /turmas');
        exit;
    }
}