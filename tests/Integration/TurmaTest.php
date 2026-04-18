<?php declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Aluno;
use App\Models\Turma;
use Tests\TestCase;

final class TurmaTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createTurma(array $overrides = []): int
    {
        return Turma::create(array_merge(
            ['nome' => '5A', 'ano' => 2024],
            $overrides
        ));
    }

    private function createAluno(int $turmaId, array $overrides = []): int
    {
        return Aluno::create(array_merge([
            'nome' => 'João',
            'email' => 'joao@example.com',
            'turma_id' => $turmaId
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // create + find
    // -------------------------------------------------------------------------

    public function test_create_persists_record(): void
    {
        $id = $this->createTurma();

        $this->assertNotNull($id);
        $this->assertIsInt($id);
    }

    public function test_find_returns_turma_instance(): void
    {
        $id    = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        $turma = Turma::find($id);

        $this->assertInstanceOf(Turma::class, $turma);
    }

    public function test_find_returns_correct_nome(): void
    {
        $id = $this->createTurma(['nome' => '5A', 'ano' => 2024]);

        $this->assertSame('5A', Turma::find($id)->nome);
    }

    public function test_find_returns_correct_ano(): void
    {
        $id = $this->createTurma(['nome' => '5A', 'ano' => 2024]);

        $this->assertSame(2024, (int) Turma::find($id)->ano);
    }

    public function test_find_returns_false_for_nonexistent_id(): void
    {
        $this->assertFalse(Turma::find(9999));
    }

    // -------------------------------------------------------------------------
    // all
    // -------------------------------------------------------------------------

    public function test_all_returns_empty_array_when_table_is_empty(): void
    {
        $this->assertSame([], Turma::all());
    }

    public function test_all_returns_all_records(): void
    {
        $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        $this->createTurma(['nome' => '6B', 'ano' => 2024]);

        $this->assertCount(2, Turma::all());
    }

    public function test_all_returns_array(): void
    {
        $this->createTurma();

        $this->assertIsArray(Turma::all());
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_update_changes_nome(): void
    {
        $id = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        Turma::update($id, ['nome' => '5B']);

        $this->assertSame('5B', Turma::find($id)->nome);
    }

    public function test_update_changes_ano(): void
    {
        $id = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        Turma::update($id, ['ano' => 2025]);

        $this->assertSame(2025, (int) Turma::find($id)->ano);
    }

    public function test_update_does_not_affect_other_records(): void
    {
        $id1 = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        $id2 = $this->createTurma(['nome' => '6B', 'ano' => 2024]);

        Turma::update($id1, ['nome' => '5B']);

        $this->assertSame('6B', Turma::find($id2)->nome);
    }

    // -------------------------------------------------------------------------
    // delete
    // -------------------------------------------------------------------------

    public function test_delete_removes_record(): void
    {
        $id = $this->createTurma();
        Turma::delete($id);

        $this->assertFalse(Turma::find($id));
    }

    public function test_delete_does_not_affect_other_records(): void
    {
        $id1 = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        $id2 = $this->createTurma(['nome' => '6B', 'ano' => 2024]);

        Turma::delete($id1);

        $this->assertInstanceOf(Turma::class, Turma::find($id2));
    }

    public function test_delete_nonexistent_id_does_not_throw(): void
    {
        Turma::delete(9999);

        $this->assertTrue(true);
    }

    // -------------------------------------------------------------------------
    // alunos()
    // -------------------------------------------------------------------------

    public function test_alunos_returns_empty_array_when_no_alunos(): void
    {
        $id    = $this->createTurma();
        $turma = Turma::find($id);

        $this->assertSame([], $turma->alunos());
    }

    public function test_alunos_returns_alunos_of_this_turma(): void
    {
        $turmaId = $this->createTurma();
        $this->createAluno($turmaId);
        $this->createAluno($turmaId, [
            'nome' => 'Maria',
            'email' => 'maria@example.com'
        ]);

        $turma = Turma::find($turmaId);

        $this->assertCount(2, $turma->alunos());
    }

    public function test_alunos_does_not_return_alunos_of_other_turma(): void
    {
        $turmaId1 = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        $turmaId2 = $this->createTurma(['nome' => '6B', 'ano' => 2024]);

        $this->createAluno($turmaId1);
        $this->createAluno($turmaId2, [
            'nome' => 'Maria',
            'email' => 'maria@example.com'
        ]);

        $turma = Turma::find($turmaId1);

        $this->assertCount(1, $turma->alunos());
    }
}