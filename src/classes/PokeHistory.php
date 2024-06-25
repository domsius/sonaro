<?php
class PokeHistory {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getPokeHistory($userId, $search = '', $dateFrom = '', $dateTo = '', $page = 1, $sortBy = 'poke_date', $sortOrder = 'desc') {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = "SELECT ph.poke_date, CONCAT(u1.firstname, ' ', u1.lastname) AS from_user, CONCAT(u2.firstname, ' ', u2.lastname) AS to_user
                  FROM poke_history ph
                  JOIN users u1 ON ph.from_user_id = u1.id
                  JOIN users u2 ON ph.to_user_id = u2.id
                  WHERE ph.from_user_id = ? OR ph.to_user_id = ?";

        $params = [$userId, $userId];
        $types = 'ii';

        if ($search) {
            $query .= " AND (CONCAT(u1.firstname, ' ', u1.lastname) LIKE ? OR CONCAT(u2.firstname, ' ', u2.lastname) LIKE ?)";
            $search = "%$search%";
            $params[] = $search;
            $params[] = $search;
            $types .= 'ss';
        }

        if ($dateFrom) {
            $query .= " AND ph.poke_date >= ?";
            $params[] = $dateFrom;
            $types .= 's';
        }

        if ($dateTo) {
            $query .= " AND ph.poke_date <= ?";
            $params[] = $dateTo;
            $types .= 's';
        }

        $query .= " ORDER BY $sortBy $sortOrder LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);

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

        $params = [$userId, $userId];
        $types = 'ii';

        if ($search) {
            $query .= " AND (CONCAT(u1.firstname, ' ', u1.lastname) LIKE ? OR CONCAT(u2.firstname, ' ', u2.lastname) LIKE ?)";
            $search = "%$search%";
            $params[] = $search;
            $params[] = $search;
            $types .= 'ss';
        }

        if ($dateFrom) {
            $query .= " AND ph.poke_date >= ?";
            $params[] = $dateFrom;
            $types .= 's';
        }

        if ($dateTo) {
            $query .= " AND ph.poke_date <= ?";
            $params[] = $dateTo;
            $types .= 's';
        }

        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_assoc()['total'];
        $stmt->close();

        return $total;
    }
}
?>