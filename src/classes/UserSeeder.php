<?php
class UserSeeder {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function generateRandomPassword($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomPassword = '';
        for ($i = 0; $i < $length; $i++) {
            $randomPassword .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomPassword;
    }

    public function createUserTable() {
        $table_check_query = "SHOW TABLES LIKE 'users'";
        $table_exists = $this->db->query($table_check_query);

        if ($table_exists->num_rows == 0) {
            $table_creation_query = "
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                firstname VARCHAR(50) NOT NULL,
                lastname VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                poke_count INT DEFAULT 0
            ) ENGINE=INNODB;
            ";

            if ($this->db->query($table_creation_query) === TRUE) {
                echo "Table `users` created successfully.\n";
            } else {
                die("Error creating table `users`: " . $this->db->error . "\n");
            }
        } else {
            // Check if poke_count column exists, and add it if it doesn't
            $column_check_query = "SHOW COLUMNS FROM users LIKE 'poke_count'";
            $column_exists = $this->db->query($column_check_query);

            if ($column_exists->num_rows == 0) {
                $add_column_query = "ALTER TABLE users ADD poke_count INT DEFAULT 0";
                if ($this->db->query($add_column_query) === TRUE) {
                    echo "Column `poke_count` added successfully.\n";
                } else {
                    die("Error adding column `poke_count`: " . $this->db->error . "\n");
                }
            }
        }
    }

    public function seedUsers($csvFile) {
        if (!file_exists($csvFile)) {
            die('CSV file not found.');
        }

        if (($handle = fopen($csvFile, 'r')) === false) {
            die('Error opening CSV file.');
        }

        fgetcsv($handle); // Skip the header row

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            list($id, $firstname, $lastname, $email) = $data;
            $username = strtolower($firstname . '.' . $lastname);
            $password = $this->generateRandomPassword();
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $check_query = "SELECT id FROM users WHERE id = ?";
            $stmt = $this->db->prepare($check_query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $update_query = "UPDATE users SET username = ?, firstname = ?, lastname = ?, email = ?, password = ? WHERE id = ?";
                $update_stmt = $this->db->prepare($update_query);
                $update_stmt->bind_param("sssssi", $username, $firstname, $lastname, $email, $password_hash, $id);

                if ($update_stmt->execute()) {
                    echo "User $username updated successfully with new password: $password\n";
                } else {
                    echo "Error updating user $username: " . $update_stmt->error . "\n";
                }

                $update_stmt->close();
            } else {
                $insert_query = "INSERT INTO users (id, username, firstname, lastname, email, password) VALUES (?, ?, ?, ?, ?, ?)";
                $insert_stmt = $this->db->prepare($insert_query);
                $insert_stmt->bind_param("isssss", $id, $username, $firstname, $lastname, $email, $password_hash);

                if ($insert_stmt->execute()) {
                    echo "User $username inserted successfully with password: $password\n";
                } else {
                    echo "Error inserting user $username: " . $insert_stmt->error . "\n";
                }

                $insert_stmt->close();
            }

            $stmt->close();
        }

        fclose($handle);
    }
}
?>