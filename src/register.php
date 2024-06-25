<?php
require_once 'vendor/autoload.php';
require_once 'db/db.php';
require_once './classes/User.php';
require_once './classes/Registration.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db = getDatabaseConnection();
$userModel = new User($db);
$registration = new Registration($userModel);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');
    $response = $registration->register(
        $_POST["firstname"],
        $_POST["lastname"],
        $_POST["email"],
        $_POST["username"],
        $_POST["password"],
        $_POST["confirm_password"]
    );

    echo json_encode($response);
    exit();
}

$title = "Registracija";
include './includes/header.php';
?>

<div class="container-fluid register">
    <div class="container flex justify-center">
        <form id="register-form" method="post" action="register.php">
            <h3 class="center-align">Registracija</h3>
            <div class="row">
                <div class="input-field">
                    <input type="text" id="firstname" name="firstname" required>
                    <label for="firstname">Vardas</label>
                </div>
                <div class="input-field">
                    <input type="text" id="lastname" name="lastname" required>
                    <label for="lastname">Pavardė</label>
                </div>
                <div class="input-field">
                    <input type="email" id="email" name="email" required>
                    <label for="email">El. paštas</label>
                </div>
                <div class="input-field">
                    <input type="text" id="username" name="username" required>
                    <label for="username">Vartotojo vardas</label>
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
                    <button type="submit" class="btn waves-effect waves-light">Registruotis</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#register-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                type: 'POST',
                url: 'register.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        toastr.error(response.error);
                    } else if (response.success) {
                        toastr.success(response.success);
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 3000);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    toastr.error('Įvyko klaida. Bandykite dar kartą.');
                }
            });
        });
    });
</script>

<?php include './includes/footer.php'; ?>