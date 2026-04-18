<?php declare(strict_types=1);

namespace Tests\Unit;

use App\Core\QueryBuilder;
use App\Models\Turma;
use PHPUnit\Framework\TestCase;

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
}