<?php

namespace App\Controllers;

use App\Core\Database;

class TurmaController
{
    public function index()
    {
        $db = new Database();

        $turmas = $db->query("SELECT * FROM turmas")->fetchAll();

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

        $db = new Database();
        $db->query(
            "INSERT INTO turmas (nome, ano) VALUES (?, ?)",
            [$nome, $ano]
        );

        header('Location: /turmas');
        exit;
    }
}