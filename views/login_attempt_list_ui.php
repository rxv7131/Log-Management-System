<?php

require_once "../views/common_ui.php";
view_common_includes('../');
view_common_header();
view_common_navigation("Login Attempt List", true, 0);

// If the user is trying to view login attempts from today, this week, this month, or all logs
if (isset($_GET['today'])) {
    view_login_attempt_list_created_today($_GET['today']);
} else if (isset($_GET['week'])) {
    view_login_attempt_list_created_today($_GET['week']);
} else if (isset($_GET['month'])) {
    view_login_attempt_list_created_today($_GET['month']);
} else {
    view_login_attempt_list_created_today("all");
} // Ends if

view_common_footer();

// Function to create the list of login attempts created within the passed in timeframe
function view_login_attempt_list_created_today($successType) {
    
    $db = new DB();

    $currentUser = $db->getUserByID($_COOKIE["loggedInUserID"]);

    $timeframe = "";

    if (isset($_GET["today"]) == "day") {
        $timeframe = "day";
    } else if (isset($_GET["week"]) == "week") {
        $timeframe = "week";
    } else if (isset($_GET["month"]) == "month") {
        $timeframe = "month";
    } // Ends if

    // Get the current page number and number of records per page from the query string
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $recordsPerPage = 20;

    $totalRows = $db->getLoginAttemptsTimeframeCount($successType, $currentUser[0], $currentUser[6], $timeframe);
    $totalNumberOfPages = ceil($totalRows / $recordsPerPage);
    
    // Get the login attempt objects for the current page
    $loginAttemptObjects = $db->getLoginAttemptsTimeframeAsTable($successType, $currentUser[0], $currentUser[6], $timeframe, $currentPage, $recordsPerPage);

    view_login_attempt_list_table($loginAttemptObjects, $totalNumberOfPages, $currentPage);

} // Ends view_login_attempt_list_created_today

// Function to create and display the table of login attempts
function view_login_attempt_list_table($loginAttemptObjects, $totalNumberOfPages, $currentPage) {

    echo('
    <!--Main layout-->
    <main style="margin-top: 58px">

    <div class="container pt-4 d-flex align-items-center justify-content-between">

            <div class="d-flex align-items-center gap-4">

                <p class="h3 m-auto">Login Attempts</p>

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

                <table class="table table-hover">
                    '.$loginAttemptObjects.'
                </table>

            </div>
        </div>

    </main>
    ');

} // Ends view_login_attempt_list_table

?>