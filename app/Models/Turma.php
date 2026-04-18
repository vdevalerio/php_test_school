<?php

namespace App\Models;

class Turma extends Model
{
    protected static string $table = 'turmas';
    public int $id;
    public string $nome;
    public int $ano;

    public function alunos(): array
    {
        $instance = new static();
        $rows     = $instance->db->query(
            "SELECT alunos.* FROM alunos WHERE alunos.turma_id = ?",
            [$this->id]
        )->fetchAll();

        return array_map(function (array $data) {
            $obj = new Aluno();
            foreach ($data as $key => $value) {
                $obj->$key = $obj->castValue($key, $value);
            }
            return $obj;
        }, $rows);
    }
}
