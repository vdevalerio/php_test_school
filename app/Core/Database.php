<?php

namespace App\Core;

use PDO;

class Database {
    private static ?PDO $connection = null;
    private static ?array $config = null;

    public function __construct() {
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

    public function query($query, array $params = []) {
        $statement = self::$connection->prepare($query);
        $statement->execute($params);
        return $statement;
    }
}
