<?php

namespace App\Models;

class Nota extends Model
{
    protected static string $table = 'notas';

    protected array $casts = [
        'id'               => 'int',
        'aluno_id'         => 'int',
        'data_lancamento'  => 'datetime',
    ];
}
