<?php

namespace App\Models;

use App\Core\QueryBuilder;

class Turma extends Model
{
    protected static string $table = 'turmas';
    public int $id;
    public string $nome;
    public int $ano;

    public function alunos(): QueryBuilder
    {
        return Aluno::query()->where('turma_id', '=', $this->id);
    }
}
