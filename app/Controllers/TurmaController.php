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
        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $result = Turma::paginate($page, 10);

        return Response::view('turmas/index', [
            'turmas'     => $result['data'],
            'pagination' => $result,
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
        return Response::view('turmas/show', [
            'turma'   => Turma::find($id),
            'heading' => 'Turma',
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