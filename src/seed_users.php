<?php
require 'vendor/autoload.php';
require 'db/db.php';
require './classes/UserSeeder.php';

$db = getDatabaseConnection();
$userSeeder = new UserSeeder($db);

$userSeeder->createUserTable();

$csvFile = 'users.csv';

$userSeeder->seedUsers($csvFile);

$db->close();
?>