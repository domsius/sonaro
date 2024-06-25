<?php
class User {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getUserById($userId) {
        $query = "SELECT username, firstname, lastname, email FROM users WHERE id=?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function updateUser($userId, $firstname, $lastname, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET firstname=?, lastname=?, email=?, password=? WHERE id=?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssi", $firstname, $lastname, $email, $passwordHash, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function isUsernameTaken($username) {
        $query = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $isTaken = $result->num_rows > 0;
        $stmt->close();
        return $isTaken;
    }

    public function isEmailTaken($email) {
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $isTaken = $result->num_rows > 0;
        $stmt->close();
        return $isTaken;
    }

    public function registerUser($username, $firstname, $lastname, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, firstname, lastname, email, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssss", $username, $firstname, $lastname, $email, $passwordHash);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>