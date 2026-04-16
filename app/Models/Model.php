<?php

namespace App\Models;

use App\Core\Database;

abstract class Model
{
    protected static string $table;
    protected Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public static function all(): array
    {
        $instance = new static();
        return $instance->db->query("SELECT * FROM " . static::$table)->fetchAll();
    }

    public static function find(int $id): array|false
    {
        $instance = new static();
        return $instance->db->query(
            "SELECT * FROM " . static::$table . " WHERE id = ?",
            [$id]
        )->fetch();
    }

    public static function create(array $data): void
    {
        $instance = new static();
        $columns  = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $instance->db->query(
            "INSERT INTO " . static::$table . " ($columns) VALUES ($placeholders)",
            array_values($data)
        );
    }

    public static function update(int $id, array $data): void
    {
        $instance = new static();
        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));

        $instance->db->query(
            "UPDATE " . static::$table . " SET $set WHERE id = ?",
            [...array_values($data), $id]
        );
    }

    public static function delete(int $id): void
    {
        $instance = new static();
        $instance->db->query(
            "DELETE FROM " . static::$table . " WHERE id = ?",
            [$id]
        );
    }
}