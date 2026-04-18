<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\Aluno;
use App\Models\Nota;

class AlunoController
{
    public function index(): void
    {
        $alunos     = Aluno::all();
        $heading    = 'Alunos';

        require "../app/Views/alunos/index.php";
    }

    public function create(): void
    {
        $action      = '/alunos';
        $method      = 'POST';
        $aluno       = null;
        $submitLabel = 'Criar aluno';

        require "../app/Views/alunos/_form.php";
    }

    public function store(): void
    {
        $nome     = trim($_POST['nome'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $turma_id = trim($_POST['turma_id'] ?? '');

        if (empty($nome) || empty($email) || empty($turma_id)) {
            Response::redirect('/alunos?error=campos_obrigatorios');
        }

        Aluno::create([
            'nome'      => $nome,
            'email'     => $email,
            'turma_id'  => $turma_id,
            'criado_em' => date('Y-m-d H:i:s')
        ]);

        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/alunos');
    }

    public function show($id): void
    {
        $aluno   = Aluno::find($id);
        $heading = 'Aluno';

        require "../app/Views/alunos/show.php";
    }

    public function edit($id): void
    {
        $aluno       = Aluno::find($id);
        $action      = "/alunos/$id";
        $method      = "PUT";
        $submitLabel = 'Atualizar aluno';

        require "../app/Views/alunos/_form.php";
    }

    public function update($id): void
    {
        $nome     = trim($_POST['nome'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $turma_id = trim($_POST['turma_id'] ?? '');

        if (empty($nome) || empty($email) || empty($turma_id)) {
            Response::redirect('/alunos');
        }

        Aluno::update($id, [
            'nome'     => $nome,
            'email'    => $email,
            'turma_id' => $turma_id
        ]);

        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/alunos');
    }

    public function destroy($id): void
    {
        Nota::deleteWhere('aluno_id', $id);
        Aluno::delete($id);

        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/alunos');
    }
}