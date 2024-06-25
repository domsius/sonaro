<?php
require_once 'vendor/autoload.php';
require_once 'db/db.php';
require_once './classes/UserSession.php';
require_once './classes/UserLoader.php';

session_start();

header('Content-Type: application/json');

$session = new UserSession();

if (!$session->isUserLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$currentUserId = $session->getUserId();
$db = getDatabaseConnection();
$userLoader = new UserLoader($db);

$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$sortBy = $_GET['sort_by'] ?? 'firstname';
$sortOrder = $_GET['sort_order'] ?? 'asc';
$itemsPerPage = 10;

$users = $userLoader->loadUsers($currentUserId, $search, $page, $sortBy, $sortOrder, $itemsPerPage);
$totalItems = $userLoader->getUserCount($currentUserId, $search);
$totalPages = ceil($totalItems / $itemsPerPage);

// Generate HTML for users table
$usersTable = '<table class="highlight"><thead><tr>
                <th><a href="#" class="sortable" data-sort-by="firstname" data-sort-order="' . ($sortBy == 'firstname' && $sortOrder == 'asc' ? 'desc' : 'asc') . '">Vardas</a></th>
                <th><a href="#" class="sortable" data-sort-by="lastname" data-sort-order="' . ($sortBy == 'lastname' && $sortOrder == 'asc' ? 'desc' : 'asc') . '">Pavardė</a></th>
                <th><a href="#" class="sortable" data-sort-by="email" data-sort-order="' . ($sortBy == 'email' && $sortOrder == 'asc' ? 'desc' : 'asc') . '">El. paštas</a></th>
                <th><a href="#" class="sortable" data-sort-by="poke_count" data-sort-order="' . ($sortBy == 'poke_count' && $sortOrder == 'asc' ? 'desc' : 'asc') . '">Poke skaičius</a></th>
                <th>Veiksmas</th></tr></thead><tbody>';
foreach ($users as $user) {
    $usersTable .= '<tr>
        <td>' . htmlspecialchars($user['firstname']) . '</td>
        <td>' . htmlspecialchars($user['lastname']) . '</td>
        <td>' . htmlspecialchars($user['email']) . '</td>
        <td id="poke-count-' . $user['id'] . '">' . htmlspecialchars($user['poke_count']) . '</td>
        <td><button class="btn waves-effect waves-light blue poke-btn" onclick="pokeUser(' . $user['id'] . ')">Poke</button></td>
    </tr>';
}
$usersTable .= '</tbody></table>';

// Generate pagination HTML
$pagination = '';
if ($totalPages > 1) {
    $pagination .= '<li class="' . ($page == 1 ? 'disabled' : 'waves-effect') . '"><a href="#" data-page="' . ($page - 1) . '" data-sort-by="' . $sortBy . '" data-sort-order="' . $sortOrder . '"><i class="fa-solid fa-chevron-left"></i></a></li>';
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $page || ($i <= $page + 2 && $i >= $page - 2) || $i == 1 || $i == $totalPages) {
            $pagination .= '<li class="' . ($i == $page ? 'active' : 'waves-effect') . '"><a href="#" data-page="' . $i . '" data-sort-by="' . $sortBy . '" data-sort-order="' . $sortOrder . '">' . $i . '</a></li>';
        } elseif ($i == 2 || $i == $totalPages - 1) {
            $pagination .= '<li class="disabled"><a href="#">...</a></li>';
        }
    }
    $pagination .= '<li class="' . ($page == $totalPages ? 'disabled' : 'waves-effect') . '"><a href="#" data-page="' . ($page + 1) . '" data-sort-by="' . $sortBy . '" data-sort-order="' . $sortOrder . '"><i class="fa-solid fa-chevron-right"></i></a></li>';
}

echo json_encode(['usersTable' => $usersTable, 'pagination' => $pagination]);

$db->close();
?>