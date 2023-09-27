<?php
// Handles creating and destroying filter sessions and variables

require_once("validation_controller.php");
require_once("authentication_controller.php");
require_once ("../views/common_ui.php");
view_common_includes("../");
$db = new DB();

// If user is trying to create a filter or clear a filter
if (isset($_GET["setLog"])) {

    create_log_filter_session();
    header("Location: https://seniordevteam1.in/views/log_list_ui.php?log");
    exit;

} elseif (isset($_GET["clearLog"])) {
    
    destroy_log_filter_session();
    header("Location: https://seniordevteam1.in/views/log_list_ui.php");
    exit;

} elseif (isset($_GET["setStudent"])) {

    create_student_filter_session();
    header("Location: https://seniordevteam1.in/views/student_list_ui.php?student");
    exit;

} elseif (isset($_GET["clearStudent"])) {

    destroy_student_filter_session();
    header("Location: https://seniordevteam1.in/views/student_list_ui.php");
    exit;

} // Ends if

// Function to create session variables for a log filter
function create_log_filter_session() {

    $_SESSION["logSearchGeneralSession"] = true;

    if(isset($_POST["logSearchUsername"])) {
        $_SESSION["logSearchUsernameSession"] = sanitize_string($_POST["logSearchUsername"]);
    }

    if(isset($_POST["logSearchType"])) {
        $_SESSION["logSearchTypeSession"] = sanitize_string($_POST["logSearchType"]);
    }

    if(isset($_POST["logSearchTime"])) {
        $_SESSION["logSearchTimeSession"] = sanitize_string($_POST["logSearchTime"]);
    }

} // Ends create_log_filter_session

// Function to destroy session variables for a log filter
function destroy_log_filter_session() {

    unset($_SESSION["logSearchGeneralSession"]);
    unset($_SESSION["logSearchUsernameSession"]);
    unset($_SESSION["logSearchTypeSession"]);
    unset($_SESSION["logSearchTimeSession"]);
    session_write_close();

} // Ends destroy_log_filter_session

// Function to create session variables for a student filter
function create_student_filter_session() {

    $_SESSION["studentSearchGeneralSession"] = true;

    if(isset($_POST["studentSearchUsername"])) {
        $_SESSION["studentSearchUsernameSession"] = sanitize_string($_POST["studentSearchUsername"]);
    }

    if(isset($_POST["studentSearchLastName"])) {
        $_SESSION["studentSearchLastNameSession"] = sanitize_string($_POST["studentSearchLastName"]);
    }

    if(isset($_POST["studentSearchClass"])) {
        $_SESSION["studentSearchClassSession"] = $_POST["studentSearchClass"];
    }

} // Ends create_student_filter_session

// Function to destroy session variables for a student filter
function destroy_student_filter_session() {

    unset($_SESSION["studentSearchGeneralSession"]);
    unset($_SESSION["studentSearchUsernameSession"]);
    unset($_SESSION["studentSearchLastNameSession"]);
    unset($_SESSION["studentSearchClassSession"]);
    session_write_close();

} // Ends destroy_student_filter_session