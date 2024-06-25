<?php
require_once 'vendor/autoload.php';
require_once 'db/db.php';
require_once './classes/UserSession.php';
require_once './classes/Login.php';

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
?>

<div class="container-fluid login">
    <div class="container flex justify-center">
        <?php
        if (isset($error_message)) {
            echo "<script>toastr.error('$error_message');</script>";
        }

        if (isset($_GET['register_success']) && $_GET['register_success'] == 1) {
            echo "<script>toastr.success('Registracija sėkminga. Dabar galite prisijungti.');</script>";
        }
        ?>
        <form method="post" action="login.php">
            <h3 class="center-align">Prisijungimas</h3>
            <div class="row">
                <div class="input-field">
                    <input type="text" id="username" name="username" required>
                    <label for="username">Vartotojo vardas</label>
                </div>
                <div class="input-field">
                    <input type="password" id="password" name="password" required>
                    <label for="password">Slaptažodis</label>
                </div>
                <div class="center-align">
                    <button type="submit" class="btn waves-effect waves-light">Prisijungti</button>
                    <a href="/register" class="btn waves-effect waves-light blue">Registruotis</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include './includes/footer.php'; ?>