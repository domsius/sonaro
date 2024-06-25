<?php
class NotificationLoader {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function loadNotifications($userId, $limit = 5) {
        $query = "SELECT ph.poke_date, u.firstname, u.lastname
                  FROM poke_history ph
                  JOIN users u ON ph.from_user_id = u.id
                  WHERE ph.to_user_id = ?
                  ORDER BY ph.poke_date DESC
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $notifications;
    }
}
?>