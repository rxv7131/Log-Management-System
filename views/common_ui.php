<?php

// Function to include things every page needs
function view_common_includes($pathToRoot) {
    session_start();
    require_once $pathToRoot . "models/PDO.DB.class.php";
    require_once $pathToRoot . "views/login_ui.php";
} // Ends view_common_includes

// Function to create the HTML header
function view_common_header() {

    if (!isset($_SESSION['loggedIn']) OR !isset($_COOKIE['loggedInBool'])) {
        header("Location: https://seniordevteam1.in");
        exit;
    } elseif ($_SESSION['loggedIn'] && $_COOKIE['loggedInBool']) {

        echo('
            <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
                    <meta http-equiv="x-ua-compatible" content="ie=edge" />
                    <title>Log Management System</title>
                    <!-- Font Awesome -->
                    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" />
                    <!-- Google Fonts Roboto -->
                    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" />
                    <!-- MDB -->
                    <link rel="stylesheet" href="https://seniordevteam1.in/src/css/mdb.min.css" />
                    <!-- Custom styles -->
                    <link rel="stylesheet" href="https://seniordevteam1.in/src/css/style.css" />
                </head>
                <body>
        ');

    }// Ends if

} // Ends view_common_header

// Function to create the HTML footer
function view_common_footer() {

    echo('
                    </div>
                </main>
                <!--Main layout-->
                <!-- MDB -->
                <script type="text/javascript" src="https://seniordevteam1.in/src/js/mdb.min.js"></script>
                <!-- Custom scripts -->
            
            </body>
      
        </html>
    ');

} // Ends view_common_footer

// Function to display the application navigation
function view_common_navigation($pageName, $showSearchBar, $activeIndex) {

    $db = new DB();
    $currentUser = $db->getUserByID($_COOKIE["loggedInUserID"]);
    $currentUserInitials = substr($currentUser[1], 0, 1) . substr($currentUser[2], 0, 1);

    $activeDash = $activeLogs = $activeStudents = $activeAlerts = "\"";

    switch ($activeIndex) {

        case 0:
            $activeDash = " active\" aria-current=\"true\"";
            break;
        case 1:
            $activeLogs = " active\" aria-current=\"true\"";
            break;
        case 2:
            $activeStudents = " active\" aria-current=\"true\"";
            break;
        case 3:
            $activeAlerts = " active\" aria-current=\"true\"";
            break;

    } // Ends switch

    $alertsBadge = "";
    $alertsCount = $db->getAlertsNotDismissedCount($currentUser[0], $currentUser[6]);
    if ($alertsCount > 0) {
        $alertsBadge = '<span class="badge badge-danger rounded-pill ms-3">'.$alertsCount.'</span>';
    } // Ends if

    $searchUsersLink = "https://seniordevteam1.in/views/student_list_ui.php";
    if ($currentUser[6] == "Professor") {
        $searchUsersLink = "https://seniordevteam1.in/views/student_list_ui.php?group";
    } // Ends if

    echo('
        <!--Main Navigation-->
        <header>
            <!-- Sidebar -->
            <nav id="sidebarMenu" class="collapse d-lg-block sidebar collapse bg-white">

                <div class="position-sticky">

                    <div class="list-group list-group-flush mt-4">

                        <a href="https://seniordevteam1.in/views/dashboard_ui.php" class="list-group-item list-group-item-action py-2 ripple'.$activeDash.'>
                            <i class="far fa-chart-bar fa-fw me-3"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="https://seniordevteam1.in/views/log_list_ui.php" class="list-group-item list-group-item-action py-2 ripple'.$activeLogs.'>
                            <i class="far fa-file-alt fa-fw me-3"></i>
                            <span>Search Logs</span>
                        </a>

                        <a href="'.$searchUsersLink.'" class="list-group-item list-group-item-action py-2 ripple'.$activeStudents.'>
                            <i class="fas fa-users fa-fw me-3"></i>
                            <span>Search Students</span>
                        </a>

                        <a href="https://seniordevteam1.in/views/alerts_ui.php" class="list-group-item list-group-item-action py-2 ripple'.$activeAlerts.'>
                            <i class="fas fa-exclamation-circle fa-fw me-3"></i>
                            <span>Alerts</span>
                            '.$alertsBadge.'
                        </a>

                    </div>

                </div>

            </nav>
            <!-- Sidebar -->
            
            <!-- Navbar -->
            <nav id="main-navbar" class="navbar navbar-expand-lg navbar-light bg-white fixed-top">

                <!-- Container wrapper -->
                <div class="container-fluid">
            
                    <div class="d-flex flex-row">

                        <!-- Toggle button -->
                        <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="fas fa-bars"></i>
                        </button>
                
                        <!-- Brand -->
                        <a class="navbar-brand" href="https://seniordevteam1.in">
                            <img src="https://seniordevteam1.in/src/img/Logo_LMS.svg" height="35" alt="LMS Logo" loading="lazy" />
                        </a>

                    </div>
            
                    <div class="d-flex flex-row gap-3 navbar-title">

                    <h2>'.$pageName.'</h2>
    '); // Ends echo
    
    // Checks boolean to see if the search bar should be displayed
    if ($showSearchBar) {

        echo ('     
                    <!-- Search form -->
                    <form class="d-none d-md-flex input-group w-auto my-auto">
                        <input autocomplete="off" type="search" class="form-control rounded" placeholder="Search" style="min-width: 225px" />
                        <span class="input-group-text border-0">
                            <i class="fas fa-search"></i>
                        </span>
                    </form>
        '); // Ends echo

    } // Ends if

    echo ('     
                    </div>
            
                    <!-- Right links -->
                    <ul class="navbar-nav ms-auto d-flex flex-row flex-grow-1 justify-content-end">

                        <!-- Notification dropdown -->
                        <!-- TBD? -->
                
                        <!-- Avatar -->
                        <li class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle hidden-arrow d-flex align-items-center" href="#" id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                                <button type="button" class="btn btn-primary btn-floating btn-lg">
                                    <p class="avatar">'.$currentUserInitials.'</p>
                                </button>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                                <li><a class="dropdown-item" href="https://seniordevteam1.in/controllers/login_controller.php?logout">Logout</a></li>
                            </ul>

                        </li>
                    </ul>
                </div>
                <!-- Container wrapper -->
            </nav>
            <!-- Navbar -->
        </header>
        <!--Main Navigation-->
    '); // Ends echo

} // Ends view_common_navigation

// Function to return the correct pagination elements
function get_common_pagination($numPages, $currentPage) {

    $links = '';
    $urlParams = $_GET;
    unset($urlParams['page']);

    $paramsString = http_build_query($urlParams);
    $paramsString = $paramsString ? '&' . $paramsString : '';

    $links .= '<li class="page-item active" aria-current="page">';
    $links .= '<a id="page-picker" class="page-link page-picker d-flex" href="#">';
    $links .= '<div class="form-outline">';
    $links .= '<input type="text" id="form12" class="form-control" value="'.$currentPage.'" readonly />';
    $links .= '</div><span class="visually-hidden">(current)</span></a></li>';

    $links .= '<li class="page-item pagination-plain-text"><p class="lh-1 fs-6 pe-0">of</p></li>';

    $links .= '<li class="page-item pagination-plain-text"><p class="lh-1 fs-6">'.$numPages.'</p></li>';

    $prevPage = $currentPage - 1;
    $prevDisabled = ($prevPage < 1) ? 'disabled' : '';
    $links .= '<li class="page-item '.$prevDisabled.'"><a class="page-link" href="?page='.$prevPage.$paramsString.'"><i class="fas fa-chevron-left fa-md"></i></a></li>';

    $nextPage = $currentPage + 1;
    $nextDisabled = ($nextPage > $numPages) ? 'disabled' : '';
    $links .= '<li class="page-item '.$nextDisabled.'"><a class="page-link" href="?page='.$nextPage.$paramsString.'"><i class="fas fa-chevron-right fa-md"></i></a></li>';

    return $links;

} // Ends get_common_pagination