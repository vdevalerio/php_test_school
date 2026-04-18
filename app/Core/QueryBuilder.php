<?php

namespace App\Core;

class QueryBuilder
{
    public function __construct(
        private string $table,
        private string $modelClass
    ) {}
}