<?php
require_once 'vendor/autoload.php';
require_once 'db/db.php';
require_once './classes/UserSession.php';
require_once './classes/Login.php';
require_once 'classes/View.php';

$title = "Prisijungimas";

$session = new UserSession();
$db = getDatabaseConnection();
$login = new Login($db, $session);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $authResult = $login->authenticate($username, $password);

    if ($authResult === true) {
        header("Location: /?login=1");
        exit();
    } else {
        $error_message = $authResult;
    }
}

include './includes/header.php';
$view = new View();
$view->render('./templates/login.php');
include './includes/footer.php';
?>