<?php
require 'vendor/autoload.php';
require './classes/UserSession.php';

$session = new UserSession();
$session->logout();

header("Location: login?logout=1");
exit();
?>