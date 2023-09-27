<?php
// Handles a user trying to navigate to view student details or log details

require_once("validation_controller.php");
require_once("authentication_controller.php");
require_once ("../views/common_ui.php");
view_common_includes("../");
$db = new DB();

// If directed here via clicking on a table item, an activity record is added to the table
// Else, they are sent back to the login or dashboard page
if (isset($_GET["type"]) && isset($_GET["id"])) {

    if ($_GET["type"] == "student") {

        $db->insertActivityViewedStudent($_COOKIE["loggedInUserID"], $_GET["id"]);
        header("Location: https://seniordevteam1.in/views/student_details_ui.php?id={$_GET["id"]}");
        exit;

    } elseif ($_GET["type"] == "log") {

        $db->insertActivityViewedLog($_COOKIE["loggedInUserID"], $_GET["id"]);
        header("Location: https://seniordevteam1.in/views/log_details_ui.php?id={$_GET["id"]}");
        exit;

    } // Ends type if

    header("Location: https://seniordevteam1.in");
    exit;

} else {

    header("Location: https://seniordevteam1.in");
    exit;

} // Ends GET if