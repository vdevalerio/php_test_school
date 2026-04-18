<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Controllers\AlunoController;
use App\Models\Aluno;
use App\Models\Nota;
use App\Models\Turma;
use Tests\TestCase;

final class AlunoControllerTest extends TestCase
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
            'nome'     => 'João',
            'email'    => 'joao@example.com',
            'turma_id' => $turmaId,
        ], $overrides));
    }

    private function createNota(int $alunoId): int
    {
        return Nota::create([
            'aluno_id'        => $alunoId,
            'disciplina'      => 'Matemática',
            'nota'            => 8.5,
            'data_lancamento' => '2024-03-15',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $_POST                        = [];
        $_SERVER['HTTP_REFERER']      = '/alunos';
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_store_creates_aluno_in_database(): void
    {
        $turmaId = $this->createTurma();
        $_POST   = [
            'nome'     => 'João',
            'email'    => 'joao@example.com',
            'turma_id' => $turmaId
        ];

        (new AlunoController())->store();

        $this->assertCount(1, Aluno::all());
    }

    public function test_store_persists_correct_values(): void
    {
        $turmaId = $this->createTurma();
        $_POST   = [
            'nome'     => 'João',
            'email'    => 'joao@example.com',
            'turma_id' => $turmaId
        ];

        (new AlunoController())->store();

        $aluno = Aluno::all()[0];
        $this->assertSame('João', $aluno->nome);
        $this->assertSame('joao@example.com', $aluno->email);
    }

    public function test_store_redirects_after_success(): void
    {
        $turmaId  = $this->createTurma();
        $_POST    = [
            'nome'     => 'João',
            'email'    => 'joao@example.com',
            'turma_id' => $turmaId
        ];
        $response = (new AlunoController())->store();

        $this->assertSame('/alunos', $response->getRedirectUrl());
    }

    public function test_store_redirects_with_error_when_nome_is_missing(): void
    {
        $turmaId  = $this->createTurma();
        $_POST    = ['email' => 'joao@example.com', 'turma_id' => $turmaId];
        $response = (new AlunoController())->store();

        $this->assertSame(
            '/alunos?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_store_redirects_with_error_when_email_is_missing(): void
    {
        $turmaId  = $this->createTurma();
        $_POST    = ['nome' => 'João', 'turma_id' => $turmaId];
        $response = (new AlunoController())->store();

        $this->assertSame(
            '/alunos?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_store_redirects_with_error_when_turma_id_is_missing(): void
    {
        $_POST    = [
            'nome'  => 'João',
            'email' => 'joao@example.com'
        ];
        $response = (new AlunoController())->store();

        $this->assertSame(
            '/alunos?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_store_does_not_create_record_on_invalid_data(): void
    {
        $_POST = ['nome' => '', 'email' => '', 'turma_id' => ''];

        (new AlunoController())->store();

        $this->assertCount(0, Aluno::all());
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_update_changes_aluno_in_database(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId);
        $_POST   = [
            'nome'     => 'Maria',
            'email'    => 'maria@example.com',
            'turma_id' => $turmaId
        ];

        (new AlunoController())->update($id);

        $this->assertSame('Maria', Aluno::find($id)->nome);
    }

    public function test_update_redirects_after_success(): void
    {
        $turmaId  = $this->createTurma();
        $id       = $this->createAluno($turmaId);
        $_POST    = [
            'nome'     => 'Maria',
            'email'    => 'maria@example.com',
            'turma_id' => $turmaId
        ];
        $response = (new AlunoController())->update($id);

        $this->assertSame('/alunos', $response->getRedirectUrl());
    }

    public function test_update_redirects_with_error_when_fields_are_missing(): void
    {
        $turmaId  = $this->createTurma();
        $id       = $this->createAluno($turmaId);
        $_POST    = [];
        $response = (new AlunoController())->update($id);

        $this->assertSame(
            '/alunos?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_update_does_not_change_record_on_invalid_data(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId, ['nome' => 'João']);
        $_POST   = ['nome' => '', 'email' => '', 'turma_id' => ''];

        (new AlunoController())->update($id);

        $this->assertSame('João', Aluno::find($id)->nome);
    }

    public function test_update_does_not_affect_other_records(): void
    {
        $turmaId = $this->createTurma();
        $id1     = $this->createAluno($turmaId, [
            'nome'  => 'João',
            'email' => 'joao@example.com'
        ]);
        $id2     = $this->createAluno($turmaId, [
            'nome'  => 'Maria',
            'email' => 'maria@example.com'
        ]);
        $_POST   = [
            'nome'     => 'Pedro',
            'email'    => 'pedro@example.com',
            'turma_id' => $turmaId
        ];

        (new AlunoController())->update($id1);

        $this->assertSame('Maria', Aluno::find($id2)->nome);
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_destroy_removes_aluno(): void
    {
        $turmaId = $this->createTurma();
        $id      = $this->createAluno($turmaId);

        (new AlunoController())->destroy($id);

        $this->assertFalse(Aluno::find($id));
    }

    public function test_destroy_removes_notas_of_aluno(): void
    {
        $turmaId = $this->createTurma();
        $alunoId = $this->createAluno($turmaId);
        $this->createNota($alunoId);

        (new AlunoController())->destroy($alunoId);

        $this->assertCount(0, Nota::all());
    }

    public function test_destroy_does_not_remove_other_alunos(): void
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

        (new AlunoController())->destroy($id1);

        $this->assertInstanceOf(Aluno::class, Aluno::find($id2));
    }

    public function test_destroy_does_not_remove_notas_of_other_alunos(): void
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
        $this->createNota($alunoId2);

        (new AlunoController())->destroy($alunoId1);

        $this->assertCount(1, Nota::all());
    }

    public function test_destroy_redirects_after_success(): void
    {
        $turmaId  = $this->createTurma();
        $id       = $this->createAluno($turmaId);
        $response = (new AlunoController())->destroy($id);

        $this->assertSame('/alunos', $response->getRedirectUrl());
    }
}