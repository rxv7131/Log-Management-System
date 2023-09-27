<?php

require_once "views/common_ui.php";
view_common_includes("");

// If user is not correctly logged in, displays the login page
// Else if they are correctly logged in, redirect them to the dashboard
if (!isset($_SESSION['loggedIn']) OR !isset($_COOKIE['loggedInBool'])) {
    
    view_login_main();

} elseif ($_SESSION['loggedIn']) {

    // Redirect to dashboard
    header("Location: https://seniordevteam1.in/views/dashboard_ui.php");
    exit;
    
} // Ends if

?>