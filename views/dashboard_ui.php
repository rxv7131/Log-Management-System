<?php

require_once "../views/common_ui.php";
view_common_includes("../");
view_common_header();
view_common_navigation("Dashboard", false, 0);
view_dashboard_main();
view_common_footer();

// Function to display the dashboard UI using the necessary logic
function view_dashboard_main() { 
    
    $db = new DB();

    $currentUser = $db->getUserByID($_COOKIE["loggedInUserID"]);

    $logsCreatedCount = $db->getLogsCreatedTimeframeCount($currentUser[0], $currentUser[6], "day");
    $successfulLoginsCount = $db->getLoginAttemptsTimeframeCount("success", $currentUser[0], $currentUser[6], "day");
    $failedLoginsCount = $db->getLoginAttemptsTimeframeCount("failure", $currentUser[0], $currentUser[6], "day");
    $cardLabel = "Today";
    $cardLink = "today";
    $dropdownToday = $dropdownWeek = $dropdownMonth = "";

    if (isset($_GET["timeframe"])) {

        if ($_GET["timeframe"] == "day") {

            $logsCreatedCount = $db->getLogsCreatedTimeframeCount($currentUser[0], $currentUser[6], "day");
            $successfulLoginsCount = $db->getLoginAttemptsTimeframeCount("success", $currentUser[0], $currentUser[6], "day");
            $failedLoginsCount = $db->getLoginAttemptsTimeframeCount("failure", $currentUser[0], $currentUser[6], "day");
            $cardLabel = "Today";
            $cardLink = "today";
            $dropdownToday = "selected";

        } else if ($_GET["timeframe"] == "week") {
            
            $logsCreatedCount = $db->getLogsCreatedTimeframeCount($currentUser[0], $currentUser[6], "week");
            $successfulLoginsCount = $db->getLoginAttemptsTimeframeCount("success", $currentUser[0], $currentUser[6], "week");
            $failedLoginsCount = $db->getLoginAttemptsTimeframeCount("failure", $currentUser[0], $currentUser[6], "week");
            $cardLabel = "This Week";
            $cardLink = "week";
            $dropdownWeek = "selected";

        } else if ($_GET["timeframe"] == "month") {
            
            $logsCreatedCount = $db->getLogsCreatedTimeframeCount($currentUser[0], $currentUser[6], "month");
            $successfulLoginsCount = $db->getLoginAttemptsTimeframeCount("success", $currentUser[0], $currentUser[6], "month");
            $failedLoginsCount = $db->getLoginAttemptsTimeframeCount("failure", $currentUser[0], $currentUser[6], "month");
            $cardLabel = "This Month";
            $cardLink = "month";
            $dropdownMonth = "selected";

        } // Ends if

    } // Ends if

    echo('
    <!--Main layout-->
    <main style="margin-top: 58px">

        <!--Section: Stat Cards-->
        <div class="container pt-4">
        
            <div class="d-flex flex-column flex-md-row pt-xl-4 justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <p class="h2">Welcome, '.$currentUser[1].' '.$currentUser[2].'</p>
                    <p class="h5">
                        <span class="badge rounded-pill bg-info">'.$currentUser[6].'</span>
                    </p>
                </div>

                <div class="d-flex pt-xl-4 align-items-center gap-3 justify-content-end">
                    <p class="h4 fw-normal text-nowrap m-0">Server Overview</p>
                    <select class="form-select form-select-sm btn-outline btn-rounded fs-5" style="flex-basis: fit-content;" onchange="redirect(this)">
                        <option class="bg-white" value="today" '.$dropdownToday.'>Today</option>
                        <option class="bg-white" value="week" '.$dropdownWeek.'>This Week</option>
                        <option class="bg-white" value="month" '.$dropdownMonth.'>This Month</option>
                    </select>
                </div>
            </div>
            
            <script>
            function redirect(selectElem) {
                var value = selectElem.value;
                if (value == \'today\') {
                    window.location.href = \'https://seniordevteam1.in/views/dashboard_ui.php?timeframe=day\';
                } else if (value == \'week\') {
                    window.location.href = \'https://seniordevteam1.in/views/dashboard_ui.php?timeframe=week\';
                } else if (value == \'month\') {
                    window.location.href = \'https://seniordevteam1.in/views/dashboard_ui.php?timeframe=month\';
                }
            }
            </script>
        
            <div id="dashboard-stats" class="row align-items-stretch pt-xl-4 pt-md-2">
                
                <!--Logs created card-->
                <div class="col col-xl-4 col-md-12 mb-4">
                    <div class="card h-100" type="button">
                        <div class="card-body">
                            <div class="d-flex justify-content-between p-md-1">
                                <div class="d-flex flex-row">
                                    <div class="align-self-center">
                                        <i class="fas fa-chart-line text-info fa-3x me-4"></i>
                                    </div>
                                    <div>
                                        <h4>Server Activity</h4>
                                        <div class="d-flex align-content-center gap-2 flex-wrap">
                                            <p class="mb-0">Logs Created </p>
                                            <span class="badge rounded-pill bg-light border text-black my-auto">'.$cardLabel.'</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="align-self-center">
                                    <h2 class="h1 mb-0">'.$logsCreatedCount.'</h2>
                                </div>
                            </div>
                            <a href="https://seniordevteam1.in/views/log_list_ui.php?'.$cardLink.'" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
        
                <!--Successful logins card-->
                <div class="col col-xl-4 col-md-12 mb-4">
                    <div class="card h-100" type="button">
                        <div class="card-body">
                            <div class="d-flex justify-content-between p-md-1">
                                <div class="d-flex flex-row">
                                    <div class="align-self-center">
                                        <i class="fas fa-user-plus text-success fa-3x me-4"></i>
                                    </div>
                                    <div>
                                        <h4>Logins</h4>
                                        <div class="d-flex align-content-center gap-2 flex-wrap">
                                            <p class="mb-0">Successful Logins </p>
                                            <span class="badge rounded-pill bg-light border text-black my-auto">'.$cardLabel.'</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="align-self-center">
                                    <h2 class="h1 mb-0">'.$successfulLoginsCount.'</h2>
                                </div>
                            </div>
                            <a href="https://seniordevteam1.in/views/login_attempt_list_ui.php?'.$cardLink.'=success" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
        
                <!--Failed logins card-->
                <div class="col col-xl-4 col-md-12 mb-4">
                    <div class="card h-100" type="button">
                        <div class="card-body">
                            <div class="d-flex justify-content-between p-md-1">
                                <div class="d-flex flex-row">
                                    <div class="align-self-center">
                                        <i class="fas fa-exclamation-circle text-danger fa-3x me-4"></i>
                                    </div>
                                    <div>
                                        <h4>Failed Logins</h4>
                                        <div class="d-flex align-content-center gap-2 flex-wrap">
                                            <p class="mb-0">Failed Logins </p>
                                            <span class="badge rounded-pill bg-light border text-black my-auto">'.$cardLabel.'</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="align-self-center">
                                    <h2 class="h1 mb-0">'.$failedLoginsCount.'</h2>
                                </div>
                            </div>
                            <a href="https://seniordevteam1.in/views/login_attempt_list_ui.php?'.$cardLink.'=failure" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
        
            </div>
            <!--Section: Stat Cards-->
        
            <!--Section: Recently Viewed Student Table-->
            <section class="mb-4">
                <div class="card">

                    <div class="card-header table-header text-center py-3">
                        <div class ="table-header-title">
                            <i class="fas fa-users fa-fw me-3"></i>
                            <h5 class="mb-0 text-center">
                                <strong>Recently Viewed Students</strong>
                            </h5>
                        </div>
                        <a href="https://seniordevteam1.in/views/student_list_ui.php?recent" type="button" class="btn btn-floating chevron-btn" data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View More">
                            <i class="fas fa-chevron-right fa-lg"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="studentDashboardTable" class="table table-hover text-nowrap" >'
                                .$db->getActivityRecentStudents($currentUser[0], 5).
                            '</table>
                        </div>
                    </div>

                </div>
            </section>
            <!--Section: Recently Viewed Student Table-->
        
            <!--Section: Recently Viewed Logs Table-->
            <section class="mb-4">
                <div class="card">
                    
                    <div class="card-header table-header text-center py-3">
                        <div class ="table-header-title">
                            <i class="far fa-file-alt fa-fw me-3"></i>
                            <h5 class="mb-0 text-center">
                                <strong>Recently Viewed Logs</strong>
                            </h5>
                        </div>
                        <a href="https://seniordevteam1.in/views/log_list_ui.php?recent" type="button" class="btn btn-floating chevron-btn" data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View More">
                            <i class="fas fa-chevron-right fa-lg"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="logDashboardTable" class="table table-hover text-nowrap" >'
                                .$db->getActivityRecentLogs($currentUser[0], 5).
                            '</table>
                        </div>
                    </div>

                </div>
            </section>
            <!--Section: Recently Viewed Logs Table-->
        
        </div>
        <!--Section: Stat Cards-->

    </main>
    ');

} // Ends view_dashboard_main

?>