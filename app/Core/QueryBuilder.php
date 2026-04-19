<?php

namespace App\Core;

class QueryBuilder
{
    private array $wheres   = [];
    private array $bindings = [];
    private Database $db;

    public function __construct(
        private string $table,
        private string $modelClass
    ) {
        $this->db = Database::getInstance();
    }

    private function buildWhereClause(): string
    {
        if (empty($this->wheres)) {
            return '';
        }

        return ' WHERE ' . implode(' AND ', $this->wheres);
    }

    public function where(string $column, string $operator, mixed $value): static
    {
        $this->wheres[]   = "{$column} {$operator} ?";
        $this->bindings[] = $value;

        return $this;
    }

    public function count(): int
    {
        $sql    = "SELECT COUNT(*) FROM {$this->table}";
        $sql   .= $this->buildWhereClause();
        $result = $this->db->query($sql, $this->bindings)->fetch();

        return (int) reset($result);
    }

    public function get(): array
    {
        $sql  = "SELECT * FROM {$this->table}" . $this->buildWhereClause();
        $rows = $this->db->query($sql, $this->bindings)->fetchAll();

        return $this->hydrate($rows);
    }

    private function hydrate(array $rows): array
    {
        return array_map(function (array $data) {
            $obj = new $this->modelClass();
            foreach ($data as $key => $value) {
                $obj->$key = $obj->cast($key, $value);
            }

            return $obj;
        }, $rows);
    }

    public function paginate(
        int $page = 1,
        int $perPage = 10,
        array $perPageOptions = [10, 25, 50, 100]
    ): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = $this->count();
        $sql    = sprintf(
            'SELECT * FROM %s%s LIMIT %d OFFSET %d',
            $this->table,
            $this->buildWhereClause(),
            $perPage,
            $offset
        );
        $rows   = $this->db->query($sql, $this->bindings)->fetchAll();
        $rows   = $this->hydrate($rows);

        return [
            'data'             => $rows,
            'total'            => $total,
            'per_page'         => $perPage,
            'per_page_options' => $perPageOptions,
            'current_page'     => $page,
            'last_page'        => (int) ceil($total / $perPage),
        ];
    }
}