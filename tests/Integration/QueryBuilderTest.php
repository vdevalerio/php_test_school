<?php declare(strict_types=1);

namespace Tests\Integration;

use App\Core\QueryBuilder;
use App\Models\Turma;
use Tests\TestCase;

final class QueryBuilderTest extends TestCase
{
    private function make(): QueryBuilder
    {
        return new QueryBuilder('turmas', Turma::class);
    }

    // -------------------------------------------------------------------------
    // count()
    // -------------------------------------------------------------------------

    public function test_count_returns_zero_when_table_is_empty(): void
    {
        $builder = $this->make();

        $this->assertSame(0, $builder->count());
    }

    public function test_count_returns_total_records(): void
    {
        Turma::create(['nome' => 'Test1', 'ano' => 2010]);
        Turma::create(['nome' => 'Test2', 'ano' => 2011]);
        Turma::create(['nome' => 'Test3', 'ano' => 2012]);

        $builder = $this->make();

        $this->assertSame(3, $builder->count());
    }

    // -------------------------------------------------------------------------
    // count() + where()
    // -------------------------------------------------------------------------

    public function test_count_respects_where_clause(): void
    {
        Turma::create(['nome' => 'Test1', 'ano' => 2020]);
        Turma::create(['nome' => 'Test2', 'ano' => 2021]);
        Turma::create(['nome' => 'Test3', 'ano' => 2022]);

        $builder = $this->make();

        $this->assertSame(1, $builder->where('ano', '=', 2020)->count());
    }

    // -------------------------------------------------------------------------
    // get()
    // -------------------------------------------------------------------------

    public function test_get_returns_empty_array_when_table_is_empty(): void
    {
        $builder = $this->make();

        $this->assertSame([], $builder->get());
    }

    public function test_get_returns_model_instances(): void
    {
        Turma::create(['nome' => 'Test1', 'ano' => 2010]);
        Turma::create(['nome' => 'Test2', 'ano' => 2011]);

        $builder = $this->make();
        $results = $builder->get();

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(Turma::class, $results);
    }

    public function test_get_returns_model_instance(): void
    {
        Turma::create(['nome' => 'Test', 'ano' => 2010]);

        $builder = $this->make();
        $result = $builder->get();

        $this->assertInstanceOf(Turma::class, $result[0]);
        $this->assertSame('Test', $result[0]->nome);
        $this->assertSame(2010, $result[0]->ano);
    }

    public function test_get_filters_by_single_where(): void
    {
        Turma::create(['nome' => 'Test1', 'ano' => 2020]);
        Turma::create(['nome' => 'Test2', 'ano' => 2021]);
        Turma::create(['nome' => 'Test3', 'ano' => 2022]);

        $builder = $this->make();
        $results = $builder->where('ano', '=', 2020)->get();

        $this->assertCount(1, $results);
        $this->assertSame('Test1', $results[0]->nome);
        $this->assertSame(2020, $results[0]->ano);
    }

    public function test_get_returns_empty_array_when_where_matches_nothing(): void
    {
        Turma::create(['nome' => 'Test1', 'ano' => 2020]);
        Turma::create(['nome' => 'Test2', 'ano' => 2021]);
        Turma::create(['nome' => 'Test3', 'ano' => 2022]);

        $builder = $this->make();
        $results = $builder->where('ano', '=', 2000)->get();

        $this->assertSame([], $results);
    }
}