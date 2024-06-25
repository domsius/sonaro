<?php
require_once 'vendor/autoload.php';
require_once 'db/db.php';
require_once './classes/UserSession.php';
require_once './classes/PokeHistory.php';

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$session = new UserSession();

if (!$session->isUserLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $session->getUserId();
$db = getDatabaseConnection();
$pokeHistory = new PokeHistory($db);

$search = $_GET['search'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$page = $_GET['page'] ?? 1;
$sortBy = $_GET['sort_by'] ?? 'poke_date';
$sortOrder = $_GET['sort_order'] ?? 'desc';

$pokes = $pokeHistory->getPokeHistory($userId, $search, $dateFrom, $dateTo, $page, $sortBy, $sortOrder);
$totalItems = $pokeHistory->getPokeHistoryCount($userId, $search, $dateFrom, $dateTo);
$itemsPerPage = 10;
$totalPages = ceil($totalItems / $itemsPerPage);

// Generate HTML for pokes table
$pokesTable = '<table class="highlight"><thead><tr>
                <th><a href="#" class="sortable" data-sort-by="poke_date" data-sort-order="' . ($sortBy == 'poke_date' && $sortOrder == 'asc' ? 'desc' : 'asc') . '">Data</a></th>
                <th><a href="#" class="sortable" data-sort-by="from_user" data-sort-order="' . ($sortBy == 'from_user' && $sortOrder == 'asc' ? 'desc' : 'asc') . '">Siuntėjas</a></th>
                <th><a href="#" class="sortable" data-sort-by="to_user" data-sort-order="' . ($sortBy == 'to_user' && $sortOrder == 'asc' ? 'desc' : 'asc') . '">Gavėjas</a></th>
                </tr></thead><tbody>';
foreach ($pokes as $poke) {
    $pokesTable .= '<tr>
        <td>' . htmlspecialchars($poke['poke_date']) . '</td>
        <td>' . htmlspecialchars($poke['from_user']) . '</td>
        <td>' . htmlspecialchars($poke['to_user']) . '</td>
    </tr>';
}
$pokesTable .= '</tbody></table>';

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

echo json_encode(['pokesTable' => $pokesTable, 'pagination' => $pagination]);

$db->close();