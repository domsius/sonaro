<?php
class Registration {
    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function register($firstname, $lastname, $email, $username, $password, $confirmPassword) {
        if (empty($firstname) || empty($lastname) || empty($email) || empty($username) || empty($password) || empty($confirmPassword)) {
            return ['error' => 'Visi laukai privalomi.'];
        }

        if ($password !== $confirmPassword) {
            return ['error' => 'Slaptažodžiai nesutampa.'];
        }

        if (!preg_match("/(?=.*[0-9])(?=.*[A-Z]).{8,}/", $password)) {
            return ['error' => 'Slaptažodis turi būti bent 8 simbolių ilgio ir turėti bent vieną skaičių ir vieną didžiąją raidę.'];
        }

        if ($this->user->isUsernameTaken($username)) {
            return ['error' => 'Vartotojo vardas jau naudojamas.'];
        }

        if ($this->user->isEmailTaken($email)) {
            return ['error' => 'El. paštas jau naudojamas.'];
        }

        if ($this->user->registerUser($username, $firstname, $lastname, $email, $password)) {
            return ['success' => 'Registracija sėkminga. Dabar galite prisijungti!'];
        } else {
            return ['error' => 'Registracijos klaida.'];
        }
    }
}
?>