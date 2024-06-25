<?php
require_once 'vendor/autoload.php';
require_once 'db/db.php';
require_once './classes/PokeSeeder.php';

$db = getDatabaseConnection();
$pokesSeeder = new PokeSeeder($db);

$pokesSeeder->createPokeHistoryTable();

$jsonFile = 'pokes.json';
if (!file_exists($jsonFile)) {
    die('JSON file not found.');
}

$jsonData = file_get_contents($jsonFile);
$pokes = json_decode($jsonData, true);

if ($pokes === null) {
    die('Failed to decode JSON.');
}

$pokesSeeder->seedPokes($pokes);

$db->close();
?>