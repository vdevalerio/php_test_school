<?php declare(strict_types=1);

namespace Tests\Integration;

use App\Core\Database;
use PDOException;
use PDOStatement;
use Tests\TestCase;

final class DatabaseTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Singleton
    // -------------------------------------------------------------------------

    public function test_get_instance_returns_database_instance(): void
    {
        $this->assertInstanceOf(Database::class, Database::getInstance());
    }

    public function test_get_instance_returns_same_instance_on_repeated_calls(): void
    {
        $db1 = Database::getInstance();
        $db2 = Database::getInstance();

        $this->assertSame($db1, $db2);
    }

    public function test_get_instance_after_reset_creates_new_instance(): void
    {
        $db1 = Database::getInstance();

        $reflection = new \ReflectionClass(Database::class);
        $reflection->getProperty('instance')->setValue(null, null);
        $reflection->getProperty('connection')->setValue(null, null);

        $db2 = Database::getInstance();

        $this->assertNotSame($db1, $db2);
    }

    // -------------------------------------------------------------------------
    // query
    // -------------------------------------------------------------------------

    public function test_query_returns_pdo_statement(): void
    {
        $result = Database::getInstance()->query('SELECT 1');

        $this->assertInstanceOf(PDOStatement::class, $result);
    }

    public function test_query_executes_and_fetches_result(): void
    {
        $result = Database::getInstance()->query('SELECT 1 AS value')->fetch();

        $this->assertSame(['value' => 1], $result);
    }

    public function test_query_with_params_binds_correctly(): void
    {
        $result = Database::getInstance()
            ->query('SELECT ? AS value', [42])
            ->fetch();

        $this->assertSame(['value' => '42'], $result);
    }

    public function test_query_with_multiple_params(): void
    {
        $result = Database::getInstance()
            ->query('SELECT ? AS a, ? AS b', [1, 2])
            ->fetch();

        $this->assertSame(['a' => '1', 'b' => '2'], $result);
    }

    // -------------------------------------------------------------------------
    // Invalid credentials
    // -------------------------------------------------------------------------

    public function test_invalid_credentials_throw_pdo_exception(): void
    {
        $original            = $_ENV['DB_PASSWORD'];
        $_ENV['DB_PASSWORD'] = 'wrong_password';

        $reflection = new \ReflectionClass(Database::class);
        $reflection->getProperty('instance')->setValue(null, null);
        $reflection->getProperty('connection')->setValue(null, null);

        $this->expectException(PDOException::class);

        try {
            Database::getInstance();
        } finally {
            $_ENV['DB_PASSWORD'] = $original;
        }
    }
}