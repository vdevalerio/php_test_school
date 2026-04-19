<?php declare(strict_types=1);

namespace Tests\Unit;

use App\Core\QueryBuilder;
use App\Models\Model;
use PHPUnit\Framework\TestCase;

class StubModel extends Model
{
    protected static string $table = 'stub';

    public function castPublic(string $key, mixed $value): mixed
    {
        return $this->castValue($key, $value);
    }
}

class CastStubModel extends StubModel
{
    protected array $casts = [
        'age'        => 'int',
        'score'      => 'float',
        'active'     => 'bool',
        'created_at' => 'datetime',
        'unknown'    => 'unknown_type',
    ];
}

final class ModelTest extends TestCase
{
    private StubModel $model;

    protected function setUp(): void
    {
        $this->model = new StubModel();
    }

    // -------------------------------------------------------------------------
    // __get
    // -------------------------------------------------------------------------

    public function test_get_returns_null_for_unset_attribute(): void
    {
        $this->assertNull($this->model->nonExistent);
    }

    public function test_get_returns_set_attribute(): void
    {
        $this->model->nome = 'João';

        $this->assertSame('João', $this->model->nome);
    }

    public function test_get_returns_null_for_other_attributes_after_setting_one(): void
    {
        $this->model->nome = 'João';

        $this->assertNull($this->model->email);
    }

    // -------------------------------------------------------------------------
    // __set
    // -------------------------------------------------------------------------

    public function test_set_stores_string_value(): void
    {
        $this->model->nome = 'Maria';

        $this->assertSame('Maria', $this->model->nome);
    }

    public function test_set_stores_integer_value(): void
    {
        $this->model->age = 25;

        $this->assertSame(25, $this->model->age);
    }

    public function test_set_stores_null_value(): void
    {
        $this->model->nome = null;

        $this->assertNull($this->model->nome);
    }

    public function test_set_overwrites_existing_attribute(): void
    {
        $this->model->nome = 'João';
        $this->model->nome = 'Maria';

        $this->assertSame('Maria', $this->model->nome);
    }

    public function test_multiple_attributes_are_stored_independently(): void
    {
        $this->model->nome  = 'João';
        $this->model->email = 'joao@example.com';
        $this->model->age   = 20;

        $this->assertSame('João', $this->model->nome);
        $this->assertSame('joao@example.com', $this->model->email);
        $this->assertSame(20, $this->model->age);
    }

    // -------------------------------------------------------------------------
    // __isset
    // -------------------------------------------------------------------------

    public function test_isset_returns_false_for_unset_attribute(): void
    {
        $this->assertFalse(isset($this->model->nome));
    }

    public function test_isset_returns_true_for_set_attribute(): void
    {
        $this->model->nome = 'João';

        $this->assertTrue(isset($this->model->nome));
    }

    public function test_isset_returns_false_when_value_is_null(): void
    {
        $this->model->nome = null;

        $this->assertFalse(isset($this->model->nome));
    }

    // -------------------------------------------------------------------------
    // castValue — no casts defined
    // -------------------------------------------------------------------------

    public function test_cast_returns_value_unchanged_when_no_cast_defined(): void
    {
        $this->assertSame('42', $this->model->castPublic('any_field', '42'));
    }

    public function test_cast_returns_null_unchanged_regardless_of_cast(): void
    {
        $model = new CastStubModel();

        $this->assertNull($model->castPublic('age', null));
    }

    // -------------------------------------------------------------------------
    // castValue — int
    // -------------------------------------------------------------------------

    public static function intCastProvider(): array
    {
        return [
            'from string' => ['42',  42],
            'from float'  => [42.9,  42],
            'from bool'   => [true,   1],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('intCastProvider')]
    public function test_cast_converts_to_int(mixed $input, int $expected): void
    {
        $model  = new CastStubModel();
        $result = $model->castPublic('age', $input);

        $this->assertSame($expected, $result);
        $this->assertIsInt($result);
    }

    // -------------------------------------------------------------------------
    // castValue — float
    // -------------------------------------------------------------------------

    public function test_cast_converts_string_to_float(): void
    {
        $model  = new CastStubModel();
        $result = $model->castPublic('score', '8.5');

        $this->assertSame(8.5, $result);
        $this->assertIsFloat($result);
    }

    // -------------------------------------------------------------------------
    // castValue — bool
    // -------------------------------------------------------------------------

    public static function boolCastProvider(): array
    {
        return [
            'truthy string' => ['1',  true],
            'falsy string'  => ['0',  false],
            'truthy int'    => [1,    true],
            'falsy int'     => [0,    false],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('boolCastProvider')]
    public function test_cast_converts_to_bool(mixed $input, bool $expected): void
    {
        $model = new CastStubModel();

        $this->assertSame($expected, $model->castPublic('active', $input));
    }

    // -------------------------------------------------------------------------
    // castValue — datetime
    // -------------------------------------------------------------------------

    public function test_cast_converts_string_to_datetime(): void
    {
        $model  = new CastStubModel();
        $result = $model->castPublic('created_at', '2024-03-15');

        $this->assertInstanceOf(\DateTime::class, $result);
        $this->assertSame('2024-03-15', $result->format('Y-m-d'));
    }

    public function test_cast_datetime_preserves_time(): void
    {
        $model  = new CastStubModel();
        $result = $model->castPublic('created_at', '2024-03-15 14:30:00');

        $this->assertSame('2024-03-15 14:30:00', $result->format('Y-m-d H:i:s'));
    }

    // -------------------------------------------------------------------------
    // castValue — unknown type
    // -------------------------------------------------------------------------

    public function test_cast_unknown_type_returns_value_unchanged(): void
    {
        $model = new CastStubModel();

        $this->assertSame('valor', $model->castPublic('unknown', 'valor'));
    }

    // -------------------------------------------------------------------------
    // cast() — public proxy for castValue
    // -------------------------------------------------------------------------

    public function test_cast_public_proxy_delegates_to_cast_value(): void
    {
        $model = new CastStubModel();

        $this->assertSame(42, $model->cast('age', '42'));
    }

    // -------------------------------------------------------------------------
    // query()
    // -------------------------------------------------------------------------

    public function test_query_returns_query_builder_instance(): void
    {
        $this->assertInstanceOf(QueryBuilder::class, StubModel::query());
    }
}