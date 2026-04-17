<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Aluno;
use App\Models\Nota;
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

    public function show($id)
    {
        $turma = Turma::find($id);
        $heading = 'Turma';
        require "../app/Views/turmas/show.php";
    }

    public function edit($id)
    {
        $turma = Turma::find($id);
        $action = "/turmas/$id";
        $method = "PUT";
        $submitLabel = 'Atualizar turma';
        require "../app/Views/turmas/_form.php";
    }

    public function update($id)
    {
        $nome = trim($_POST['nome'] ?? '');
        $ano  = trim($_POST['ano'] ?? '');

        if (empty($nome) || empty($ano)) {
            header("Location: /turmas/$id/edit?error=campos_obrigatorios");
            exit;
        }

        Turma::update($id, [
            'nome' => $nome,
            'ano' => $ano
        ]);

        header("Location: /turmas");
        exit;
    }

    public function destroy($id)
    {
        $alunoIds = Aluno::pluck('id', 'turma_id', $id);
        Nota::deleteWhereIn('aluno_id', $alunoIds);
        Aluno::deleteWhere('turma_id', $id);
        Turma::delete($id);
        header('Location: /turmas');
        exit;
    }
}