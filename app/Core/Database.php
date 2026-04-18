<?php

namespace App\Core;

use PDO;
use PDOStatement;

class Database {
    private static ?PDO $connection = null;
    private static ?array $config = null;
    private static ?self $instance = null;

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        self::$config = $config;

        $data_source_name = "mysql:" . http_build_query(self::$config, '', ';');
        self::$connection = new PDO(
            $data_source_name,
            self::$config['user'] ?? '',
            self::$config['password'] ?? '',
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function query($query, array $params = []): PDOStatement|false
    {
        $statement = self::$connection->prepare($query);
        $statement->execute($params);

        return $statement;
    }
}
