<?php

namespace Database\Seeders;

use App\Models\Turma;

class TurmaSeeder
{
    public function run(): void
    {
        $count = 0;
        for ($i = 1; $i <= 10; $i++) {
            Turma::create([
                'nome' => 'Turma ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'ano' => 2010 + $i
            ]);
            $count++;
        }

        echo "TurmaSeeder: " . $count . " records inserted.\n";
    }
}