<?php

require "helpers.php";
#require "router.php";

require_once 'Database.php';

$config = require '../config/database.php';

$db = new Database($config);
$turmas = $db->query("SELECT * FROM turmas");

dd($turmas);
