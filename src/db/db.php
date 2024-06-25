<?php
require_once 'vendor/autoload.php';
require_once 'classes/Database.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));

$dotenv->load();

function getDatabaseConnection() {
    $servername = $_ENV['MYSQL_HOST'];
    $username = $_ENV['MYSQL_USER'];
    $password = $_ENV['MYSQL_PASSWORD'];
    $dbname = $_ENV['MYSQL_DATABASE'];



    return new Database($servername, $username, $password, $dbname);
}
?>