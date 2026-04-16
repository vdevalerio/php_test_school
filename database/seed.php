<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$fresh = in_array('--fresh', $argv);

if ($fresh) {
    $db = new App\Core\Database();
    $db->query("SET FOREIGN_KEY_CHECKS = 0");
    $db->query("TRUNCATE TABLE notas");
    $db->query("TRUNCATE TABLE alunos");
    $db->query("TRUNCATE TABLE turmas");
    $db->query("SET FOREIGN_KEY_CHECKS = 1");
    echo "Tables truncated.\n";
}

(new Database\Seeders\DatabaseSeeder())->run();

echo "Database seeded successfully.\n";