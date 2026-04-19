<?php

namespace App\Core;

class QueryBuilder
{
    private array $wheres   = [];
    private array $bindings = [];
    private Database $db;
    private ?string $orderByColumn    = null;
    private string  $orderByDirection = 'asc';
    private array $joins = [];

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

    private function buildOrderByClause(): string
    {
        if ($this->orderByColumn === null) {
            return '';
        }

        $clause = sprintf(
            ' ORDER BY %s %s',
            $this->orderByColumn,
            $this->orderByDirection
        );

        return $clause;
    }

    public function orderBy(string $column, string $direction = 'asc'): static
    {
        $this->orderByColumn    = $column;
        $this->orderByDirection = strtolower($direction) === 'desc'
            ? 'desc'
            : 'asc';

        return $this;
    }

    private function buildJoinClause(): string
    {
        if (empty($this->joins)) {
            return '';
        }

        return ' ' . implode(' ', $this->joins);
    }

    public function join(
        string $table,
        string $first,
        string $operator,
        string $second
    ): static
    {
        $this->joins[] = "JOIN {$table} ON {$first} {$operator} {$second}";

        return $this;
    }

    public function leftJoin(
        string $table,
        string $first,
        string $operator,
        string $second
    ): static
    {
        $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";

        return $this;
    }

    public function count(): int
    {
        $sql    = 'SELECT COUNT(*) FROM '
            . $this->table
            . $this->buildJoinClause()
            . $this->buildWhereClause();
        $result = $this->db->query($sql, $this->bindings)->fetch();

        return (int) reset($result);
    }

    public function get(): array
    {
        $sql  = 'SELECT ' . $this->table . '.* FROM '
            . $this->table
            . $this->buildJoinClause()
            . $this->buildWhereClause()
            . $this->buildOrderByClause();

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
            'SELECT %s.* FROM %s%s%s%s LIMIT %d OFFSET %d',
            $this->table,
            $this->table,
            $this->buildJoinClause(),
            $this->buildWhereClause(),
            $this->buildOrderByClause(),
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