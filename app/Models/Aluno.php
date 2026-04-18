<?php

namespace App\Models;

class Aluno extends Model
{
    protected static string $table = 'alunos';
    public int $id;
    public string $nome;
    public string $email;
    public int $turma_id;

    public function turma(): ?Turma
    {
        return Turma::find($this->turma_id);
    }

    public function notas(): array
    {
        return $this->db->query(
            "SELECT notas.* FROM notas WHERE notas.aluno_id = ?",
            [$this->id]
        )->fetchAll();
    }
}
