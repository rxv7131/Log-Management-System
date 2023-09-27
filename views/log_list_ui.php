<?php

require_once "../views/common_ui.php";
require_once "../controllers/validation_controller.php";
view_common_includes('../');
view_common_header();
view_common_navigation("Search Logs", false, 1);

// If the user is trying to view recent logs, logs within a timeframe, or all logs
if (isset($_GET['recent'])) {
    view_log_list_recent();    
} else if (isset($_GET['today']) OR isset($_GET['week']) OR isset($_GET['month'])) {
    view_log_list_created_today();
} else {
    view_log_list_main();
} // Ends if

view_common_footer();

// Function to create the list of all logs to be displayed, and applies filters if needed
function view_log_list_main() {

    $db = new DB();

    $currentUser = $db->getUserByID($_COOKIE["loggedInUserID"]);

    // Get the current page number and number of records per page from the query string
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $recordsPerPage = 20;

    if (isset($_GET["log"])) {

        $filterByUsername = $filterByType = $filterByTime = $sortBy = "";

        if (isset($_GET["sortBy"])) {
            $sortBy = $_GET["sortBy"];
        }

        if (isset($_SESSION["logSearchUsernameSession"])) {
            $filterByUsername = $_SESSION["logSearchUsernameSession"];
        }

        if (isset($_SESSION["logSearchTypeSession"])) {
            $filterByType = $_SESSION["logSearchTypeSession"];
        }

        if (isset($_SESSION["logSearchTimeSession"])) {
            $filterByTime = $_SESSION["logSearchTimeSession"];
        }

        $logObjects = $db->getLogObjectsByRoleFilteredAsTable($currentUser[0], $currentUser[6], $currentPage, $recordsPerPage, $sortBy, $filterByUsername, $filterByTime, $filterByType);
        $totalRows = $db->getLogObjectsByRoleFilteredCount($currentUser[0], $currentUser[6], $sortBy, $filterByUsername, $filterByTime, $filterByType);
    
    } else {
        
        $logObjects = $db->getLogObjectsByRoleAsTable($currentUser[0], $currentUser[6], $currentPage, $recordsPerPage);
        $totalRows = $db->getLogObjectsByRoleCount($currentUser[0], $currentUser[6]);
    
    }// Ends if

    $totalNumberOfPages = ceil($totalRows / $recordsPerPage);

    view_log_list_table($logObjects, $totalNumberOfPages, $currentPage);
    view_log_list_filter_modal();

} // Ends view_log_list_main()

// Function to create the list of recent logs
function view_log_list_recent() {
    
    $db = new DB();

    $currentUser = $db->getUserByID($_COOKIE["loggedInUserID"]);

    // Get the current page number and number of records per page from the query string
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $recordsPerPage = 20;

    $totalRows = $db->getActivityLogObjectsCount($currentUser[0]);
    $totalNumberOfPages = ceil($totalRows / $recordsPerPage);
    
    // Get the log objects for the current page
    $logObjects = $db->getActivityLogObjectsAsTable($currentUser[0], $currentPage, $recordsPerPage);

    view_log_list_table($logObjects, $totalNumberOfPages, $currentPage);
    view_log_list_filter_modal();

} // Ends view_log_list_recent

// Function to create the list of logs created within a specific timeframe
function view_log_list_created_today() {
    
    $db = new DB();

    $currentUser = $db->getUserByID($_COOKIE["loggedInUserID"]);

    // Get the current page number and number of records per page from the query string
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $recordsPerPage = 20;
    
    // Get the log objects for the current page
    if (isset($_GET['today'])) {
        $logObjects = $db->getLogsCreatedTimeframeTable($currentUser[0], $currentUser[6], "day", $currentPage, $recordsPerPage);
        $totalRows = $db->getLogsCreatedTimeframeCount($currentUser[0], $currentUser[6], "day");
    } else if (isset($_GET['week'])) {
        $logObjects = $db->getLogsCreatedTimeframeTable($currentUser[0], $currentUser[6], "week", $currentPage, $recordsPerPage);
        $totalRows = $db->getLogsCreatedTimeframeCount($currentUser[0], $currentUser[6], "week");
    } else if (isset($_GET['month'])) {
        $logObjects = $db->getLogsCreatedTimeframeTable($currentUser[0], $currentUser[6], "month", $currentPage, $recordsPerPage);
        $totalRows = $db->getLogsCreatedTimeframeCount($currentUser[0], $currentUser[6], "month");
    } // Ends if

    $totalNumberOfPages = ceil($totalRows / $recordsPerPage);

    view_log_list_table($logObjects, $totalNumberOfPages, $currentPage);
    view_log_list_filter_modal();

} // Ends view_log_list_recent

// Function to create and display the table of logs
function view_log_list_table($logObjects, $totalNumberOfPages, $currentPage) {

    $checkedRecent = $checkedStudent = $checkedType = "\"";

    if (isset($_GET["sortBy"])) {

        $sortBy = $_GET["sortBy"];

        switch ($sortBy) {

            case "mostRecent":
                $checkedRecent = "checked";
                break;
            case "student":
                $checkedStudent = "checked";
                break;
            case "type":
                $checkedType = "checked";
                break;

        } // Ends switch
        
    } // Ends if

    echo('
    <!--Main layout-->
    <main style="margin-top: 58px">

        <div class="container pt-4 d-flex align-items-center justify-content-between">

            <div class="d-flex align-items-center gap-4">

                <p class="h3 m-auto">Logs</p>

                <!-- Sort By Filter -->
                <div class="dropdown">

                    <a class="btn btn-outline-dark btn-rounded" type="button" id="dropdownMenuButton" data-mdb-toggle="dropdown" aria-expanded="false">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-sort-amount-down-alt fa-lg" ></i>
                            <p class="lh-1 fs-6 m-auto">Sort By</p>
                        </div>
                    </a>

                    <ul class="dropdown-menu sort-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="SortBy" id="MostRecent" onclick="window.location.href=\'https://seniordevteam1.in/views/log_list_ui.php?log&sortBy=mostRecent\'" '.$checkedRecent.' />
                                <label class="form-check-label" for="MostRecent"> Most Recent </label>
                            </div>
                        </li>
                        <li>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="SortBy" id="Student" onclick="window.location.href=\'https://seniordevteam1.in/views/log_list_ui.php?log&sortBy=student\'" '.$checkedStudent.' />
                                <label class="form-check-label" for="Student"> Student </label>
                            </div>
                        </li>
                        <li>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="SortBy" id="Type" onclick="window.location.href=\'https://seniordevteam1.in/views/log_list_ui.php?log&sortBy=type\'" '.$checkedType.' />
                                <label class="form-check-label" for="Type"> Type </label>
                            </div>
                        </li>
                    </ul>

                </div>

                <a class="btn btn-outline-dark btn-rounded ripple-surface" type="button" cursor: pointer; data-mdb-toggle="modal" data-mdb-target="#logModal">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-filter" ></i>
                        <p class="lh-1 fs-6 m-auto">Filter</p>
                    </div>
                </a>
    ');

    // Displays chips for each filter that is added
    if (isset($_SESSION["logSearchGeneralSession"])) {

        if (isset($_SESSION["logSearchTimeSession"])) {

            echo('
                <div class="btn btn-rounded pe-none" type="button" style="background-color: lightblue;">
                    Time: '.$_SESSION["logSearchTimeSession"].'
                </div>
            ');

        } // Ends if

        if (isset($_SESSION["logSearchTypeSession"])) {

            echo('
                <div class="btn btn-rounded pe-none" type="button" style="background-color: lightblue;">
                    Type: '.$_SESSION["logSearchTypeSession"].'
                </div>
            ');

        } // Ends if

        if (isset($_SESSION["logSearchUsernameSession"]) && !empty($_SESSION["logSearchUsernameSession"])) {

            echo('
                <div class="btn btn-rounded pe-none" type="button" style="background-color: lightblue;">
                    Username: '.$_SESSION["logSearchUsernameSession"].'
                </div>
            ');

        } // Ends if

        if (isset($_GET['recent'])) {

            echo('
                <div class="btn btn-rounded" type="button" style="background-color: lightblue;" onclick="window.location.href=\'https://seniordevteam1.in/views/log_list_ui.php\'">
                    Clear Filters
                    <span class="closebtn">&times;</span>
                </div>
            ');

        } else {

            echo('
                <div class="btn btn-rounded" type="button" style="background-color: lightblue;" onclick="window.location.href=\'https://seniordevteam1.in/controllers/filter_controller.php?clearLog\'">
                    Clear Filters
                    <span class="closebtn">&times;</span>
                </div>
            ');

        }

    } // Ends if

    echo('
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

        <!-- Log Table -->
        <div class="container pt-2 long-table-container">
            <div class="table-responsive search-table">

                <table id="logsListTable" class="table table-hover">
                    '.$logObjects.'
                </table>

            </div>
        </div>

    </main>
    ');

} // Ends view_log_list_table

// Function to display the filter modal
function view_log_list_filter_modal() {
    
    echo '
    <div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Log Search Filters</h5>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="https://seniordevteam1.in/controllers/filter_controller.php?setLog" method="post">

                    <div class="modal-body">

                            <!--Log Time Dropdown Filter-->
                            <label for="logSearchTime">Log Time:</label>
                            <select name="logSearchTime" id="searchLogTime">
                                <option value="Any">Any</option>
                                <option value="Last Day">Last Day</option>
                                <option value="Last Three Days">Last 3 Days</option>
                                <option value="Last Week">Last Week</option>
                                <option value="Last Month">Last Month</option>
                            </select>

                            <br>

                            <!--Log Type Dropdown Filter-->
                            <label for="logSearchType">Log Type:</label>
                            <select name="logSearchType" id="searchLogType">
                                <option value="Any">Any</option>
                                <option value="Failed Login">Failed Login</option>
                                <option value="Successful Login">Successful Login</option>
                                <option value="File Created">File Created</option>
                                <option value="File Modified">File Modified</option>
                            </select>

                            <br>

                            <!--Log Type Dropdown Filter-->
                            <label for="logSearchUsername">Username:</label>
                            <input type="search" name="logSearchUsername" id="logUserSearchBar" placeholder="Username">

                            <br>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark btn-rounded" data-mdb-ripple-color="dark" data-mdb-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-rounded btn-primary">Apply Filter</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    ';

} // Ends view_log_list_filter_modal

?>