<?php

namespace App\Models;

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

    public function notas(): array
    {
        $instance = new static();
        $rows     = $instance->db->query(
            "SELECT notas.* FROM notas WHERE notas.aluno_id = ?",
            [$this->id]
        )->fetchAll();

        return array_map(function (array $data) {
            $obj = new Nota();
            foreach ($data as $key => $value) {
                $obj->$key = $obj->castValue($key, $value);
            }
            return $obj;
        }, $rows);
    }
}
