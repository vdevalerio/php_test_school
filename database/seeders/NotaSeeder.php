<?php

namespace Database\Seeders;

use App\Models\Aluno;
use App\Models\Nota;

class NotaSeeder
{
    public function run(): void
    {
        $disciplinas = [
            'Matemática',
            'Português',
            'Ciências',
            'História',
            'Geografia'
        ];
        $alunos = Aluno::all();
        foreach ($alunos as $aluno) {
            for ($i = 1; $i <= 3; $i++) {
                $randomTimestamp  = rand(
                    strtotime('2020-01-01'),
                    strtotime('2024-12-31')
                );

                Nota::create([
                    'aluno_id'        => $aluno->id,
                    'disciplina'      => $disciplinas[array_rand($disciplinas)],
                    'nota'            => rand(0, 10),
                    'data_lancamento' => date('Y-m-d', $randomTimestamp),
                ]);
            }
        }

        echo "NotaSeeder: " . (count($alunos) * 3) . " records inserted.\n";
    }
}