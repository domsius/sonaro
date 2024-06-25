<?php
require_once 'vendor/autoload.php';
require_once 'db/db.php';
require_once './classes/UserSession.php';
require_once './classes/User.php';

use Dotenv\Dotenv;

session_start();

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

$user_id = $_SESSION['user_id'];
$db = getDatabaseConnection();
$userModel = new User($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Visi laukai privalomi.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Slaptažodžiai nesutampa.';
    } elseif (!preg_match("/(?=.*[0-9])(?=.*[A-Z]).{8,}/", $password)) {
        $error_message = 'Slaptažodis turi būti bent 8 simbolių ilgio ir turėti bent vieną skaičių ir vieną didžiąją raidę.';
    } else {
        $updateSuccess = $userModel->updateUser($user_id, $firstname, $lastname, $email, $password);
        if ($updateSuccess) {
            header("Location: profile?update_success=1");
            exit();
        } else {
            $error_message = 'Error: ' . $db->error;
        }
    }
}

$user = $userModel->getUserById($user_id);

$title = "Profilis";
include './includes/header.php';
?>
<div class="container-fluid profile">
    <div class="container flex justify-center">
        <?php
        if (isset($error_message)) {
            echo "<script>toastr.error('$error_message');</script>";
        }
        ?>
        <form method="post" action="profile">
            <h3 class="center-align">Profilis</h3>
            <div class="row">
                <div class="input-field">
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" disabled>
                    <label for="username">Vartotojo vardas</label>
                </div>
                <div class="input-field">
                    <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($user['firstname']); ?>" required>
                    <label for="firstname">Vardas</label>
                </div>
                <div class="input-field">
                    <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($user['lastname']); ?>" required>
                    <label for="lastname">Pavardė</label>
                </div>
                <div class="input-field">
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
                    <label for="email">El. paštas</label>
                </div>
                <div class="input-field">
                    <input type="password" id="password" name="password" required>
                    <label for="password">Slaptažodis</label>
                </div>
                <div class="input-field">
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <label for="confirm_password">Pakartokite slaptažodį</label>
                </div>
                <div class="center-align">
                    <button type="submit" class="btn waves-effect waves-light">Atnaujinti</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include './includes/footer.php'; ?>