<?php

namespace App\Models;

class Nota extends Model
{
    protected static string $table = 'notas';
    public int $id;
    public int $aluno_id;
    public string $disciplina;
    public \DateTime $data_lancamento;

    protected array $casts = [
        'id'               => 'int',
        'aluno_id'         => 'int',
        'data_lancamento'  => 'datetime',
    ];
}
