<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Sanitizer;
use App\Core\Validator;
use App\Exceptions\DatabaseException;
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

        $allowedSorts = ['id', 'nome', 'ano'];
        $allowedDirs  = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        if (!in_array($direction, $allowedDirs)) {
            $direction = 'asc';
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
        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'nome' => 'required|string|min_len:2|max_len:100',
            'ano'  => 'required|integer|min:2000|max:2100',
        ])) {
            return Response::redirect(
                '/turmas?error=' . urlencode($validator->firstError())
            );
        }

        try {
            Turma::create([
                'nome' => Sanitizer::string($_POST['nome']),
                'ano'  => Sanitizer::int($_POST['ano']),
            ]);
        } catch (\PDOException $e) {
            throw DatabaseException::fromPDO($e);
        }

        return Response::redirect('/turmas');
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

        $allowedSorts = ['id', 'nome', 'email', 'criado_em'];
        $allowedDirs  = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        if (!in_array($direction, $allowedDirs)) {
            $direction = 'asc';
        }

        $turma = Turma::find($id);
        if (!$turma) {
            http_response_code(404);
            return Response::view('errors/404', []);
        }

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

    public function edit(string $id): Response
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            http_response_code(404);
            return Response::view('errors/404', []);
        }

        $turma = Turma::find($id);
        if (!$turma) {
            http_response_code(404);
            return Response::view('errors/404', []);
        }

        return Response::view('turmas/_form', [
            'turma'       => $turma,
            'action'      => "/turmas/$id",
            'method'      => 'PUT',
            'submitLabel' => 'Atualizar turma',
        ]);
    }

    public function update(string $id): Response
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            return Response::redirect(
                '/turmas?error=' . urlencode('ID inválido.')
            );
        }

        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'nome' => 'required|string|min_len:2|max_len:100',
            'ano'  => 'required|integer|min:2000|max:2100',
        ])) {
            return Response::redirect(
                '/turmas?error=' . urlencode($validator->firstError())
            );
        }

        try {
            Turma::update($id, [
                'nome' => Sanitizer::string($_POST['nome']),
                'ano'  => Sanitizer::int($_POST['ano']),
            ]);
        } catch (\PDOException $e) {
            throw DatabaseException::fromPDO($e);
        }

        return Response::redirect('/turmas');
    }

    public function destroy(string $id): Response
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            return Response::redirect(
                '/turmas?error=' . urlencode('ID inválido.')
            );
        }

        try {
            $alunoIds = Aluno::pluck('id', 'turma_id', $id);
            Nota::deleteWhereIn('aluno_id', $alunoIds);
            Aluno::deleteWhere('turma_id', $id);
            Turma::delete($id);
        } catch (\PDOException $e) {
            throw DatabaseException::fromPDO($e);
        }

        return Response::redirect('/turmas');
    }
}