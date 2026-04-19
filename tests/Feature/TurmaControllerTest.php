<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Controllers\TurmaController;
use App\Models\Aluno;
use App\Models\Nota;
use App\Models\Turma;
use Tests\TestCase;

final class TurmaControllerTest extends TestCase
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

    private function createAluno(int $turmaId): int
    {
        return Aluno::create([
            'nome'     => 'João',
            'email'    => 'joao@example.com',
            'turma_id' => $turmaId,
        ]);
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
        $_GET  = [];
        $_POST = [];
    }

    // -------------------------------------------------------------------------
    // index
    // -------------------------------------------------------------------------

    public function test_index_returns_view_response(): void
    {
        $response = (new TurmaController())->index();

        $this->assertTrue($response->isView());
    }

    public function test_index_returns_correct_view(): void
    {
        $response = (new TurmaController())->index();

        $this->assertSame('turmas/index', $response->getView());
    }

    public function test_index_passes_heading_to_view(): void
    {
        $response = (new TurmaController())->index();

        $this->assertSame('Turmas', $response->getData()['heading']);
    }

    public function test_index_passes_pagination_to_view(): void
    {
        $response = (new TurmaController())->index();

        $this->assertArrayHasKey('pagination', $response->getData());
    }

    public function test_index_passes_turmas_to_view(): void
    {
        $this->createTurma();

        $response   = (new TurmaController())->index();
        $response   = $response->getData();
        $data       = $response['pagination']['data'];
        $turma      = $data[0];

        $this->assertArrayHasKey('pagination', $response);
        $this->assertCount(1, $data);
        $this->assertInstanceOf(Turma::class, $turma);
    }

    public function test_index_defaults_to_page_1(): void
    {
        $response = (new TurmaController())->index();

        $this->assertSame(1, $response->getData()['pagination']['current_page']);
    }

    public function test_index_uses_get_page_parameter(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $this->createTurma(['nome' => "Turma $i", 'ano' => 2024 + $i]);
        }
        $_GET['page'] = 2;

        $response = (new TurmaController())->index();

        $this->assertSame(2, $response->getData()['pagination']['current_page']);
    }

    public function test_index_clamps_invalid_page_to_1(): void
    {
        $_GET['page'] = 0;

        $response = (new TurmaController())->index();

        $this->assertSame(1, $response->getData()['pagination']['current_page']);
    }

    public function test_index_second_page_returns_correct_turma_count(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $this->createTurma(['nome' => "Turma $i", 'ano' => 2024 + $i]);
        }
        $_GET['page'] = 2;

        $response = (new TurmaController())->index();

        $this->assertCount(5, $response->getData()['pagination']['data']);
    }

    // -------------------------------------------------------------------------
    // create
    // -------------------------------------------------------------------------

    public function test_create_returns_view_response(): void
    {
        $response = (new TurmaController())->create();

        $this->assertTrue($response->isView());
    }

    public function test_create_returns_correct_view(): void
    {
        $response = (new TurmaController())->create();

        $this->assertSame('turmas/_form', $response->getView());
    }

    public function test_create_passes_correct_action_to_view(): void
    {
        $response = (new TurmaController())->create();

        $this->assertSame('/turmas', $response->getData()['action']);
    }

    public function test_create_passes_post_method_to_view(): void
    {
        $response = (new TurmaController())->create();

        $this->assertSame('POST', $response->getData()['method']);
    }

    public function test_create_passes_null_turma_to_view(): void
    {
        $response = (new TurmaController())->create();

        $this->assertNull($response->getData()['turma']);
    }

    // -------------------------------------------------------------------------
    // show
    // -------------------------------------------------------------------------

    public function test_show_returns_view_response(): void
    {
        $id       = $this->createTurma();
        $response = (new TurmaController())->show($id);

        $this->assertTrue($response->isView());
    }

    public function test_show_returns_correct_view(): void
    {
        $id       = $this->createTurma();
        $response = (new TurmaController())->show($id);

        $this->assertSame('turmas/show', $response->getView());
    }

    public function test_show_passes_correct_turma_to_view(): void
    {
        $id       = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        $response = (new TurmaController())->show($id);

        $this->assertSame('5A', $response->getData()['turma']->nome);
    }

    public function test_show_passes_heading_to_view(): void
    {
        $id       = $this->createTurma();
        $response = (new TurmaController())->show($id);

        $this->assertSame('Turma', $response->getData()['heading']);
    }

    // -------------------------------------------------------------------------
    // edit
    // -------------------------------------------------------------------------

    public function test_edit_returns_view_response(): void
    {
        $id       = $this->createTurma();
        $response = (new TurmaController())->edit($id);

        $this->assertTrue($response->isView());
    }

    public function test_edit_returns_correct_view(): void
    {
        $id       = $this->createTurma();
        $response = (new TurmaController())->edit($id);

        $this->assertSame('turmas/_form', $response->getView());
    }

    public function test_edit_passes_correct_turma_to_view(): void
    {
        $id       = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        $response = (new TurmaController())->edit($id);

        $this->assertSame('5A', $response->getData()['turma']->nome);
    }

    public function test_edit_passes_correct_action_to_view(): void
    {
        $id       = $this->createTurma();
        $response = (new TurmaController())->edit($id);

        $this->assertSame("/turmas/$id", $response->getData()['action']);
    }

    public function test_edit_passes_put_method_to_view(): void
    {
        $id       = $this->createTurma();
        $response = (new TurmaController())->edit($id);

        $this->assertSame('PUT', $response->getData()['method']);
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_store_creates_turma_in_database(): void
    {
        $_POST = ['nome' => '5A', 'ano' => '2024'];

        (new TurmaController())->store();

        $this->assertCount(1, Turma::all());
    }

    public function test_store_persists_correct_values(): void
    {
        $_POST = ['nome' => '5A', 'ano' => '2024'];

        (new TurmaController())->store();

        $turma = Turma::all()[0];
        $this->assertSame('5A', $turma->nome);
        $this->assertSame(2024, $turma->ano);
    }

    public function test_store_redirects_to_turmas_after_success(): void
    {
        $_POST    = ['nome' => '5A', 'ano' => '2024'];
        $response = (new TurmaController())->store();

        $this->assertSame('/turmas', $response->getRedirectUrl());
    }

    public function test_store_redirects_with_error_when_nome_is_missing(): void
    {
        $_POST    = ['ano' => '2024'];
        $response = (new TurmaController())->store();

        $this->assertSame(
            '/turmas?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_store_redirects_with_error_when_ano_is_missing(): void
    {
        $_POST    = ['nome' => '5A'];
        $response = (new TurmaController())->store();

        $this->assertSame(
            '/turmas?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_store_redirects_with_error_when_nome_is_whitespace(): void
    {
        $_POST    = ['nome' => '   ', 'ano' => '2024'];
        $response = (new TurmaController())->store();

        $this->assertSame(
            '/turmas?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_store_does_not_create_record_on_invalid_data(): void
    {
        $_POST = ['nome' => '', 'ano' => '2024'];

        (new TurmaController())->store();

        $this->assertCount(0, Turma::all());
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_update_changes_turma_in_database(): void
    {
        $id    = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        $_POST = ['nome' => '5B', 'ano' => '2025'];

        (new TurmaController())->update($id);

        $turma = Turma::find($id);
        $this->assertSame('5B', $turma->nome);
    }

    public function test_update_redirects_to_turmas_after_success(): void
    {
        $id       = $this->createTurma();
        $_POST    = ['nome' => '5B', 'ano' => '2025'];
        $response = (new TurmaController())->update($id);

        $this->assertSame('/turmas', $response->getRedirectUrl());
    }

    public function test_update_redirects_with_error_when_nome_is_missing(): void
    {
        $id       = $this->createTurma();
        $_POST    = ['ano' => '2024'];
        $response = (new TurmaController())->update($id);

        $this->assertSame(
            '/turmas?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_update_redirects_with_error_when_ano_is_missing(): void
    {
        $id       = $this->createTurma();
        $_POST    = ['nome' => '5A'];
        $response = (new TurmaController())->update($id);

        $this->assertSame(
            '/turmas?error=campos_obrigatorios',
            $response->getRedirectUrl()
        );
    }

    public function test_update_does_not_change_record_on_invalid_data(): void
    {
        $id    = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        $_POST = ['nome' => '', 'ano' => '2024'];

        (new TurmaController())->update($id);

        $this->assertSame('5A', Turma::find($id)->nome);
    }

    public function test_update_does_not_affect_other_records(): void
    {
        $id1   = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        $id2   = $this->createTurma(['nome' => '6B', 'ano' => 2024]);
        $_POST = ['nome' => '5B', 'ano' => '2025'];

        (new TurmaController())->update($id1);

        $this->assertSame('6B', Turma::find($id2)->nome);
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_destroy_removes_turma(): void
    {
        $id = $this->createTurma();

        (new TurmaController())->destroy($id);

        $this->assertFalse(Turma::find($id));
    }

    public function test_destroy_removes_alunos_of_turma(): void
    {
        $turmaId = $this->createTurma();
        $this->createAluno($turmaId);

        (new TurmaController())->destroy($turmaId);

        $this->assertCount(0, Aluno::all());
    }

    public function test_destroy_removes_notas_of_alunos(): void
    {
        $turmaId = $this->createTurma();
        $alunoId = $this->createAluno($turmaId);
        $this->createNota($alunoId);

        (new TurmaController())->destroy($turmaId);

        $this->assertCount(0, Nota::all());
    }

    public function test_destroy_does_not_remove_other_turmas(): void
    {
        $id1 = $this->createTurma(['nome' => '5A', 'ano' => 2024]);
        $id2 = $this->createTurma(['nome' => '6B', 'ano' => 2024]);

        (new TurmaController())->destroy($id1);

        $this->assertInstanceOf(Turma::class, Turma::find($id2));
    }

    public function test_destroy_redirects_to_turmas(): void
    {
        $id       = $this->createTurma();
        $response = (new TurmaController())->destroy($id);

        $this->assertSame('/turmas', $response->getRedirectUrl());
    }
}