<?php

require "helpers.php";
#require "router.php";

require_once '../vendor/autoload.php';
require_once 'Database.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$envs = [
    'DB_HOST' => $_ENV['DB_HOST'],
    'DB_PORT' => $_ENV['DB_PORT'],
    'DB_USERNAME' => $_ENV['DB_USERNAME'],
    'DB_PASSWORD' => $_ENV['DB_PASSWORD'],
    'DB_DATABASE' => $_ENV['DB_DATABASE'],
];

$db = new Database($envs);
$turmas = $db->query("SELECT * FROM turmas");

dd($turmas);
