<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\Nota;

class NotaController
{
    public function index(): Response
    {
        $page           = max(1, (int) ($_GET['page'] ?? 1));
        $perPage        = (int) ($_GET['per_page'] ?? 10);
        $perPageOptions = [10, 25, 50, 100];
        $sort           = $_GET['sort'] ?? 'id';
        $direction      = $_GET['direction'] ?? 'asc';

        $allowedSorts   = [
            'notas.id',
            'alunos.nome',
            'turmas.nome',
            'notas.disciplina',
            'notas.nota',
            'notas.data_lancamento',
        ];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        $pagination = Nota::query()
            ->leftJoin('alunos', 'notas.aluno_id', '=', 'alunos.id')
            ->leftJoin('turmas', 'alunos.turma_id', '=', 'turmas.id')
            ->orderBy($sort, $direction)
            ->paginate($page, $perPage, $perPageOptions);

        return Response::view('notas/index', [
            'pagination' => $pagination,
            'sort'       => $sort,
            'direction'  => $direction,
            'heading'    => 'Notas',
        ]);
    }

    public function create(): Response
    {
        return Response::view('notas/_form', [
            'action'      => '/notas',
            'method'      => 'POST',
            'nota'        => null,
            'submitLabel' => 'Criar nota',
        ]);
    }

    public function store(): Response
    {
        $aluno_id        = trim($_POST['aluno_id'] ?? '');
        $disciplina      = trim($_POST['disciplina'] ?? '');
        $nota            = trim($_POST['nota'] ?? '');
        $data_lancamento = trim($_POST['data_lancamento'] ?? '');

        if (empty($aluno_id) || empty($disciplina) || empty($nota)) {
            return Response::redirect('/notas?error=campos_obrigatorios');
        }

        Nota::create([
            'aluno_id'        => $aluno_id,
            'disciplina'      => $disciplina,
            'nota'            => $nota,
            'data_lancamento' => $data_lancamento ?: date('Y-m-d'),
        ]);

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/notas');
    }

    public function edit($id): Response
    {
        return Response::view('notas/_form', [
            'nota'        => Nota::find($id),
            'action'      => "/notas/$id",
            'method'      => 'PUT',
            'submitLabel' => 'Atualizar nota',
        ]);
    }

    public function update($id): Response
    {
        $aluno_id        = trim($_POST['aluno_id'] ?? '');
        $disciplina      = trim($_POST['disciplina'] ?? '');
        $nota            = trim($_POST['nota'] ?? '');
        $data_lancamento = trim($_POST['data_lancamento'] ?? '');

        if (empty($aluno_id) || empty($disciplina) || empty($nota)) {
            return Response::redirect('/notas?error=campos_obrigatorios');
        }

        Nota::update($id, [
            'aluno_id'        => $aluno_id,
            'disciplina'      => $disciplina,
            'nota'            => $nota,
            'data_lancamento' => $data_lancamento ?: date('Y-m-d'),
        ]);

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/notas');
    }

    public function destroy($id): Response
    {
        Nota::delete($id);

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/notas');
    }
}