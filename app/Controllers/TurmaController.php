<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\Aluno;
use App\Models\Nota;
use App\Models\Turma;

class TurmaController
{
    public function index(): Response
    {
        $page           = max(1, (int) ($_GET['page'] ?? 1));
        $perPage        = (int) ($_GET['per_page'] ?? 10);
        $perPageOptions = [10, 25, 50, 100];
        $sort           = $_GET['sort'] ?? 'id';
        $direction      = $_GET['direction'] ?? 'asc';

        $allowedSorts   = ['id', 'nome', 'ano'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        $pagination = Turma::query()
            ->orderBy($sort, $direction)
            ->paginate($page, $perPage, $perPageOptions);


        return Response::view('turmas/index', [
            'pagination' => $pagination,
            'sort'       => $sort,
            'direction'  => $direction,
            'heading'    => 'Turmas',
        ]);
    }

    public function create(): Response
    {
        return Response::view('turmas/_form', [
            'action'      => '/turmas',
            'method'      => 'POST',
            'turma'       => null,
            'submitLabel' => 'Criar turma',
        ]);
    }

    public function store(): Response
    {
        $nome = trim($_POST['nome'] ?? '');
        $ano  = trim($_POST['ano'] ?? '');

        if (empty($nome) || empty($ano)) {
            return Response::redirect('/turmas?error=campos_obrigatorios');
        }

        Turma::create([
            'nome' => $nome,
            'ano'  => $ano
        ]);

        return Response::redirect('/turmas');
    }

    public function show($id): Response
    {
        $page           = max(1, (int) ($_GET['page'] ?? 1));
        $perPage        = (int) ($_GET['per_page'] ?? 10);
        $perPageOptions = [10, 25, 50, 100];
        $sort           = $_GET['sort'] ?? 'id';
        $direction      = $_GET['direction'] ?? 'asc';
        $allowedSorts   = ['id', 'nome', 'email', 'criado_em'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        $turma      = Turma::find($id);
        $pagination = $turma->alunos()
            ->orderBy($sort, $direction)
            ->paginate($page, $perPage, $perPageOptions);

        return Response::view('turmas/show', [
            'turma'      => $turma,
            'pagination' => $pagination,
            'sort'       => $sort,
            'direction'  => $direction,
            'heading'    => 'Turma',
        ]);
    }

    public function edit($id): Response
    {
        return Response::view('turmas/_form', [
            'turma'       => Turma::find($id),
            'action'      => "/turmas/$id",
            'method'      => 'PUT',
            'submitLabel' => 'Atualizar turma',
        ]);
    }

    public function update($id): Response
    {
        $nome = trim($_POST['nome'] ?? '');
        $ano  = trim($_POST['ano'] ?? '');

        if (empty($nome) || empty($ano)) {
            return Response::redirect(
                "/turmas?error=campos_obrigatorios"
            );
        }

        Turma::update($id, [
            'nome'  => $nome,
            'ano'   => $ano
        ]);

        return Response::redirect('/turmas');
    }

    public function destroy($id): Response
    {
        $alunoIds = Aluno::pluck('id', 'turma_id', $id);
        Nota::deleteWhereIn('aluno_id', $alunoIds);
        Aluno::deleteWhere('turma_id', $id);
        Turma::delete($id);

        return Response::redirect('/turmas');
    }
}