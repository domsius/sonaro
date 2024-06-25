<?php
ob_start();
require_once 'vendor/autoload.php';
require_once 'db/db.php';
require_once './classes/UserSession.php';
require_once './classes/NotificationLoader.php';

$session = new UserSession();

if (!$session->isUserLoggedIn()) {
    $currentFile = basename($_SERVER['PHP_SELF'], ".php");
    $allowedFiles = ['login', 'register'];
    if (!in_array($currentFile, $allowedFiles)) {
        header("Location: login");
        exit;
    }
}

$current_user_id = $session->getUserId();

$title = isset($title) ? $title : 'Vartotojai';
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="./assets/css/style.css">
    <script>
        $(document).ready(function() {
            $('.dropdown-trigger').dropdown({
                onOpenStart: function() {
                    $('#poke-dropdown').html('<div class="loader-container"><div class="loader"></div></div>');
                    $.ajax({
                        type: "GET",
                        url: "load_notifications.php",
                        success: function(response) {
                            let notificationsHtml = '';
                            if (response.success) {
                                response.notifications.forEach(function(notification) {
                                    notificationsHtml += '<li><a>' + notification.firstname + ' ' + notification.lastname + '</a></li>';
                                });
                                notificationsHtml += '<li class="divider"></li>';
                                notificationsHtml += '<li><a href="poke_history">VISI POKE ></a></li>';
                            } else {
                                notificationsHtml = '<li><a>Prašome prisijungti</a></li>';
                            }
                            $('#poke-dropdown').html(notificationsHtml);
                        },
                        error: function() {
                            $('#poke-dropdown').html('<li><a>Error loading notifications</a></li>');
                        }
                    });
                }
            });

            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            <?php if (isset($_GET['logout']) && $_GET['logout'] == 1) { ?>
                toastr.success('Atsijungta sėkmingai!');
            <?php } ?>
            <?php if (isset($_GET['login']) && $_GET['login'] == 1) { ?>
                toastr.success('Prisijungimas sėkmingas!');
            <?php } ?>
            <?php if (isset($_GET['update_success']) && $_GET['update_success'] == 1) { ?>
                toastr.success('Profilis atnaujintas sėkmingai!');
            <?php } ?>
            <?php if (isset($_GET['register_success']) && $_GET['register_success'] == 1) { ?>
                toastr.success('Registracija sėkminga. Dabar galite prisijungti!');
            <?php } ?>
        });
    </script>
</head>
<body>
    <div class="container-fluid blue">
        <div class="container flex justify-between align-center header">
            <a href="/" class="brand-logo white-text flow-text">BAKSNOTOJAS 2000</a>
            <ul class="right flex justify-between align-center gap">
                <li>
                    <a class="dropdown-trigger" href="#!" data-target="poke-dropdown">
                        <i class="white-text flow-text fa-solid fa-hand-point-right"></i>
                    </a>
                    <ul id="poke-dropdown" class="dropdown-content">
                        <div class="loader-container">
                            <div class="loader"></div>
                        </div>
                    </ul>
                </li>
                <li><a href="profile"><i class="white-text flow-text fa-solid fa-user"></i></a></li>
                <li><a href="logout"><i class="white-text flow-text fa-solid fa-right-from-bracket"></i></a></li>
            </ul>
        </div>
    </div>
</body>
</html>