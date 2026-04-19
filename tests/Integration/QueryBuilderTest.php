<?php declare(strict_types=1);

namespace Tests\Integration;

use App\Core\QueryBuilder;
use App\Models\Turma;
use PHPUnit\Framework\Attributes\DataProvider;
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

    // -------------------------------------------------------------------------
    // paginate()
    // -------------------------------------------------------------------------

    public function test_paginate_returns_correct_keys(): void
    {
        $builder = $this->make();
        $result = $builder->paginate();

        $this->assertArrayHasKey('data',             $result);
        $this->assertArrayHasKey('total',            $result);
        $this->assertArrayHasKey('per_page',         $result);
        $this->assertArrayHasKey('per_page_options', $result);
        $this->assertArrayHasKey('current_page',     $result);
        $this->assertArrayHasKey('last_page',        $result);
    }

    public function test_paginate_data_contains_model_instances(): void
    {
        Turma::create(['nome' => 'Test1', 'ano' => 2020]);

        $result = $this->make()->paginate();

        $this->assertInstanceOf(Turma::class, $result['data'][0]);
    }

    public function test_paginate_respects_per_page(): void
    {
        Turma::create(['nome' => 'Test1', 'ano' => 2020]);
        Turma::create(['nome' => 'Test2', 'ano' => 2021]);
        Turma::create(['nome' => 'Test3', 'ano' => 2022]);

        $page1 = $this->make()->paginate(1, 2);
        $page2 = $this->make()->paginate(2, 2);

        $this->assertSame(2020, $page1['data'][0]->ano);
        $this->assertSame(2022, $page2['data'][0]->ano);
    }

    public static function lastPageProvider(): array
    {
        $totalTurmas = 10;
        $data = [];
        foreach (range(1, $totalTurmas) as $perPage) {
            $data["perPage={$perPage}"] = [
                $totalTurmas,
                $perPage,
                (int) ceil($totalTurmas / $perPage)
            ];
        }

        return $data;
    }

    #[DataProvider('lastPageProvider')]
    public function test_paginate_calculates_last_page_correctly(
        int $totalTurmas,
        int $perPage,
        int $expectedLastPage
    ): void
    {
        for ($i = 0; $i < $totalTurmas; $i++) {
            Turma::create(['nome' => 'Test' . $i, 'ano' => 2020 + $i]);
        }

        $lastPage = $this->make()->paginate(1, $perPage)['last_page'];
        $this->assertSame($expectedLastPage, $lastPage);
    }

    public function test_paginate_returns_correct_page(): void
    {
        Turma::create(['nome' => 'Test1', 'ano' => 2020]);
        Turma::create(['nome' => 'Test2', 'ano' => 2021]);
        Turma::create(['nome' => 'Test3', 'ano' => 2022]);

        $page1 = $this->make()->paginate(1, 1);
        $page2 = $this->make()->paginate(2, 1);
        $page3 = $this->make()->paginate(3, 1);

        $this->assertSame(2020, $page1['data'][0]->ano);
        $this->assertSame(2021, $page2['data'][0]->ano);
        $this->assertSame(2022, $page3['data'][0]->ano);
    }

    public function test_paginate_returns_empty_data_beyond_last_page(): void
    {
        Turma::create(['nome' => 'Test1', 'ano' => 2020]);
        Turma::create(['nome' => 'Test2', 'ano' => 2021]);

        $page3 = $this->make()->paginate(3, 1);

        $this->assertSame([], $page3['data']);
    }

    //-------------------------------------------------------------------------
    // paginate() with where()
    //-------------------------------------------------------------------------

    public function test_paginate_respects_where_clause(): void
    {
        Turma::create(['nome' => '5A', 'ano' => 2024]);
        Turma::create(['nome' => '6B', 'ano' => 2024]);
        Turma::create(['nome' => '7C', 'ano' => 2025]);

        $result = $this->make()->where('ano', '=', 2024)->paginate(1, 10);

        $this->assertCount(2, $result['data']);
        $this->assertSame(2, $result['total']);
        $this->assertSame(1, $result['last_page']);
    }
}