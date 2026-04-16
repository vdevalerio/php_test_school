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
        require "../app/Views/turmas/turmas.view.php";
    }
}