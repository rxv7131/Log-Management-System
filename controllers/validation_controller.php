<?php
// Handles validations for the various inputs

require_once "../models/PDO.DB.class.php";
$db = new DB();

// Function to display error message elements on the page
function show_error_element($errorArray) {
    
    echo '<main style="margin-top: 56px">';
    // Loops through error array and displays each as a different message
    foreach ($errorArray as $error) {

        echo '
            <div class="container pt-2">
                <section class="mb-6">
                    <p class="note note-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>'.$error.'</strong>
                    </p>
                </section>
            </div>
        ';
    
    } // Ends foreach

    echo '</main>';

} // Ends show_error_element

// Function to validate correct login credentials were entered. Returns array with error messages if invalid, or empty array if valid
function validate_login() {

    $db = new DB();
    $errorArray = array();
    
    $loginUsername = check_username($_POST["userUsername"]);
    $loginPassword = check_password($_POST["userPassword"]);

    switch ($loginUsername) {
        case 1:
            $errorArray[] = "ERROR: Invalid input for username";
            break;
        case 2:
            $errorArray[] = "ERROR: Username cannot be empty";
            break;
        case 3:
            $errorArray[] = "ERROR: Username cannot include special characters";
            break;
        case 4:
            $errorArray[] = "ERROR: Username cannot exceed 30 characters";
            break;
        default:
            break;
    } // Ends username switch

    switch ($loginPassword) {
        case 1:
            $errorArray[] = "ERROR: Invalid input for password";
            break;
        case 2:
            $errorArray[] = "ERROR: Password cannot be empty";
            break;
        case 3:
            $errorArray[] = "ERROR: Password contains invalid characters";
            break;
        case 4:
            $errorArray[] = "ERROR: Password cannot exceed 30 characters";
            break;
        default:
            break;
    } // Ends password switch

    // Calls function to get a record for a user by username and password
    // If a valid record exists, and array is returned. If not, then a string is returned
    $loggedInUser = $db->getUserInfoByLogin($_POST['userUsername'], $_POST['userPassword']);

    if ( !is_array($loggedInUser) ) {
        $errorArray[] = "ERROR: Invalid login credentials. Please try again.";
    } // Ends if

    // If no errors were found, then this array will be empty
    return $errorArray;

} // Ends validate_login

// Function to validate and sanitize an entered username string
function check_username($stringInput) {

    $sanitizedInput = sanitize_string($stringInput);

    if ($stringInput != $sanitizedInput) {
        $stringInput = 1;
    } elseif (!verify_not_empty($sanitizedInput)) {
        $stringInput = 2;
    } elseif (!verify_is_alphabetical($sanitizedInput)) {
        $stringInput = 3;
    } elseif (!verify_length($sanitizedInput, 30)) {
        $stringInput = 4;
    } else {
        $stringInput = sanitize_string($stringInput);
    }

    return $stringInput;

} // Ends check_username

// Function to validate and sanitize an entered password string
function check_password($stringInput) {

    $sanitizedInput = sanitize_string($stringInput);

    if ($stringInput != $sanitizedInput) {
        $stringInput = 1;
    } elseif (!verify_not_empty($sanitizedInput)) {
        $stringInput = 2;
    } elseif (!verify_is_alpha_numeric_punct($sanitizedInput)) {
        $stringInput = 3;
    } elseif (!verify_length($sanitizedInput, 128)) {
        $stringInput = 4;
    } else {
        $stringInput = sanitize_string($stringInput);
    }

    return $stringInput;

} // Ends check_password

// Function to validate and sanitize an entered number
function check_number($numberInput) {

    if ($numberInput != sanitize_string($numberInput)) {
        $errorCode = 1;
    } elseif (!verify_not_empty(sanitize_string($numberInput))) {
        $errorCode = 2;
    } elseif (!verify_is_numeric(sanitize_string($numberInput))) {
        $errorCode = 3;
    } elseif (!verify_length(sanitize_string($numberInput), 6)) {
        $errorCode = 4;
    } elseif (sanitize_string($numberInput) < 1) {
        $errorCode = 5;
    } else {
        $errorCode = sanitize_string($numberInput);
    }

    return $errorCode;

} // Ends check_number

// Function to validate and sanitize an entered date
function check_date_string($dateInput, $dateAfter = "1/1/1900") {

    $sanitized = sanitize_string($dateInput);

    if ($dateInput != $sanitized) {
        $dateInput = 1;
    } elseif (!verify_not_empty($sanitized)) {
        $dateInput = 2;
    } elseif (!verify_is_date($sanitized)) {
        $dateInput = 3;
    } elseif (!verify_is_date_format($sanitized)) {
        $dateInput = 4;
    } elseif (!verify_length($sanitized, 10)) {
        $dateInput = 5;
    } elseif (strtotime($sanitized) < strtotime($dateAfter)) {
        $dateInput = 6;
    } else {
        $dateInput = sanitize_string($dateInput);
    }

    return $dateInput;

} // Ends check_date_string

// Function to sanitize a string that may have harmful characters
function sanitize_string($input) {
    
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlentities($input);
    $input = strip_tags($input);
    return $input;

} // Ends santize_string

// Function to verify that an input is not empty
function verify_not_empty($input) {

    if (empty($input)) {
        return false;
    } else {
        return true;
    }

} // Ends verify_not_empty

// Function to verify than an input only contains alphabetical characters
function verify_is_alphabetical($input) {

    $reg = "/^[A-Za-z ]+$/";
	return preg_match($reg, $input);

} // Ends verify_is_alphabetical

// Function to verify than an input only contains alphabetical, numeric, or punctuation characters
function verify_is_alpha_numeric_punct($input) {

	$reg = "/^[A-Za-z0-9 _.,!?\"']+$/";
	return(preg_match($reg, $input));

} // Ends verify_is_alpha_numeric_punct

// Function to verify than an input only contains numeric characters
function verify_is_numeric($input) {

    $reg = "/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/";
	return preg_match($reg, $input);

} // Ends verify_is_numeric

// Function to verify an input does not exceed the passed in length
function verify_length($input, $maxLength) {

    if(strlen($input) > $maxLength) {
        return false;
    } else {
        return true;
    }

} // Ends verify_length

// Function to verify than an input is a date
function verify_is_date($input) {

	$reg = "/^(((0?[1-9]|1[012])\/(0?[1-9]|1\d|2[0-8])|(0?[13456789]|1[012])\/(29|30)|(0?[13578]|1[02])\/31)\/(19|[2-9]\d)\d{2}|0?2\/29\/((19|[2-9]\d)(0[48]|[2468][048]|[13579][26])|(([2468][048]|[3579][26])00)))$/";
	return preg_match($reg, $input);

} // Ends verify_is_date

// Function to verify than an input follow the correct date format
function verify_is_date_format($input) {

	$reg = "/(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d/";
	return preg_match($reg, $input);

} // Ends verify_is_date_format
?>