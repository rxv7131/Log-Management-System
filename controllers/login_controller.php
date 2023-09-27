<?php
// Handles login information to authorize the user or log the user out

require_once("validation_controller.php");
require_once("authentication_controller.php");
require_once ("../views/common_ui.php");
view_common_includes("../");
$db = new DB();

// If user is trying to login, validates and authorizes their login info
// Else if they are trying to logout, destroy the session and cookie variables
if (isset($_GET["login"])) {

    // Calls function to validate login information. Returns empty array if valid, or an array with error messages if invalid
    $errorArray = validate_login();

    if (count($errorArray) == 0) {
        
        // Below used if there is a navigation error
        echo "<h2>".$_POST["userUsername"]."</h2>";
        echo "<h2>".$_POST["userPassword"]."</h2>";

        create_login_session(sanitize_string($_POST["userUsername"]), sanitize_string($_POST["userPassword"]));

        // Redirects to dashboard view
        echo "Session Created";
        header("Location: https://seniordevteam1.in/views/dashboard_ui.php");
        exit;

    } else {

        // Displays login page again and passes the error array in
        view_login_main($errorArray);
    
    } // Ends outer if

} elseif (isset($_GET["logout"])) {

    // Unsets session, sets cookies to expire now, destroys the session, and then redirects to login page
    session_unset();

    foreach($_COOKIE as $k => $v) {
        unset($_COOKIE[$k]);
        $params = session_get_cookie_params();
        setcookie($k, '', 1, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
    header("Location: https://seniordevteam1.in");
    exit;

} // Ends if

// Function that creates a session indicating that the user logged in correctly
function create_login_session($loginUsername, $loginPassword) {

    $db = new DB();
    $loggedInUser = $db->getUserInfoByLogin($loginUsername, $loginPassword);

    // Set timezone
    date_default_timezone_set('EST');

    // Create session
    session_name('LMS_Login');
    session_start();

    // Set cookie params
    //$value = date("F j, Y g:i a");
    $expire = time() + (60*180); // Expires in 3 hours
    $path = "/";
    $domain = "seniordevteam1.in";
    $secure = false;
    //$httponly = true;

    // Set session variable
    $_SESSION['loggedIn'] = true;

    // Set cookies to hold ID and classification of user returned in array from login function
    setcookie("loggedInBool", true, $expire, $path, $domain, $secure);
    setcookie("loggedInUserID", $loggedInUser[0], $expire, $path, $domain, $secure);
    setcookie("loggedInUserClassification", $loggedInUser[1], $expire, $path, $domain, $secure);
    //setcookie("loggedIn", $value, $expire, $path, $domain, $secure, $httponly);

} // Ends create_login_session

?>