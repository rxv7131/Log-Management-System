<?php

require_once "../views/common_ui.php";
require_once "../controllers/validation_controller.php";
view_common_includes('../');
view_common_header();
view_common_navigation("Search Student", false, 2);

// If the user is trying to view recently viewed students, students categorized by class, or all students
if (isset($_GET['recent'])) {
    view_student_list_recent();
} elseif (isset($_GET['group'])) {
    view_student_list_by_class();
} else {
    view_student_list_main();
} // Ends if

view_common_footer();

// Function to create the list of all students appropriate for the user
function view_student_list_main() {

    $db = new DB();

    $currentUser = $db->getUserByID($_COOKIE["loggedInUserID"]);

    // Get the current page number and number of records per page from the query string
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $recordsPerPage = 20;

    if (isset($_GET["student"])) {

        $filterByUsername = $filterByClass = $filterByLastName = $sortBy = "";

        if (isset($_GET["sortBy"])) {
            $sortBy = $_GET["sortBy"];
        }

        if (isset($_SESSION["studentSearchUsernameSession"])) {
            $filterByUsername = $_SESSION["studentSearchUsernameSession"];
        }

        if (isset($_SESSION["studentSearchLastNameSession"])) {
            $filterByLastName = $_SESSION["studentSearchLastNameSession"];
        }

        if (isset($_SESSION["studentSearchClassSession"])) {
            $filterByClass = $_SESSION["studentSearchClassSession"];
        }

        $studentObjects = $db->getStudentObjectsByRoleFilteredAsTable($currentUser[0], $currentUser[6], $currentPage, $recordsPerPage, $sortBy, $filterByUsername, $filterByClass, $filterByLastName);
        $totalRows = $db->getStudentObjectsByRoleFilteredCount($currentUser[0], $currentUser[6], $sortBy, $filterByUsername, $filterByClass, $filterByLastName);
    
    } else {
        
        $studentObjects = $db->getStudentObjectsByRoleAsTable($currentUser[0], $currentUser[6], $currentPage, $recordsPerPage);
        $totalRows = $db->getStudentObjectsByRoleCount($currentUser[0], $currentUser[6]);
    
    }// Ends if

    $totalNumberOfPages = ceil($totalRows / $recordsPerPage);

    $classArray = $db->getClassArray($currentUser[0], $currentUser[6]);

    view_student_list_table($studentObjects, $totalNumberOfPages, $currentPage);
    view_student_list_filter_modal($classArray);

} // Ends view_student_list_main()

// Function to create the list of recently viewed students
function view_student_list_recent() {
    
    $db = new DB();

    $currentUser = $db->getUserByID($_COOKIE["loggedInUserID"]);

    // Get the current page number and number of records per page from the query string
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $recordsPerPage = 20;

    $totalRows = $db->getActivityStudentObjectsCount($currentUser[0]);
    $totalNumberOfPages = ceil($totalRows / $recordsPerPage);
    
    // Get the student objects for the current page
    $studentObjects = $db->getActivityStudentObjectsAsTable($currentUser[0], $currentPage, $recordsPerPage);

    $classArray = $db->getClassArray($currentUser[0], $currentUser[6]);

    view_student_list_table($studentObjects, $totalNumberOfPages, $currentPage);
    view_student_list_filter_modal($classArray);

} // Ends view_student_list_recent

// Function to create and display the table of students
function view_student_list_table($studentObjects, $totalNumberOfPages, $currentPage) {

    $db = new DB();
    $checkedID = $checkedUsername = $checkedSchool = $checkedLastName = "\"";

    if (isset($_GET["sortBy"])) {

        $sortBy = $_GET["sortBy"];

        switch ($sortBy) {

            case "id":
                $checkedID = "checked";
                break;
            case "username":
                $checkedUsername = "checked";
                break;
            case "school":
                $checkedSchool = "checked";
                break;
            case "lastName":
                $checkedLastName = "checked";
                break;

        } // Ends switch
        
    } // Ends if

    echo('
    <!--Main layout-->
    <main style="margin-top: 58px">

        <div class="container pt-4 d-flex align-items-center justify-content-between">

            <div class="d-flex align-items-center gap-4">

                <p class="h3 m-auto">Students</p>

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
                                <input class="form-check-input" type="radio" name="SortBy" id="StudentID" onclick="window.location.href=\'https://seniordevteam1.in/views/student_list_ui.php?student&sortBy=id\'" '.$checkedID.' />
                                <label class="form-check-label" for="MostRecent"> ID </label>
                            </div>
                        </li>
                        <li>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="SortBy" id="Username" onclick="window.location.href=\'https://seniordevteam1.in/views/student_list_ui.php?student&sortBy=username\'" '.$checkedUsername.' />
                                <label class="form-check-label" for="Username"> Username </label>
                            </div>
                        </li>
                        <li>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="SortBy" id="School" onclick="window.location.href=\'https://seniordevteam1.in/views/student_list_ui.php?student&sortBy=school\'" '.$checkedSchool.' />
                                <label class="form-check-label" for="School"> School </label>
                            </div>
                        </li>
                        <li>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="SortBy" id="LastName" onclick="window.location.href=\'https://seniordevteam1.in/views/student_list_ui.php?student&sortBy=lastName\'" '.$checkedLastName.' />
                                <label class="form-check-label" for="LastName"> Last Name </label>
                            </div>
                        </li>
                    </ul>

                </div>

                <a class="btn btn-outline-dark btn-rounded ripple-surface" type="button" cursor: pointer; data-mdb-toggle="modal" data-mdb-target="#studentModal">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-filter" ></i>
                        <p class="lh-1 fs-6 m-auto">Filter</p>
                    </div>
                </a>
    ');

    // Displays chips for each filter that is added
    if (isset($_SESSION["studentSearchGeneralSession"])) {

        if (isset($_SESSION["studentSearchUsernameSession"]) && !empty($_SESSION["studentSearchUsernameSession"])) {

            echo('
                <div class="btn btn-rounded pe-none" type="button" style="background-color: lightblue;">
                    Username: '.$_SESSION["studentSearchUsernameSession"].'
                </div>
            ');

        } // Ends if

        if (isset($_SESSION["studentSearchLastNameSession"]) && !empty($_SESSION["studentSearchLastNameSession"])) {

            echo('
                <div class="btn btn-rounded pe-none" type="button" style="background-color: lightblue;">
                    Last Name: '.$_SESSION["studentSearchLastNameSession"].'
                </div>
            ');

        } // Ends if

        if (isset($_SESSION["studentSearchClassSession"]) && !empty($_SESSION["studentSearchClassSession"])) {

            echo('
                <div class="btn btn-rounded pe-none" type="button" style="background-color: lightblue;">
                    Class: '.$db->getClassAbbreviationByID($_SESSION["studentSearchClassSession"]).'
                </div>
            ');

        } // Ends if

        if (isset($_GET['recent'])) {

            echo('
                <div class="btn btn-rounded" type="button" style="background-color: lightblue;" onclick="window.location.href=\'https://seniordevteam1.in/views/student_list_ui.php\'">
                    Clear Filters
                    <span class="closebtn">&times;</span>
                </div>
            ');

        } else {

            echo('
                <div class="btn btn-rounded" type="button" style="background-color: lightblue;" onclick="window.location.href=\'https://seniordevteam1.in/controllers/filter_controller.php?clearStudent\'">
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

        <!-- Student Table -->
        <div class="container pt-2 long-table-container">
            <div class="table-responsive search-table">

                <table id="studentsListTable" class="table table-hover">
                    '.$studentObjects.'
                </table>

            </div>
        </div>

    </main>
    ');

} // Ends view_student_list_table

// Function to display the filter modal
function view_student_list_filter_modal($classArray) {

    echo '
    <div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="https://seniordevteam1.in/controllers/filter_controller.php?setStudent" method="post">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Student Search Filters</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                            <label for="studentSearchUsername">Username:</label>
                            <input name="studentSearchUsername" type="search" id="studentSearchBar" placeholder="Username">
                            
                            <br />

                            <label for="studentSearchLastName">Last Name:</label>
                            <input name="studentSearchLastName" type="search" id="studentSearchBar" placeholder="Last Name">
                            
                            <br />

                            <!--Class Dropdown Filter-->
                            <label for="studentSearchClass" style="padding-top:1em">Class:</label>
                            <select name="studentSearchClass" id="studentSearchClass" style="width: 200px; overflow-wrap: break-word; word-wrap: break-word;">
    ';

    $classDropdown = "";
    foreach ($classArray as $class) {
        $classID = $class['classId'];
        $className = $class['className'];
        $classDropdown .= '<option value="'.$classID.'">'.$className.'</option>';
    } // Ends foreach

    echo $classDropdown;
                                
    echo '
                            </select>

                            <br />

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

} // Ends view_student_list_filter_modal

// Function to display the UI for a professor to be able to select which of their classes they would like to view students by
function view_student_list_by_class() {

    $db = new DB();

    $currentUser = $db->getUserByID($_COOKIE["loggedInUserID"]);
    $classArray = $db->getClassArray($currentUser[0], $currentUser[6]);

    $classButtonOutput = "";
    foreach ($classArray as $class) {
        $classID = $class['classId'];
        $className = $class['className'];
        $classButtonOutput .= '
            <form id="class-form'.$classID.'" action="https://seniordevteam1.in/controllers/filter_controller.php?setStudent" method="post">
                <div class="card h-100" onclick="document.getElementById(\'class-form'.$classID.'\').submit();">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <input type="hidden" name="studentSearchClass" value="'.$classID.'">
                        <p class="my-auto fs-6">'.$className.'</p>
                        <i class="fas fa-chevron-right fa-md"></i>
                    </div>
                </div>
            </form>
        ';
    }

    echo('
        <!--Main layout-->
        <main style="margin-top: 58px">
            <div class="container pt-5">
                <div id="classes" class="container d-flex flex-column gap-5">

                    <div class="d-flex flex-column gap-2">
                        <p class="h5">View Students by Class:</p>

                        '.$classButtonOutput.'

                    </div>

                    <div class="card h-100">
                        <div class="card-body d-flex justify-content-between align-items-center" type="button" onclick="window.location.href=\'https://seniordevteam1.in/views/student_list_ui.php\'">
                            <h5 class="my-auto">View All</h5>
                            <i class="fas fa-chevron-right fa-md"></i>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    ');

} // Ends view_student_list_by_class

?>