<?php
class PokeSeeder {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getUserIdByEmail($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->fetch();
        $stmt->close();
        return $id;
    }

    public function createPokeHistoryTable() {
        $table_check_query = "SHOW TABLES LIKE 'poke_history'";
        $table_exists = $this->db->query($table_check_query);

        if ($table_exists->num_rows == 0) {
            $table_creation_query = "
            CREATE TABLE poke_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                from_user_id INT NOT NULL,
                to_user_id INT NOT NULL,
                poke_date DATETIME NOT NULL,
                FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=INNODB;
            ";

            if ($this->db->query($table_creation_query) === TRUE) {
                echo "Table `poke_history` created successfully.\n";
            } else {
                die("Error creating table `poke_history`: " . $this->db->error . "\n");
            }
        }
    }

    public function seedPokes($pokes) {
        foreach ($pokes as $poke) {
            $from_email = $poke['from'];
            $to_email = $poke['to'];
            $date = $poke['date'];

            $from_user_id = $this->getUserIdByEmail($from_email);
            $to_user_id = $this->getUserIdByEmail($to_email);

            if ($from_user_id && $to_user_id) {
                $stmt = $this->db->prepare("INSERT INTO poke_history (from_user_id, to_user_id, poke_date) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $from_user_id, $to_user_id, $date);

                if ($stmt->execute()) {
                    echo "Poke from $from_email to $to_email on $date inserted successfully.\n";
                } else {
                    echo "Error inserting poke from $from_email to $to_email: " . $stmt->error . "\n";
                }

                $stmt->close();
            } else {
                echo "User ID not found for $from_email or $to_email.\n";
            }
        }
    }
}
?>