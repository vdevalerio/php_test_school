<?php

class Database {
    private ?PDO $connection;
    
    public function __construct(array $envs) {
        $data_source_name = sprintf(
            "mysql:host=%s;port=%s;user=%s;password=%s;dbname=%s;charset=utf8mb4", 
            $envs['DB_HOST'], 
            $envs['DB_PORT'], 
            $envs['DB_USERNAME'], 
            $envs['DB_PASSWORD'], 
            $envs['DB_DATABASE']
        );
        $this->connection = new PDO($data_source_name);
    }

    public function query($query) {
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $statement;
        
    }
}
