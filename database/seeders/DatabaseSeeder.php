<?php

namespace Database\Seeders;

class DatabaseSeeder
{
    public function run(): void
    {
        (new TurmaSeeder())->run();
    }
}