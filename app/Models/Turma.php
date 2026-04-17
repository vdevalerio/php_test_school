<?php

namespace App\Models;

class Turma extends Model
{
    protected static string $table = 'turmas';
    public int $id;
    public string $nome;
    public int $ano;

    public function alunos()
    {
        $instance = new static();
        return $instance->db->query("
            SELECT alunos.*
            FROM alunos
            WHERE alunos.turma_id = ?
        ", [$this->id])->fetchAll();
    }
}
