<?php

require_once "../views/common_ui.php";
view_common_includes('../');
view_common_header();
view_common_navigation("Alerts", false, 3);

view_undismissed_alerts();

view_common_footer();

// Function to get the appropriate list of undismissed alerts for a user
function view_undismissed_alerts() {
    
    $db = new DB();

    $currentUser = $db->getUserByID($_COOKIE["loggedInUserID"]);

    // Get the current page number and number of records per page from the query string
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $recordsPerPage = 20;

    $totalRows = $db->getAlertsNotDismissedCount($currentUser[0], $currentUser[6]);
    $totalNumberOfPages = ceil($totalRows / $recordsPerPage);
    
    // Get the alert objects for the current page
    $alertObjects = $db->getAlertsNotDismissedAsTable($currentUser[0], $currentUser[6], $currentPage, $recordsPerPage);

    view_undismissed_alerts_table($alertObjects, $totalNumberOfPages, $currentPage);

} // Ends view_undismissed_alerts

// Function to create and return the table of alerts
function view_undismissed_alerts_table($alertObjects, $totalNumberOfPages, $currentPage) {

    echo('
    <!--Main layout-->
    <main style="margin-top: 58px">

    <div class="container pt-4 d-flex align-items-center justify-content-between">

            <div class="d-flex align-items-center gap-4">

                <p class="h3 m-auto">New Alerts:</p>

            </div>

            <!-- Pagination links -->
            <nav aria-label="Pagination">
                <ul class="pagination pagination-circle pagination-custom">
                    <li class="page-item pagination-plain-text">
                        <p class="lh-1 fs-6">Page</p>
                    </li>
                    '.get_common_pagination($totalNumberOfPages, $currentPage).'
                </ul>
            </nav>

        </div>

        <div class="container pt-2 long-table-container">
            <div class="table-responsive search-table">

                <table class="table">
                    '.$alertObjects.'
                </table>

            </div>
        </div>

    </main>
    ');

} // Ends view_undismissed_alerts_table

?>