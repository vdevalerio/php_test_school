<?php declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Aluno;
use App\Models\Nota;
use App\Models\Turma;
use Tests\TestCase;

final class NotaTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createTurma(): int
    {
        return Turma::create(['nome' => '5A', 'ano' => 2024]);
    }

    private function createAluno(int $turmaId): int
    {
        return Aluno::create([
            'nome'     => 'João',
            'email'    => 'joao@example.com',
            'turma_id' => $turmaId,
        ]);
    }

    private function createNota(int $alunoId, array $overrides = []): int
    {
        return Nota::create(array_merge([
            'aluno_id'        => $alunoId,
            'disciplina'      => 'Matemática',
            'nota'            => 8.5,
            'data_lancamento' => '2024-03-15',
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // create + find
    // -------------------------------------------------------------------------

    public function test_create_persists_record(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id      = $this->createNota($alunoId);

        $this->assertNotNull($id);
        $this->assertIsInt($id);
    }

    public function test_find_returns_nota_instance(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id      = $this->createNota($alunoId);

        $this->assertInstanceOf(Nota::class, Nota::find($id));
    }

    public function test_find_returns_correct_disciplina(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id      = $this->createNota($alunoId, ['disciplina' => 'Português']);

        $this->assertSame('Português', Nota::find($id)->disciplina);
    }

    public function test_find_returns_false_for_nonexistent_id(): void
    {
        $this->assertFalse(Nota::find(9999));
    }

    // -------------------------------------------------------------------------
    // all
    // -------------------------------------------------------------------------

    public function test_all_returns_empty_array_when_table_is_empty(): void
    {
        $this->assertSame([], Nota::all());
    }

    public function test_all_returns_all_records(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $this->createNota($alunoId);
        $this->createNota($alunoId, ['disciplina' => 'Português']);

        $this->assertCount(2, Nota::all());
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_update_changes_disciplina(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id      = $this->createNota($alunoId);

        Nota::update($id, ['disciplina' => 'Português']);

        $this->assertSame('Português', Nota::find($id)->disciplina);
    }

    public function test_update_changes_nota_value(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id      = $this->createNota($alunoId, ['nota' => 7.0]);

        Nota::update($id, ['nota' => 9.5]);

        $this->assertSame(9.5, (float) Nota::find($id)->nota);
    }

    public function test_update_does_not_affect_other_records(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id1     = $this->createNota($alunoId, ['disciplina' => 'Matemática']);
        $id2     = $this->createNota($alunoId, ['disciplina' => 'Português']);

        Nota::update($id1, ['disciplina' => 'História']);

        $this->assertSame('Português', Nota::find($id2)->disciplina);
    }

    // -------------------------------------------------------------------------
    // delete
    // -------------------------------------------------------------------------

    public function test_delete_removes_record(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id      = $this->createNota($alunoId);

        Nota::delete($id);

        $this->assertFalse(Nota::find($id));
    }

    public function test_delete_does_not_affect_other_records(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id1     = $this->createNota($alunoId, ['disciplina' => 'Matemática']);
        $id2     = $this->createNota($alunoId, ['disciplina' => 'Português']);

        Nota::delete($id1);

        $this->assertInstanceOf(Nota::class, Nota::find($id2));
    }

    // -------------------------------------------------------------------------
    // Casts
    // -------------------------------------------------------------------------

    public function test_find_casts_id_to_int(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id      = $this->createNota($alunoId);
        $nota    = Nota::find($id);

        $this->assertIsInt($nota->id);
    }

    public function test_find_casts_aluno_id_to_int(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id      = $this->createNota($alunoId);
        $nota    = Nota::find($id);

        $this->assertIsInt($nota->aluno_id);
    }

    public function test_find_casts_data_lancamento_to_datetime(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id      = $this->createNota($alunoId, [
            'data_lancamento' => '2024-03-15'
        ]);
        $nota    = Nota::find($id);

        $this->assertInstanceOf(\DateTime::class, $nota->data_lancamento);
        $this->assertSame(
            '2024-03-15',
            $nota->data_lancamento->format('Y-m-d')
        );
    }

    // -------------------------------------------------------------------------
    // deleteWhereIn
    // -------------------------------------------------------------------------

    public function test_delete_where_in_removes_matching_records(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id1     = $this->createNota($alunoId, ['disciplina' => 'Matemática']);
        $id2     = $this->createNota($alunoId, ['disciplina' => 'Português']);
        $id3     = $this->createNota($alunoId, ['disciplina' => 'História']);

        Nota::deleteWhereIn('id', [$id1, $id2]);

        $this->assertFalse(Nota::find($id1));
        $this->assertFalse(Nota::find($id2));
        $this->assertInstanceOf(Nota::class, Nota::find($id3));
    }

    public function test_delete_where_in_does_nothing_when_array_is_empty(): void
    {
        $alunoId = $this->createAluno($this->createTurma());
        $id      = $this->createNota($alunoId);

        Nota::deleteWhereIn('id', []);

        $this->assertInstanceOf(Nota::class, Nota::find($id));
    }
}