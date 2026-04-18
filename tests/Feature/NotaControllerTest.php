<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Controllers\NotaController;
use App\Models\Aluno;
use App\Models\Nota;
use App\Models\Turma;
use Tests\TestCase;

final class NotaControllerTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createAluno(): int
    {
        $turmaId = Turma::create(['nome' => '5A', 'ano' => 2024]);

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

    protected function setUp(): void
    {
        parent::setUp();
        $_POST                   = [];
        $_SERVER['HTTP_REFERER'] = '/notas';
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_store_creates_nota_in_database(): void
    {
        $alunoId = $this->createAluno();
        $_POST   = [
            'aluno_id' => $alunoId,
            'disciplina' => 'Matemática',
            'nota' => '8.5'
        ];

        (new NotaController())->store();

        $this->assertCount(1, Nota::all());
    }

    public function test_store_persists_correct_values(): void
    {
        $alunoId = $this->createAluno();
        $_POST   = [
            'aluno_id'   => $alunoId,
            'disciplina' => 'Matemática',
            'nota'       => '8.5'
        ];

        (new NotaController())->store();

        $nota = Nota::all()[0];
        $this->assertSame('Matemática', $nota->disciplina);
        $this->assertSame(8.5, (float) $nota->nota);
    }

    public function test_store_uses_today_when_data_lancamento_is_empty(): void
    {
        $alunoId = $this->createAluno();
        $_POST   = [
            'aluno_id'   => $alunoId,
            'disciplina' => 'Matemática',
            'nota'       => '8.5'
        ];

        (new NotaController())->store();

        $nota = Nota::all()[0];
        $this->assertInstanceOf(\DateTime::class, $nota->data_lancamento);
        $this->assertSame(date('Y-m-d'), $nota->data_lancamento->format('Y-m-d'));
    }

    public function test_store_uses_provided_data_lancamento(): void
    {
        $alunoId = $this->createAluno();
        $_POST   = [
            'aluno_id'        => $alunoId,
            'disciplina'      => 'Matemática',
            'nota'            => '8.5',
            'data_lancamento' => '2024-03-15',
        ];

        (new NotaController())->store();

        $nota = Nota::all()[0];
        $this->assertInstanceOf(\DateTime::class, $nota->data_lancamento);
        $this->assertSame('2024-03-15', $nota->data_lancamento->format('Y-m-d'));
    }

    public function test_store_redirects_after_success(): void
    {
        $alunoId  = $this->createAluno();
        $_POST    = [
            'aluno_id'   => $alunoId,
            'disciplina' => 'Matemática',
            'nota'       => '8.5'
        ];
        $response = (new NotaController())->store();

        $this->assertSame('/notas', $response->getRedirectUrl());
    }

    public function test_store_redirects_with_error_when_aluno_id_is_missing(): void
    {
        $_POST    = ['disciplina' => 'Matemática', 'nota' => '8.5'];
        $response = (new NotaController())->store();

        $this->assertSame(
            '/notas?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_store_redirects_with_error_when_disciplina_is_missing(): void
    {
        $alunoId  = $this->createAluno();
        $_POST    = ['aluno_id' => $alunoId, 'nota' => '8.5'];
        $response = (new NotaController())->store();

        $this->assertSame(
            '/notas?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_store_redirects_with_error_when_nota_is_missing(): void
    {
        $alunoId  = $this->createAluno();
        $_POST    = ['aluno_id' => $alunoId, 'disciplina' => 'Matemática'];
        $response = (new NotaController())->store();

        $this->assertSame(
            '/notas?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_store_does_not_create_record_on_invalid_data(): void
    {
        $_POST = ['aluno_id' => '', 'disciplina' => '', 'nota' => ''];

        (new NotaController())->store();

        $this->assertCount(0, Nota::all());
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_update_changes_nota_in_database(): void
    {
        $alunoId = $this->createAluno();
        $id      = $this->createNota($alunoId);
        $_POST   = [
            'aluno_id'   => $alunoId,
            'disciplina' => 'Português',
            'nota'       => '9.0'
        ];

        (new NotaController())->update($id);

        $this->assertSame('Português', Nota::find($id)->disciplina);
    }

    public function test_update_redirects_after_success(): void
    {
        $alunoId  = $this->createAluno();
        $id       = $this->createNota($alunoId);
        $_POST    = [
            'aluno_id'   => $alunoId,
            'disciplina' => 'Português',
            'nota'       => '9.0'
        ];
        $response = (new NotaController())->update($id);

        $this->assertSame('/notas', $response->getRedirectUrl());
    }

    public function test_update_redirects_with_error_when_fields_are_missing(): void
    {
        $alunoId  = $this->createAluno();
        $id       = $this->createNota($alunoId);
        $_POST    = [];
        $response = (new NotaController())->update($id);

        $this->assertSame(
            '/notas?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_update_does_not_change_record_on_invalid_data(): void
    {
        $alunoId = $this->createAluno();
        $id      = $this->createNota($alunoId, ['disciplina' => 'Matemática']);
        $_POST   = [
            'aluno_id'   => '',
            'disciplina' => '',
            'nota'       => ''
        ];

        (new NotaController())->update($id);

        $this->assertSame('Matemática', Nota::find($id)->disciplina);
    }

    public function test_update_does_not_affect_other_records(): void
    {
        $alunoId = $this->createAluno();
        $id1     = $this->createNota($alunoId, ['disciplina' => 'Matemática']);
        $id2     = $this->createNota($alunoId, ['disciplina' => 'Português']);
        $_POST   = [
            'aluno_id'   => $alunoId,
            'disciplina' => 'História',
            'nota'       => '7.0'
        ];

        (new NotaController())->update($id1);

        $this->assertSame('Português', Nota::find($id2)->disciplina);
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_destroy_removes_nota(): void
    {
        $alunoId = $this->createAluno();
        $id      = $this->createNota($alunoId);

        (new NotaController())->destroy($id);

        $this->assertFalse(Nota::find($id));
    }

    public function test_destroy_does_not_remove_other_notas(): void
    {
        $alunoId = $this->createAluno();
        $id1     = $this->createNota($alunoId, ['disciplina' => 'Matemática']);
        $id2     = $this->createNota($alunoId, ['disciplina' => 'Português']);

        (new NotaController())->destroy($id1);

        $this->assertInstanceOf(Nota::class, Nota::find($id2));
    }

    public function test_destroy_redirects_after_success(): void
    {
        $alunoId  = $this->createAluno();
        $id       = $this->createNota($alunoId);
        $response = (new NotaController())->destroy($id);

        $this->assertSame('/notas', $response->getRedirectUrl());
    }
}