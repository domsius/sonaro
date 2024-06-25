<?php
class UserSession {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isUserLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function login($userId, $username) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
    }

    public function logout() {
        session_destroy();
    }
}
?>