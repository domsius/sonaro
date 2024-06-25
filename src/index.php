<?php
require 'db/db.php';
require 'classes/UserSession.php';
require 'classes/UserLoader.php';
require 'classes/View.php';

$title = "Vartotojai";
include './includes/header.php';

$session = new UserSession();

if (!$session->isUserLoggedIn()) {
    echo "User not logged in.";
    exit;
}

$view = new View();
$view->render('./templates/users.php');
?>

<script>
    function pokeUser(userId) {
    $.ajax({
        type: "POST",
        url: "poke.php",
        data: { user_id: userId },
        success: function(response) {
            // Check if the response is already an object
            if (typeof response === "string") {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    toastr.error('Failed to parse response.');
                    return;
                }
            }

            if (response.success) {
                let pokeCountElement = $("#poke-count-" + userId);
                pokeCountElement.text(response.new_poke_count);
                toastr.success('Baksnojimas sėkmingas!');
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('Failed to send poke.');
        }
    });
}

$(document).ready(function() {
    let currentSortBy = 'firstname';
    let currentSortOrder = 'asc';

    loadUsers();

    $('#search').on('input', function() {
        loadUsers();
    });

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const $parentLi = $(this).closest('li');
        if ($parentLi.hasClass('disabled')) {
            return;
        }
        const page = $(this).attr('data-page');
        loadUsers(page);
    });

    $(document).on('click', '.sortable', function(e) {
        e.preventDefault();
        const sortBy = $(this).attr('data-sort-by');
        if (currentSortBy === sortBy) {
            currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortBy = sortBy;
            currentSortOrder = 'asc';
        }
        loadUsers(1, currentSortBy, currentSortOrder);
    });

    function loadUsers(page = 1, sortBy = currentSortBy, sortOrder = currentSortOrder) {
        const search = $('#search').val();
        $('#loader').show();
        $('.loader-container').show();
        $('#users-container').hide();

        $.ajax({
            type: "GET",
            url: "load_users.php",
            data: { search: search, page: page, sort_by: sortBy, sort_order: sortOrder },
            success: function(response) {
                $('#loader').hide();
                $('.loader-container').hide();
                $('#users-container').show();
                
                // Check if the response is already an object
                if (typeof response === "string") {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        $('#users-container').html('<p>Error parsing JSON response</p>');
                        return;
                    }
                }

                $('#users-container').html(response.usersTable);
                $('#pagination').html(response.pagination);
            },
            error: function() {
                $('#loader').hide();
                $('#users-container').show();
                $('#users-container').html('<p>Error loading users</p>');
            }
        });
    }
});
</script>
<?php include './includes/footer.php'; ?>