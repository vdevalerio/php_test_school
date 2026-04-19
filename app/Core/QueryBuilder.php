<?php

namespace App\Core;

class QueryBuilder
{
    private Database $db;

    private array $wheres   = [];
    private array $bindings = [];

    private array $orderByClauses     = [];

    private array $joins   = [];
    private string $select = '*';

    public function __construct(
        private string $table,
        private string $modelClass
    ) {
        $this->db = Database::getInstance();
    }

    public function select(string $select): static
    {
        $this->select = $select;

        return $this;
    }

    public function fromSubquery(string $alias = 'subquery'): static
    {
        $innerSql = sprintf(
            'SELECT %s FROM %s%s%s',
            $this->select,
            $this->table,
            $this->buildJoinClause(),
            $this->buildWhereClause()
        );

        $new = new static(
            "({$innerSql}) AS {$alias}",
            $this->modelClass
        );
        $new->select   = '*';
        $new->bindings = $this->bindings;

        return $new;
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
        if (empty($this->orderByClauses)) {
            return '';
        }

        return ' ORDER BY ' . implode(', ', $this->orderByClauses);
    }

    public function orderBy(string $column, string $direction = 'asc'): static
    {
        $direction              = strtolower($direction) === 'desc' ? 'desc' : 'asc';
        $this->orderByClauses[] = "{$column} {$direction}";

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
        $sql = "SELECT {$this->select} FROM {$this->table}"
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
            'SELECT %s FROM %s%s%s%s LIMIT %d OFFSET %d',
            $this->select,
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