<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Sanitizer;
use App\Core\Validator;
use App\Exceptions\DatabaseException;
use App\Models\Aluno;
use App\Models\Nota;

class AlunoController
{
    public function index(): Response
    {
        $page           = max(1, (int) ($_GET['page'] ?? 1));
        $perPage        = (int) ($_GET['per_page'] ?? 10);
        $perPageOptions = [10, 25, 50, 100];
        $sort           = $_GET['sort'] ?? 'alunos.id';
        $direction      = $_GET['direction'] ?? 'asc';

        $allowedSorts = [
            'alunos.id',
            'alunos.nome',
            'alunos.email',
            'turmas.nome',
            'alunos.criado_em',
        ];
        $allowedDirs = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'alunos.id';
        }

        if (!in_array($direction, $allowedDirs)) {
            $direction = 'asc';
        }

        $pagination = Aluno::query()
            ->select('alunos.*', 'turmas.nome as turma_nome')
            ->leftJoin('turmas', 'alunos.turma_id', '=', 'turmas.id')
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
        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'nome'     => 'required|string|min_len:2|max_len:100',
            'email'    => 'required|email|max_len:150',
            'turma_id' => 'required|integer',
        ])) {
            return Response::redirect('/alunos?error='
                . urlencode($validator->firstError()));
        }

        try {
            Aluno::create([
                'nome'      => Sanitizer::string($_POST['nome']),
                'email'     => Sanitizer::email($_POST['email']),
                'turma_id'  => Sanitizer::int($_POST['turma_id']),
                'criado_em' => date('Y-m-d H:i:s'),
            ]);
        } catch (\PDOException $e) {
            throw DatabaseException::fromPDO($e);
        }

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/alunos');
    }

    public function show(string $id): Response
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            http_response_code(404);
            return Response::view('errors/404', []);
        }

        $page           = max(1, (int) ($_GET['page'] ?? 1));
        $perPage        = (int) ($_GET['per_page'] ?? 10);
        $perPageOptions = [10, 25, 50, 100];
        $sort           = $_GET['sort'] ?? 'id';
        $direction      = $_GET['direction'] ?? 'asc';

        $allowedSorts = ['id', 'disciplina', 'nota', 'data_lancamento'];
        $allowedDirs  = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        if (!in_array($direction, $allowedDirs)) {
            $direction = 'asc';
        }

        $aluno = Aluno::find($id);
        if (!$aluno) {
            http_response_code(404);
            return Response::view('errors/404', []);
        }

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

    public function edit(string $id): Response
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            http_response_code(404);
            return Response::view('errors/404', []);
        }

        $aluno = Aluno::find($id);
        if (!$aluno) {
            http_response_code(404);
            return Response::view('errors/404', []);
        }

        return Response::view('alunos/_form', [
            'aluno'       => $aluno,
            'action'      => "/alunos/$id",
            'method'      => 'PUT',
            'submitLabel' => 'Atualizar aluno',
        ]);
    }

    public function update(string $id): Response
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            return Response::redirect(
                '/alunos?error=' . urlencode('ID inválido.')
            );
        }

        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'nome'     => 'required|string|min_len:2|max_len:100',
            'email'    => 'required|email|max_len:150',
            'turma_id' => 'required|integer',
        ])) {
            return Response::redirect(
                '/alunos?error=' . urlencode($validator->firstError())
            );
        }

        try {
            Aluno::update($id, [
                'nome'     => Sanitizer::string($_POST['nome']),
                'email'    => Sanitizer::email($_POST['email']),
                'turma_id' => Sanitizer::int($_POST['turma_id']),
            ]);
        } catch (\PDOException $e) {
            throw DatabaseException::fromPDO($e);
        }

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/alunos');
    }

    public function destroy(string $id): Response
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            return Response::redirect(
                '/alunos?error=' . urlencode('ID inválido.')
            );
        }

        try {
            Nota::deleteWhere('aluno_id', $id);
            Aluno::delete($id);
        } catch (\PDOException $e) {
            throw DatabaseException::fromPDO($e);
        }

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/alunos');
    }
}