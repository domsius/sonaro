<?php
require 'vendor/autoload.php';
require 'db/db.php';
require './classes/UserSession.php';
require './classes/PokeHistory.php';

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$session = new UserSession();

if (!$session->isUserLoggedIn()) {
    header("Location: login");
    exit;
}

$userId = $session->getUserId();
$db = getDatabaseConnection();
$pokeHistory = new PokeHistory($db);

$title = "Poke Istorija";
include './includes/header.php';
?>

<div class="container-fluid poke">
    <div class="container flex justify-center">
        <div class="poke-history">
            <h3 class="center-align">Poke istorija</h3>
            <div class="search-form flex justify-center">
                <div class="form-group search-wrapper">
                    <input type="text" id="search" placeholder="Ieškoti pagal vardą">
                </div>
                <div class="form-group datepicker-wrapper">
                    <input type="text" id="date_from" placeholder="Data nuo">
                </div>
                <div class="form-group datepicker-wrapper">
                    <input type="text" id="date_to" placeholder="Data iki">
                </div>
            </div>
            <div id="loader" class="loader-container" style="display: none;">
                <div class="loader"></div>
            </div>
            <div id="pokes-container">
            </div>
            <ul class="pagination center-align" id="pagination">
            </ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#date_from').datepicker({
            format: 'yyyy-mm-dd',
        });
        $('#date_to').datepicker({
            format: 'yyyy-mm-dd',
        });

        let currentSortBy = 'poke_date';
        let currentSortOrder = 'desc';

        loadPokes();

        $('#search, #date_from, #date_to').on('input change', function() {
            loadPokes();
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const $parentLi = $(this).closest('li');
            if ($parentLi.hasClass('disabled') || $parentLi.hasClass('active')) {
                return;
            }
            const page = $(this).attr('data-page');
            loadPokes(page);
        });

        $(document).on('click', '.sortable', function(e) {
            e.preventDefault();
            const sort_by = $(this).attr('data-sort-by');
            if (currentSortBy === sort_by) {
                currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                currentSortBy = sort_by;
                currentSortOrder = 'asc';
            }
            loadPokes(1, currentSortBy, currentSortOrder);
        });

        function loadPokes(page = 1, sort_by = currentSortBy, sort_order = currentSortOrder) {
            const search = $('#search').val();
            const date_from = $('#date_from').val();
            const date_to = $('#date_to').val();

            $('#loader').show();
            $('#pokes-container').hide();

            $.ajax({
                type: "GET",
                url: "load_pokes.php",
                data: { search: search, date_from: date_from, date_to: date_to, page: page, sort_by: sort_by, sort_order: sort_order },
                success: function(response) {
                    $('#loader').hide();
                    $('#pokes-container').show();
                    try {
                        response = JSON.parse(response);
                        $('#pokes-container').html(response.pokesTable);
                        $('#pagination').html(response.pagination);
                    } catch (e) {
                        $('#pokes-container').html('<p>Error parsing JSON response</p>');
                    }
                },
                error: function() {
                    $('#loader').hide();
                    $('#pokes-container').show();
                    $('#pokes-container').html('<p>Error loading pokes</p>');
                }
            });
        }
    });
</script>

<?php include './includes/footer.php'; ?>