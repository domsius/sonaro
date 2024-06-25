<?php
class UserLoader {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function loadUsers($currentUserId, $search = '', $page = 1, $sortBy = 'firstname', $sortOrder = 'asc', $itemsPerPage = 10) {
        $offset = ($page - 1) * $itemsPerPage;
        $validSortColumns = ['firstname', 'lastname', 'email', 'poke_count'];
        
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'firstname';
        }
        $sortOrder = ($sortOrder === 'desc') ? 'desc' : 'asc';

        $query = "SELECT id, firstname, lastname, email, poke_count FROM users
                  WHERE id != ? AND (firstname LIKE ? OR lastname LIKE ? OR email LIKE ?)
                  ORDER BY $sortBy $sortOrder
                  LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $searchParam = "%$search%";
        $stmt->bind_param("isssii", $currentUserId, $searchParam, $searchParam, $searchParam, $itemsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $users;
    }

    public function getUserCount($currentUserId, $search = '') {
        $query = "SELECT COUNT(*) AS total FROM users
                  WHERE id != ? AND (firstname LIKE ? OR lastname LIKE ? OR email LIKE ?)";
        $stmt = $this->db->prepare($query);
        $searchParam = "%$search%";
        $stmt->bind_param("isss", $currentUserId, $searchParam, $searchParam, $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_assoc()['total'];
        $stmt->close();

        return $total;
    }
}
?>