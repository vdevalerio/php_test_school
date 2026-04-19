<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\Aluno;
use App\Models\Nota;

class AlunoController
{
    public function index(): Response
    {
        $page           = max(1, (int) ($_GET['page'] ?? 1));
        $perPage        = (int) ($_GET['per_page'] ?? 10);
        $perPageOptions = [10, 25, 50, 100];
        $sort           = $_GET['sort'] ?? 'id';
        $direction      = $_GET['direction'] ?? 'asc';

        $allowedSorts   = ['id', 'nome', 'email', 'turma_id', 'criado_em'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        $pagination = Aluno::query()
            ->orderBy($sort, $direction)
            ->paginate($page, $perPage, $perPageOptions);

        return Response::view('alunos/index', [
            'pagination' => $pagination,
            'sort'       => $sort,
            'direction'  => $direction,
            'heading'    => 'Alunos',
        ]);
    }

    public function create(): Response
    {
        return Response::view('alunos/_form', [
            'action'      => '/alunos',
            'method'      => 'POST',
            'aluno'       => null,
            'submitLabel' => 'Criar aluno',
        ]);
    }

    public function store(): Response
    {
        $nome     = trim($_POST['nome'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $turma_id = trim($_POST['turma_id'] ?? '');

        if (empty($nome) || empty($email) || empty($turma_id)) {
            return Response::redirect('/alunos?error=campos_obrigatorios');
        }

        Aluno::create([
            'nome'      => $nome,
            'email'     => $email,
            'turma_id'  => $turma_id,
            'criado_em' => date('Y-m-d H:i:s'),
        ]);

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/alunos');
    }

    public function show($id): Response
    {
        $page           = max(1, (int) ($_GET['page'] ?? 1));
        $perPage        = (int) ($_GET['per_page'] ?? 10);
        $perPageOptions = [10, 25, 50, 100];
        $sort           = $_GET['sort'] ?? 'id';
        $direction      = $_GET['direction'] ?? 'asc';
        $allowedSorts   = ['id', 'disciplina', 'nota', 'data_lancamento'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        $aluno      = Aluno::find($id);
        $pagination = $aluno->notas()
            ->orderBy($sort, $direction)
            ->paginate($page, $perPage, $perPageOptions);

        return Response::view('alunos/show', [
            'aluno'      => $aluno,
            'pagination' => $pagination,
            'sort'       => $sort,
            'direction'  => $direction,
            'heading'    => 'Aluno',
        ]);
    }

    public function edit($id): Response
    {
        return Response::view('alunos/_form', [
            'aluno'       => Aluno::find($id),
            'action'      => "/alunos/$id",
            'method'      => 'PUT',
            'submitLabel' => 'Atualizar aluno',
        ]);
    }

    public function update($id): Response
    {
        $nome     = trim($_POST['nome'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $turma_id = trim($_POST['turma_id'] ?? '');

        if (empty($nome) || empty($email) || empty($turma_id)) {
            return Response::redirect('/alunos?error=campos_obrigatorios');
        }

        Aluno::update($id, [
            'nome'     => $nome,
            'email'    => $email,
            'turma_id' => $turma_id,
        ]);

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/alunos');
    }

    public function destroy($id): Response
    {
        Nota::deleteWhere('aluno_id', $id);
        Aluno::delete($id);

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/alunos');
    }
}