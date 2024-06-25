<?php
require 'vendor/autoload.php';
require 'db/db.php';
require './classes/MailService.php';
require './classes/PokeService.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$fromUserId = $_SESSION['user_id'];
$toUserId = $_POST['user_id'];

$db = getDatabaseConnection();

// Commented out MailService instantiation for now
// $mailService = new MailService(
//     $_ENV['SMTP_HOST'],
//     $_ENV['SMTP_USERNAME'],
//     $_ENV['SMTP_PASSWORD'],
//     $_ENV['SMTP_FROM_EMAIL'],
//     $_ENV['SMTP_FROM_NAME'],
//     $_ENV['SMTP_PORT']
// );

$pokeService = new PokeService($db);
$response = $pokeService->pokeUser($fromUserId, $toUserId);

echo json_encode($response);

$db->close();
?>