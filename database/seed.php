<?php

use App\Core\Database;
use Database\Seeders\DatabaseSeeder;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$fresh = in_array('--fresh', $argv);

if ($fresh) {
    $db = Database::getInstance();
    $db->query("SET FOREIGN_KEY_CHECKS = 0");
    $db->query("TRUNCATE TABLE notas");
    $db->query("TRUNCATE TABLE alunos");
    $db->query("TRUNCATE TABLE turmas");
    $db->query("SET FOREIGN_KEY_CHECKS = 1");
    echo "Tables truncated.\n";
}

(new DatabaseSeeder())->run();

echo "Database seeded successfully.\n";