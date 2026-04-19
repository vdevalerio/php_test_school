<?php

namespace Database\Seeders;

use App\Models\Aluno;
use App\Models\Turma;

class AlunoSeeder
{
    public function run(): void
    {
        $turmas = Turma::all();
        foreach ($turmas as $turma) {
            for ($i = 1; $i <= 30; $i++) {
                Aluno::create([
                    'nome'      => 'Aluno ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'email'     => 'aluno' . $i . '@example.com',
                    'turma_id'  => $turma->id,
                    'criado_em' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        echo "AlunoSeeder: " . (count($turmas) * 30) . " records inserted.\n";
    }
}