<?php

namespace App\Models;

use App\Core\Database;

abstract class Model
{
    protected static string $table;
    protected Database $db;
    protected array $attributes = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public static function pluck(
        string $column,
        string $whereColumn,
        mixed $whereValue
    ): array
    {
        $instance = new static();
        $rows = $instance->db->query(
            "SELECT $column FROM " . static::$table . " WHERE $whereColumn = ?",
            [$whereValue]
        )->fetchAll(\PDO::FETCH_COLUMN);
        return $rows ?: [];
    }

    public static function all(): array
    {
        $instance = new static();
        return $instance->db->query(
            "SELECT * FROM " . static::$table
        )->fetchAll();
    }

    public static function find(int $id): static|false
    {
        $instance = new static();
        $data     = $instance->db->query(
            "SELECT * FROM " . static::$table . " WHERE id = ?",
            [$id]
        )->fetch();
        if (!$data) return false;

        foreach ($data as $key => $value) {
            $instance->$key = $instance->castValue($key, $value);
        }

        return $instance;
    }

    protected function castValue(string $key, mixed $value): mixed
    {
        $casts = $this->casts ?? [];
        $type  = $casts[$key] ?? null;

        if ($value === null || $type === null) {
            return $value;
        }

        return match ($type) {
            'int', 'integer' => (int) $value,
            'float'          => (float) $value,
            'bool', 'boolean'=> (bool) $value,
            'datetime'       => new \DateTime($value),
            default          => $value,
        };
    }

    public static function create(array $data): void
    {
        $instance     = new static();
        $columns      = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql          = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            static::$table,
            $columns,
            $placeholders
        );

        $instance->db->query($sql, array_values($data));
    }

    public static function update(int $id, array $data): void
    {
        $instance = new static();
        $set      = implode(
            ', ',
            array_map(fn($col) => "$col = ?", array_keys($data))
        );

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

    public static function deleteWhere(string $column, mixed $value): void
    {
        $instance = new static();
        $instance->db->query(
            "DELETE FROM " . static::$table . " WHERE $column = ?",
            [$value]
        );
    }

    public static function deleteWhereIn(string $column, array $values): void
    {
        if (empty($values)) return;

        $instance     = new static();
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $sql          = sprintf(
            'DELETE FROM %s WHERE %s IN (%s)',
            static::$table,
            $column,
            $placeholders
        );

        $instance->db->query($sql, $values);
    }
}