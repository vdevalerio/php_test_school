<?php declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Aluno;
use App\Models\Nota;
use App\Models\Turma;
use Tests\TestCase;

final class AlunoTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createTurma(): int
    {
        return Turma::create(['nome' => '5A', 'ano' => 2024]);
    }

    private function createAluno(int $turmaId, array $overrides = []): int
    {
        return Aluno::create(array_merge([
            'nome' => 'João',
            'email' => 'joao@example.com',
            'turma_id' => $turmaId
        ], $overrides));
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
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId);

        $this->assertNotNull($id);
        $this->assertIsInt($id);
    }

    public function test_find_returns_aluno_instance(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId);

        $this->assertInstanceOf(Aluno::class, Aluno::find($id));
    }

    public function test_find_returns_correct_nome(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId, ['nome' => 'Maria']);

        $this->assertSame('Maria', Aluno::find($id)->nome);
    }

    public function test_find_returns_correct_email(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId, [
            'email' => 'maria@example.com'
        ]);

        $this->assertSame('maria@example.com', Aluno::find($id)->email);
    }

    public function test_find_returns_false_for_nonexistent_id(): void
    {
        $this->assertFalse(Aluno::find(9999));
    }

    // -------------------------------------------------------------------------
    // all
    // -------------------------------------------------------------------------

    public function test_all_returns_empty_array_when_table_is_empty(): void
    {
        $this->assertSame([], Aluno::all());
    }

    public function test_all_returns_all_records(): void
    {
        $turmaId = $this->createTurma();
        $this->createAluno($turmaId);
        $this->createAluno($turmaId, [
            'nome' => 'Maria',
            'email' => 'maria@example.com'
        ]);

        $this->assertCount(2, Aluno::all());
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_update_changes_nome(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId);

        Aluno::update($id, ['nome' => 'Maria']);

        $this->assertSame('Maria', Aluno::find($id)->nome);
    }

    public function test_update_changes_email(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId);

        Aluno::update($id, ['email' => 'novo@example.com']);

        $this->assertSame('novo@example.com', Aluno::find($id)->email);
    }

    public function test_update_does_not_affect_other_records(): void
    {
        $turmaId = $this->createTurma();
        $id1     = $this->createAluno($turmaId, [
            'nome' => 'João',
            'email' => 'joao@example.com'
        ]);
        $id2     = $this->createAluno($turmaId, [
            'nome' => 'Maria',
            'email' => 'maria@example.com'
        ]);

        Aluno::update($id1, ['nome' => 'Pedro']);

        $this->assertSame('Maria', Aluno::find($id2)->nome);
    }

    // -------------------------------------------------------------------------
    // delete
    // -------------------------------------------------------------------------

    public function test_delete_removes_record(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId);

        Aluno::delete($id);

        $this->assertFalse(Aluno::find($id));
    }

    public function test_delete_does_not_affect_other_records(): void
    {
        $turmaId = $this->createTurma();
        $id1     = $this->createAluno($turmaId, [
            'nome' => 'João',
            'email' => 'joao@example.com'
        ]);
        $id2     = $this->createAluno($turmaId, [
            'nome' => 'Maria',
            'email' => 'maria@example.com'
        ]);

        Aluno::delete($id1);

        $this->assertInstanceOf(Aluno::class, Aluno::find($id2));
    }

    // -------------------------------------------------------------------------
    // turma()
    // -------------------------------------------------------------------------

    public function test_turma_returns_turma_instance(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId);
        $aluno   = Aluno::find($id);

        $this->assertInstanceOf(Turma::class, $aluno->turma());
    }

    public function test_turma_returns_correct_turma(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId);
        $aluno   = Aluno::find($id);

        $this->assertSame($turmaId, (int) $aluno->turma()->id);
    }

    // -------------------------------------------------------------------------
    // notas()
    // -------------------------------------------------------------------------

    public function test_notas_returns_empty_array_when_no_notas(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId);
        $aluno   = Aluno::find($id);

        $this->assertSame([], $aluno->notas());
    }

    public function test_notas_returns_notas_of_this_aluno(): void
    {
        $turmaId = $this->createTurma();
        $alunoId = $this->createAluno($turmaId);
        $this->createNota($alunoId);
        $this->createNota($alunoId, ['disciplina' => 'Português']);

        $aluno = Aluno::find($alunoId);

        $this->assertCount(2, $aluno->notas());
    }

    public function test_notas_does_not_return_notas_of_other_aluno(): void
    {
        $turmaId  = $this->createTurma();
        $alunoId1 = $this->createAluno($turmaId, [
            'nome' => 'João',
            'email' => 'joao@example.com'
        ]);
        $alunoId2 = $this->createAluno($turmaId, [
            'nome' => 'Maria',
            'email' => 'maria@example.com'
        ]);

        $this->createNota($alunoId1);
        $this->createNota($alunoId2);

        $aluno = Aluno::find($alunoId1);

        $this->assertCount(1, $aluno->notas());
    }

    // -------------------------------------------------------------------------
    // deleteWhere
    // -------------------------------------------------------------------------

    public function test_delete_where_removes_all_matching_records(): void
    {
        $turmaId = $this->createTurma();
        $this->createAluno($turmaId, [
            'nome' => 'João',
            'email' => 'joao@example.com'
        ]);
        $this->createAluno($turmaId, [
            'nome' => 'Maria',
            'email' => 'maria@example.com'
        ]);

        Aluno::deleteWhere('turma_id', $turmaId);

        $this->assertSame([], Aluno::all());
    }

    public function test_delete_where_does_not_affect_other_records(): void
    {
        $turmaId1 = $this->createTurma();
        $turmaId2 = Turma::create(['nome' => '6B', 'ano' => 2024]);

        $this->createAluno($turmaId1, [
            'nome' => 'João',
            'email' => 'joao@example.com'
        ]);
        $this->createAluno($turmaId2, [
            'nome' => 'Maria',
            'email' => 'maria@example.com'
        ]);

        Aluno::deleteWhere('turma_id', $turmaId1);

        $this->assertCount(1, Aluno::all());
    }
}