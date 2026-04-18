<?php

namespace App\Core;

class QueryBuilder
{
    private array $wheres   = [];
    private array $bindings = [];

    public function __construct(
        private string $table,
        private string $modelClass
    ) {}

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
}