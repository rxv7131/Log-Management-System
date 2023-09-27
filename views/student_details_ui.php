<?php

require_once "../views/common_ui.php";
view_common_includes('../');
view_common_header();
view_common_navigation("Student Details", false, 2);
view_log_details_main();
view_common_footer();

// Function to display the log details UI with the necessary logic
function view_log_details_main() {

    $db = new DB();
 
    $currentStudent = $db->getStudentByID($_GET["id"]);
    $currentSchool = $db->getSchoolByID($currentStudent[5]);
    $latestLog = $db->getLogLatestByStudentID($currentStudent[0]);

    if (!is_array($latestLog)) {
        $logTypeString = "NA";
        $logTime = "NA";
        $latestLog = "";
    } elseif (is_array($latestLog)) {

        if ($latestLog[1] == 0) {
            $logTypeString = "Login Attempt";
        } elseif ($latestLog[1] == 1) {
            $logTypeString = "File Creation";
        } elseif ($latestLog[1] == 2) {
            $logTypeString = "File Modification";
        } else {
            $logTypeString = "";
        } // Ends if

        $logTime = $latestLog[2];

    } // Ends if

    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $recordsPerPage = 20;

    $fileObjects = $db->getFileObjectsByStudentAsTable($currentStudent[0], $currentPage, $recordsPerPage);
    $totalRows = $db->getFileObjectsByStudentCount($currentStudent[0]);
    $totalNumberOfPages = ceil($totalRows / $recordsPerPage);

    echo('
    <main style="margin-top: 58px">
        <div class="container pt-4">
            <div class="card">

                <div class="card-header table-header text-center py-3">
                    <div class ="table-header-title">
                        <i class="fas fa-user fa-fw me-3"></i>
                        <h5 class="mb-0 text-center">
                            <strong>Student ID: '.$currentStudent[0].'</strong>
                        </h5>
                    </div>
                    <form action="https://seniordevteam1.in/controllers/filter_controller.php?setLog" method="POST">
                        <input type="hidden" name="logSearchUsername" value="'.$currentStudent[4].'">
                        <input type="hidden" name="logSearchTime" value="Any">
                        <input type="hidden" name="logSearchType" value="Any">
                        <button type="submit" class="btn btn-outline-dark btn-rounded font-weight-bold" data-mdb-ripple-color="dark">
                            View Logs
                        </button>
                    </form>
                </div>

                <div id="student-card-body" class="card-body d-flex flex-column">

                    <div class="d-flex gap-2">
                        <p class="h6 lh-1">Full Name:</p>
                        <p class="fs-6 lh-1">'.$currentStudent[1].' '.$currentStudent[2].' '.$currentStudent[3].'</p>
                    </div>

                    <div class="d-flex gap-2">
                        <p class="h6 lh-1">Student Username:</p>
                        <p class="fs-6 lh-1">'.$currentStudent[4].'</p>
                    </div>

                    <div class="d-flex gap-2">
                        <p class="h6 lh-1">School:</p>
                        <p class="fs-6 lh-1">'.$currentSchool[1].'</p>
                    </div>

                    <div class="d-flex gap-2">
                        <p class="h6 lh-1">Lastest Log Time:</p>
                        <p class="fs-6 lh-1">'.$logTime.'</p>
                    </div>

                    <div class="d-flex gap-2">
                        <p class="h6 lh-1">Lastest Log Type:</p>
                        <p class="fs-6 lh-1">'.$logTypeString.'</p>
                    </div>

                </div>

            </div>

            <div class="container pt-4 d-flex align-items-center justify-content-between">
                
                <div class="d-flex align-items-center gap-2">
                    <p class="h5">Files from this student:</p>
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

            <!-- File Table -->
            <div class="container pt-2 long-table-container">
                <div class="table-responsive search-table">

                    <table id="singleStudentListTable" class="table">
                        '.$fileObjects.'
                    </table>

                </div>
            </div>

        </div>
    </main>
    ');

} // Ends view_log_details_main