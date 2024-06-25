<?php
class Login {
    private $db;
    private $session;

    public function __construct(Database $db, UserSession $session) {
        $this->db = $db;
        $this->session = $session;
    }

    public function authenticate($username, $password) {
        if (empty($username) || empty($password)) {
            return 'Vartotojo vardas ir slaptažodis yra privalomi.';
        }

        $query = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $this->session->login($user['id'], $user['username']);
                return true;
            } else {
                return 'Blogi prisijungimo duomenys.';
            }
        } else {
            return 'Blogi prisijungimo duomenys.';
        }
    }
}
?>