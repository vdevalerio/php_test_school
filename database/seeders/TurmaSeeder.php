<?php

namespace Database\Seeders;

use App\Core\Database;

class TurmaSeeder
{
    public function run(): void
    {
        $db = new Database();

        $turmas = [
            ['nome' => 'Turma 01', 'ano' => 2010],
            ['nome' => 'Turma 02', 'ano' => 2011],
            ['nome' => 'Turma 03', 'ano' => 2012],
            ['nome' => 'Turma 04', 'ano' => 2013],
            ['nome' => 'Turma 05', 'ano' => 2014],
            ['nome' => 'Turma 06', 'ano' => 2015],
            ['nome' => 'Turma 07', 'ano' => 2016],
            ['nome' => 'Turma 08', 'ano' => 2017],
            ['nome' => 'Turma 09', 'ano' => 2018],
            ['nome' => 'Turma 10', 'ano' => 2019],
            ['nome' => 'Turma 11', 'ano' => 2020],
            ['nome' => 'Turma 12', 'ano' => 2021],
            ['nome' => 'Turma 13', 'ano' => 2022],
            ['nome' => 'Turma 14', 'ano' => 2023],
            ['nome' => 'Turma 15', 'ano' => 2024],
            ['nome' => 'Turma 16', 'ano' => 2025],
            ['nome' => 'Turma 17', 'ano' => 2026],
            ['nome' => 'Turma 18', 'ano' => 2027],
            ['nome' => 'Turma 19', 'ano' => 2028],
            ['nome' => 'Turma 20', 'ano' => 2029],
            ['nome' => 'Turma 21', 'ano' => 2030],
        ];

        foreach ($turmas as $turma) {
            $db->query(
                "INSERT INTO turmas (nome, ano) VALUES (?, ?)",
                [$turma['nome'], $turma['ano']]
            );
        }

        echo "TurmaSeeder: " . count($turmas) . " records inserted.\n";
    }
}