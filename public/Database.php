<?php

class Database {
    private ?PDO $connection;
    
    public function __construct(array $config) {
        $data_source_name = "mysql:" . http_build_query($config, '', ';');
        $this->connection = new PDO(
            $dsn = $data_source_name,
            $username = $config['user'] ?? '',
            $password = $config['password'] ?? '',
            $options = [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    public function query($query) {
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $statement;
    }
}
