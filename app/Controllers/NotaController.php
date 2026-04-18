<?php

namespace App\Controllers;

use App\Models\Nota;

class NotaController
{
    public function index(): void
    {
        $notas     = Nota::all();
        $heading   = 'Notas';

        require "../app/Views/notas/index.php";
    }

    public function create(): void
    {
        $action      = '/notas';
        $method      = 'POST';
        $nota        = null;
        $submitLabel = 'Criar nota';

        require "../app/Views/notas/_form.php";
    }

    public function store(): void
    {
        $aluno_id        = trim($_POST['aluno_id'] ?? '');
        $disciplina      = trim($_POST['disciplina'] ?? '');
        $nota            = trim($_POST['nota'] ?? '');
        $data_lancamento = trim($_POST['data_lancamento'] ?? '');

        if (empty($aluno_id) || empty($disciplina) || empty($nota)) {
            header('Location: /notas?error=campos_obrigatorios');
            exit;
        }

        Nota::create([
            'aluno_id'        => $aluno_id,
            'disciplina'      => $disciplina,
            'nota'            => $nota,
            'data_lancamento' => $data_lancamento ?: date('Y-m-d'),
        ]);

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/notas'));
        exit;
    }

    public function edit($id): void
    {
        $nota        = Nota::find($id);
        $action      = "/notas/$id";
        $method      = "PUT";
        $submitLabel = 'Atualizar nota';

        require "../app/Views/notas/_form.php";
    }

    public function update($id): void
    {
        $aluno_id        = trim($_POST['aluno_id'] ?? '');
        $disciplina      = trim($_POST['disciplina'] ?? '');
        $nota            = trim($_POST['nota'] ?? '');
        $data_lancamento = trim($_POST['data_lancamento'] ?? '');

        if (empty($aluno_id) || empty($disciplina) || empty($nota)) {
            header("Location: /notas/$id/edit?error=campos_obrigatorios");
            exit;
        }

        Nota::update($id, [
            'aluno_id'        => $aluno_id,
            'disciplina'      => $disciplina,
            'nota'            => $nota,
            'data_lancamento' => $data_lancamento ?: date('Y-m-d'),
        ]);

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/notas'));
        exit;
    }

    public function destroy($id): void
    {
        Nota::delete($id);

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/notas'));
        exit;
    }
}