<?php

namespace App\Models;

class Nota extends Model
{
    protected static string $table = 'notas';
    public int $id;
    public int $aluno_id;
    public string $disciplina;
    public \DateTime $data_lancamento;
}
