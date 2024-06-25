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