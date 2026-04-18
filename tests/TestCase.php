<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use App\Core\Database;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->resetDatabaseSingleton();
        $this->truncateTables();
    }

    private function resetDatabaseSingleton(): void
    {
        $reflection = new \ReflectionClass(Database::class);

        $instance = $reflection->getProperty('instance');
        $instance->setValue(null, null);

        $connection = $reflection->getProperty('connection');
        $connection->setValue(null, null);
    }

    private function truncateTables(): void
    {
        $db = Database::getInstance();
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $db->query('TRUNCATE TABLE notas');
        $db->query('TRUNCATE TABLE alunos');
        $db->query('TRUNCATE TABLE turmas');
        $db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}