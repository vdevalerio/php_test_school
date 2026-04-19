<?php

namespace App\Models;

use App\Core\QueryBuilder;

class Aluno extends Model
{
    protected static string $table = 'alunos';
    public int $id;
    public string $nome;
    public string $email;
    public int $turma_id;
    public \DateTime $data_lancamento;

    protected array $casts = [
        'criado_em' => 'datetime',
    ];

    public function turma(): ?Turma
    {
        return Turma::find($this->turma_id);
    }

    public function notas(): QueryBuilder
    {
        return Nota::query()->where('aluno_id', '=', $this->id);
    }
}
