<?php
require_once 'vendor/autoload.php';
require_once 'db/db.php';
require_once './classes/UserSession.php';
require_once './classes/NotificationLoader.php';

session_start();

header('Content-Type: application/json');

$session = new UserSession();

if (!$session->isUserLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$currentUserId = $session->getUserId();
$db = getDatabaseConnection();
$notificationLoader = new NotificationLoader($db);

$notifications = $notificationLoader->loadNotifications($currentUserId);

echo json_encode(['success' => true, 'notifications' => $notifications]);

$db->close();