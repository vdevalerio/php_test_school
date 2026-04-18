<?php declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Turma;
use Tests\TestCase;

final class PaginationTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createTurmas(int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            Turma::create(['nome' => "Turma $i", 'ano' => 2024]);
        }
    }

    // -------------------------------------------------------------------------
    // Structure
    // -------------------------------------------------------------------------

    public function test_paginate_returns_array_with_all_required_keys(): void
    {
        $result = Turma::paginate();

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('per_page', $result);
        $this->assertArrayHasKey('current_page', $result);
        $this->assertArrayHasKey('last_page', $result);
    }

    // -------------------------------------------------------------------------
    // Empty table
    // -------------------------------------------------------------------------

    public function test_paginate_on_empty_table_returns_empty_data(): void
    {
        $result = Turma::paginate();

        $this->assertSame([], $result['data']);
    }

    public function test_paginate_on_empty_table_returns_zero_total(): void
    {
        $result = Turma::paginate();

        $this->assertSame(0, $result['total']);
    }

    public function test_paginate_on_empty_table_returns_zero_last_page(): void
    {
        $result = Turma::paginate();

        $this->assertSame(0, $result['last_page']);
    }

    // -------------------------------------------------------------------------
    // Single record
    // -------------------------------------------------------------------------

    public function test_paginate_with_one_record_returns_one_item(): void
    {
        $this->createTurmas(1);

        $result = Turma::paginate();

        $this->assertCount(1, $result['data']);
    }

    public function test_paginate_with_one_record_returns_total_of_1(): void
    {
        $this->createTurmas(1);

        $result = Turma::paginate();

        $this->assertSame(1, $result['total']);
    }

    public function test_paginate_with_one_record_returns_last_page_of_1(): void
    {
        $this->createTurmas(1);

        $result = Turma::paginate();

        $this->assertSame(1, $result['last_page']);
    }

    // -------------------------------------------------------------------------
    // Default parameters
    // -------------------------------------------------------------------------

    public function test_paginate_defaults_to_page_1(): void
    {
        $result = Turma::paginate();

        $this->assertSame(1, $result['current_page']);
    }

    public function test_paginate_defaults_to_per_page_10(): void
    {
        $result = Turma::paginate();

        $this->assertSame(10, $result['per_page']);
    }

    // -------------------------------------------------------------------------
    // Explicit parameters
    // -------------------------------------------------------------------------

    public function test_paginate_returns_correct_current_page(): void
    {
        $result = Turma::paginate(3);

        $this->assertSame(3, $result['current_page']);
    }

    public function test_paginate_returns_correct_per_page(): void
    {
        $result = Turma::paginate(1, 5);

        $this->assertSame(5, $result['per_page']);
    }

    // -------------------------------------------------------------------------
    // last_page calculation
    // -------------------------------------------------------------------------

    public function test_paginate_last_page_rounds_up(): void
    {
        $this->createTurmas(11);

        $result = Turma::paginate(1, 10);

        $this->assertSame(2, $result['last_page']);
    }

    public function test_paginate_last_page_on_exact_multiple(): void
    {
        $this->createTurmas(20);

        $result = Turma::paginate(1, 10);

        $this->assertSame(2, $result['last_page']);
    }

    public function test_paginate_last_page_with_custom_per_page(): void
    {
        $this->createTurmas(10);

        $result = Turma::paginate(1, 3);

        $this->assertSame(4, $result['last_page']); // ceil(10/3) = 4
    }

    // -------------------------------------------------------------------------
    // Offset / page slicing
    // -------------------------------------------------------------------------

    public function test_paginate_first_page_returns_first_records(): void
    {
        $this->createTurmas(15);

        $result = Turma::paginate(1, 10);

        $this->assertCount(10, $result['data']);
    }

    public function test_paginate_second_page_returns_remaining_records(): void
    {
        $this->createTurmas(15);

        $result = Turma::paginate(2, 10);

        $this->assertCount(5, $result['data']);
    }

    public function test_paginate_second_page_total_still_reflects_all_records(): void
    {
        $this->createTurmas(15);

        $result = Turma::paginate(2, 10);

        $this->assertSame(15, $result['total']);
    }

    public function test_paginate_page_beyond_last_returns_empty_data(): void
    {
        $this->createTurmas(5);

        $result = Turma::paginate(3, 10);

        $this->assertSame([], $result['data']);
    }

    public function test_paginate_page_beyond_last_still_returns_correct_total(): void
    {
        $this->createTurmas(5);

        $result = Turma::paginate(3, 10);

        $this->assertSame(5, $result['total']);
    }

    public function test_paginate_custom_per_page_limits_data_count(): void
    {
        $this->createTurmas(10);

        $result = Turma::paginate(1, 3);

        $this->assertCount(3, $result['data']);
    }

    // -------------------------------------------------------------------------
    // Data items are model instances with correct attributes
    // -------------------------------------------------------------------------

    public function test_paginate_data_contains_model_instances(): void
    {
        $this->createTurmas(1);

        $result = Turma::paginate();

        $this->assertInstanceOf(Turma::class, $result['data'][0]);
    }

    public function test_paginate_data_items_have_correct_attributes(): void
    {
        Turma::create(['nome' => 'Turma A', 'ano' => 2025]);

        $result = Turma::paginate();
        $turma  = $result['data'][0];

        $this->assertSame('Turma A', $turma->nome);
    }
}
