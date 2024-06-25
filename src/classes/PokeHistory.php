<?php
class PokeHistory {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getPokeHistory($userId, $search = '', $dateFrom = '', $dateTo = '', $page = 1, $sortBy = 'poke_date', $sortOrder = 'desc') {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = "SELECT ph.poke_date, u1.username AS from_user, u2.username AS to_user
                  FROM poke_history ph
                  JOIN users u1 ON ph.from_user_id = u1.id
                  JOIN users u2 ON ph.to_user_id = u2.id
                  WHERE ph.from_user_id = ? OR ph.to_user_id = ?";

        if ($search) {
            $query .= " AND (u1.username LIKE ? OR u2.username LIKE ?)";
        }

        if ($dateFrom) {
            $query .= " AND ph.poke_date >= ?";
        }

        if ($dateTo) {
            $query .= " AND ph.poke_date <= ?";
        }

        $query .= " ORDER BY $sortBy $sortOrder LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($query);
        if ($search) {
            $search = "%$search%";
            $stmt->bind_param("iissssii", $userId, $userId, $search, $search, $dateFrom, $dateTo, $limit, $offset);
        } else {
            $stmt->bind_param("iissii", $userId, $userId, $dateFrom, $dateTo, $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $pokes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $pokes;
    }

    public function getPokeHistoryCount($userId, $search = '', $dateFrom = '', $dateTo = '') {
        $query = "SELECT COUNT(*) AS total
                  FROM poke_history ph
                  JOIN users u1 ON ph.from_user_id = u1.id
                  JOIN users u2 ON ph.to_user_id = u2.id
                  WHERE ph.from_user_id = ? OR ph.to_user_id = ?";

        if ($search) {
            $query .= " AND (u1.username LIKE ? OR u2.username LIKE ?)";
        }

        if ($dateFrom) {
            $query .= " AND ph.poke_date >= ?";
        }

        if ($dateTo) {
            $query .= " AND ph.poke_date <= ?";
        }

        $stmt = $this->db->prepare($query);
        if ($search) {
            $search = "%$search%";
            $stmt->bind_param("iissss", $userId, $userId, $search, $search, $dateFrom, $dateTo);
        } else {
            $stmt->bind_param("iiss", $userId, $userId, $dateFrom, $dateTo);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_assoc()['total'];
        $stmt->close();

        return $total;
    }
}
?>