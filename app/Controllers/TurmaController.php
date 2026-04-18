<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\Aluno;
use App\Models\Nota;
use App\Models\Turma;

class TurmaController
{
    public function index(): void
    {
        $turmas     = Turma::all();
        $heading    = 'Turmas';

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
            Response::redirect('/turmas?error=campos_obrigatorios');
        }

        Turma::create([
            'nome' => $nome,
            'ano'  => $ano
        ]);

        Response::redirect('/turmas');
    }

    public function show($id): void
    {
        $turma   = Turma::find($id);
        $heading = 'Turma';

        require "../app/Views/turmas/show.php";
    }

    public function edit($id): void
    {
        $turma       = Turma::find($id);
        $action      = "/turmas/$id";
        $method      = "PUT";
        $submitLabel = 'Atualizar turma';

        require "../app/Views/turmas/_form.php";
    }

    public function update($id): void
    {
        $nome = trim($_POST['nome'] ?? '');
        $ano  = trim($_POST['ano'] ?? '');

        if (empty($nome) || empty($ano)) {
            Response::redirect('/turmas/$id/edit?error=campos_obrigatorios');
        }

        Turma::update($id, [
            'nome'  => $nome,
            'ano'   => $ano
        ]);

        Response::redirect('/turmas');
    }

    public function destroy($id): void
    {
        $alunoIds = Aluno::pluck('id', 'turma_id', $id);
        Nota::deleteWhereIn('aluno_id', $alunoIds);
        Aluno::deleteWhere('turma_id', $id);
        Turma::delete($id);

        Response::redirect('/turmas');
    }
}