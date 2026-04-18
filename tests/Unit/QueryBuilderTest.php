<?php declare(strict_types=1);

namespace Tests\Unit;

use App\Core\QueryBuilder;
use App\Models\Turma;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class QueryBuilderTest extends TestCase
{
    private function make(): QueryBuilder
    {
        return new QueryBuilder('turmas', Turma::class);
    }

    // -------------------------------------------------------------------------
    // Instantiation
    // -------------------------------------------------------------------------

    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(QueryBuilder::class, $this->make());
    }

    // -------------------------------------------------------------------------
    // where()
    // -------------------------------------------------------------------------

    public function test_where_returns_same_instance(): void
    {
        $builder = $this->make();

        $this->assertSame($builder, $builder->where('ano', '=', 2024));
    }

    public function test_where_stores_bindings_in_order(): void
    {
        $builder = $this->make();
        $builder->where('ano', '=', 2024)->where('nome', 'LIKE', '5%');

        $reflection = new ReflectionClass(QueryBuilder::class);
        $bindings   = $reflection->getProperty('bindings')->getValue($builder);

        $this->assertSame([2024, '5%'], $bindings);
    }

    // -------------------------------------------------------------------------
    // buildWhereClause()
    // -------------------------------------------------------------------------

    public function test_build_where_clause_returns_empty_string_when_no_conditions(): void
    {
        $builder = $this->make();
        $this->assertSame('', $this->callBuildWhereClause($builder));
    }

    public function test_build_where_clause_returns_single_condition(): void
    {
        $builder = $this->make();
        $builder->where('ano', '=', 2024);

        $this->assertSame(
            ' WHERE ano = ?',
            $this->callBuildWhereClause($builder)
        );
    }

    public function test_build_where_clause_joins_coinditions_with_and(): void
    {
        $builder = $this->make();
        $builder->where('ano', '=', 2024)->where('nome', '!=', '6B');

        $this->assertSame(
            ' WHERE ano = ? AND nome != ?',
            $this->callBuildWhereClause($builder)
        );
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function callBuildWhereClause(QueryBuilder $builder): string
    {
        $reflection = new ReflectionClass(QueryBuilder::class);
        $method     = $reflection->getMethod('buildWhereClause');

        return $method->invoke($builder);
    }
}