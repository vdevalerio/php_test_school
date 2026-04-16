<?php

namespace Database\Seeders;

use App\Core\Database;
use App\Models\Turma;

class TurmaSeeder
{
    public function run(): void
    {
        $db = new Database();
        $turmas = [];

        for ($i = 1; $i <= 10; $i++) {
            $turmas[] = [
                'nome' => 'Turma ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'ano' => 2010 + $i
            ];
        }

        foreach ($turmas as $turma) {
            Turma::create($turma);
        }

        echo "TurmaSeeder: " . count($turmas) . " records inserted.\n";
    }
}