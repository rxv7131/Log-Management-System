<?php
// Handles objects for all database items

class Activity {

    private $activityId;
    private $activityUserId;
    private $activityLogId;
    private $activityStudentId;
    private $activityDatetime;

    // This function will be changed depending on the frontend team's styling
    public function getTableData() {

        return "<tr>
            <td>{$this->activityId}</td>
            <td>{$this->activityUserId}</td>
            <td>{$this->activityLogId}</td>
            <td>{$this->activityStudentId}</td>
            <td>{$this->activityDatetime}</td>
        </tr>\n";

    } // Ends getTableData function

    // Getters
    public function getActivityID() { return $this->activityId; }
    public function getActivityUserID() { return $this->activityUserId; }
    public function getActivityLogID() { return $this->activityLogId; }
    public function getActivityStudentID() { return $this->activityStudentId; }
    public function getActivityDatetime() { return $this->activityDatetime; }

    // Setters
    public function setActivityID($activityId) { $this->activityId = $activityId; }
    public function setActivityUserID($activityUserId) { $this->activityUserId = $activityUserId; }
    public function setActivityLogID($activityLogId) { $this->activityLogId = $activityLogId; }
    public function setActivityStudentID($activityStudentId) { $this->activityStudentId = $activityStudentId; }
    public function setActivityDatetime($activityDatetime) { $this->activityDatetime = $activityDatetime; }

} // Ends Activity class

class Alert {

    private $alertId;
    private $alertDescription;
    private $alertDismissed;
    private $alertStudent;

    public function getTableData() {

        return "<tr>
            <td style='vertical-align: middle;'>{$this->alertDescription}</td>
            <td style='vertical-align: middle;'>
                <button type='button' class='btn btn-rounded btn-danger' data-mdb-ripple-color='dark' onclick=\"window.location='https://seniordevteam1.in/controllers/alert_controller.php?type=dismiss&id={$this->alertId}'\">Dismiss</button>
            </td>
        </tr>\n";

    } // Ends getTableData function

    // Getters
    public function getAlertID() { return $this->alertId; }
    public function getAlertDescription() { return $this->alertDescription; }
    public function getAlertDismissed() { return $this->alertDismissed; }
    public function getAlertStudent() { return $this->alertStudent; }

    // Setters
    public function setAlertID($alertId) { $this->alertId = $alertId; }
    public function setAlertDescription($alertDescription) { $this->alertDescription = $alertDescription; }
    public function setAlertDismissed($alertDismissed) { $this->alertDismissed = $alertDismissed; }
    public function setAlertStudent($alertStudent) { $this->alertStudent = $alertStudent; }

} // Ends Alert class

class ClassTable {

    private $classId;
    private $className;
    private $classProfessor;
    private $schoolId;

    // This function will be changed depending on the frontend team's styling
    public function getTableData() {

        return "<tr>
            <td>{$this->classId}</td>
            <td>{$this->className}</td>
            <td>{$this->classProfessor}</td>
            <td>{$this->schoolId}</td>
        </tr>\n";

    } // Ends getTableData function

    // Getters
    public function getClassID() { return $this->classId; }
    public function getClassName() { return $this->className; }
    public function getClassProfessor() { return $this->classProfessor; }
    public function getClassSchoolID() { return $this->schoolId; }

    // Setters
    public function setClassID($classId) { $this->classId = $classId; }
    public function setClassName($className) { $this->className = $className; }
    public function setClassProfessor($classProfessor) { $this->classId = $classProfessor; }
    public function setClassSchoolID($schoolId) { $this->schoolId = $schoolId; }

} // Ends ClassTable class

class ClassEntry {

    private $studentId;
    private $classId;

    // This function will be changed depending on the frontend team's styling
    public function getTableData() {

        return "<tr>
            <td>{$this->studentId}</td>
            <td>{$this->classId}</td>
        </tr>\n";

    } // Ends getTableData function

    // Getters
    public function getClassEntryStudentID() { return $this->studentId; }
    public function getClassEntryClassID() { return $this->classId; }

    // Setters
    public function setClassEntryStudentID($studentId) { $this->studentId = $studentId; }
    public function setClassEntryClassID($classId) { $this->classId = $classId; }

} // Ends ClassEntry class

class File {

    private $fileId;
    private $fileName;
    private $fileTimeCreated;
    private $fileTimeEdited;
    private $fileLocation;
    private $studentId;

    // This function will be changed depending on the frontend team's styling
    public function getTableData() {

        return "<tr>
            <td>{$this->fileName}</td>
            <td>{$this->fileLocation}</td>
            <td>{$this->fileTimeCreated}</td>
            <td>{$this->fileTimeEdited}</td>
        </tr>\n";

    } // Ends getTableData function

    // Getters
    public function getFileID() { return $this->fileId; }
    public function getFileName() { return $this->fileName; }
    public function getFileTimeCreated() { return $this->fileTimeCreated; }
    public function getFileTimeEdited() { return $this->fileTimeEdited; }
    public function getFileLocation() { return $this->fileLocation; }
    public function getFileStudentID() { return $this->studentId; }

    // Setters
    public function setFileID($fileId) { $this->fileId = $fileId; }
    public function setFileName($fileName) { $this->fileName = $fileName; }
    public function setFileTimeCreated($fileTimeCreated) { $this->fileTimeCreated = $fileTimeCreated; }
    public function setFileTimeEdited($fileTimeEdited) { $this->fileTimeEdited = $fileTimeEdited; }
    public function setFileLocation($fileLocation) { $this->fileLocation = $fileLocation; }
    public function setFileStudentID($studentId) { $this->studentId = $studentId; }

} // Ends File class

class Log {

    private $logId;
    private $logType;
    private $logTimeCreated;
    private $loginAttemptId;
    private $studentId;

    public function getTableData() {

        if ($this->logType == 0) {
            $logTypeString = "Login";
        } else if ($this->logType == 1) {
            $logTypeString = "File Created";
        } else if ($this->logType == 2) {
            $logTypeString = "File Modified";
        } // Ends if

        return "<tr>
            <td>{$logTypeString}</td>
            <td>{$this->studentId}</td>
            <td>{$this->logTimeCreated}</td>
        </tr>\n";

    } // Ends getTableData function

    public function getTableLinkingRow() {

        if ($this->logType == 0) {
            $logTypeString = "Login";
        } else if ($this->logType == 1) {
            $logTypeString = "File Created";
        } else if ($this->logType == 2) {
            $logTypeString = "File Modified";
        } // Ends if

        $returnString =
        "<tr onclick=\"window.location='https://seniordevteam1.in/controllers/activity_controller.php?type=log&id={$this->logId}'\">
            <td>{$logTypeString}</td>
            <td>{$this->studentId}</td>
            <td>{$this->logTimeCreated}</td>
        ";

        $returnString .= "
        </tr>\n";

        return $returnString;

    } // Ends getTableLinkingRow function

    // Getters
    public function getLogID() { return $this->logId; }
    public function getLogTimeCreated() { return $this->logTimeCreated; }
    public function getLogType() { return $this->logType; }
    public function getLogLoginAttemptID() { return $this->loginAttemptId; }
    public function getLogStudentID() { return $this->studentId; }

    // Setters
    public function setLogID($logId) { $this->logId = $logId; }
    public function setLogTimeCreated($logTimeCreated) { $this->logTimeCreated = $logTimeCreated; }
    public function setLogType($logType) { $this->logType = $logType; }
    public function setLogLoginAttemptID($loginAttemptId) { $this->loginAttemptId = $loginAttemptId; }
    public function setLogStudentID($studentId) { $this->studentId = $studentId; }

} // Ends Log class

class LoginAttempt {

    private $loginAttemptId;
    private $loginAttemptUsername;
    private $loginAttemptTimeEntered;
    private $loginAttemptSuccess;
    private $studentId;

    public function getTableData($logId) {

        return "<tr onclick=\"window.location='https://seniordevteam1.in/views/log_details_ui.php?id={$logId}'\">
            <td>{$this->loginAttemptUsername}</td>
            <td>{$this->loginAttemptTimeEntered}</td>
            <td>{$this->loginAttemptSuccess}</td>
        </tr>\n";

    } // Ends getTableData function

    // Getters
    public function getLoginAttemptID() { return $this->loginAttemptId; }
    public function getLoginAttemptUsername() { return $this->loginAttemptUsername; }
    public function getLoginAttemptTimeEntered() { return $this->loginAttemptTimeEntered; }
    public function getLoginAttemptSuccess() { return $this->loginAttemptSuccess; }
    public function getLoginAttemptStudentID() { return $this->studentId; }

    // Setters
    public function setloginAttemptID($loginAttemptId) { $this->loginAttemptId = $loginAttemptId; }
    public function setloginAttemptUsername($loginAttemptUsername) { $this->loginAttemptUsername = $loginAttemptUsername; }
    public function setloginAttemptTimeEntered($loginAttemptTimeEntered) { $this->loginAttemptTimeEntered = $loginAttemptTimeEntered; }
    public function setloginAttemptSuccess($loginAttemptSuccess) { $this->loginAttemptSuccess = $loginAttemptSuccess; }
    public function setloginAttemptStudentID($studentId) { $this->studentId = $studentId; }

} // Ends LoginAttempt class

class Student {

    private $studentId;
    private $studentFirstName;
    private $studentMiddleInitial;
    private $studentLastName;
    private $studentUsername;
    private $schoolId;

    // This function will be changed depending on the frontend team's styling
    public function getTableData() {

        $returnString =
        "<tr>
            <td>{$this->studentFirstName}</td>
            <td>{$this->studentMiddleInitial}</td>
            <td>{$this->studentLastName}</td>
            <td>{$this->studentUsername}</td>
            <td>{$this->schoolId}</td>
        ";

        $returnString .= "
        </tr>\n";

        return $returnString;

    } // Ends getTableData function

    public function getTableLinkingRow() {

        $returnString =
        "<tr onclick=\"window.location='https://seniordevteam1.in/controllers/activity_controller.php?type=student&id={$this->studentId}'\">
            <td>{$this->studentFirstName}</td>
            <td>{$this->studentMiddleInitial}</td>
            <td>{$this->studentLastName}</td>
            <td>{$this->studentUsername}</td>
            <td>{$this->schoolId}</td>
        ";

        $returnString .= "
        </tr>\n";

        return $returnString;

    } // Ends getTableLinkingRow function

    // Getters
    public function getStudentID() { return $this->studentId; }
    public function getStudentFirstName() { return $this->studentFirstName; }
    public function getStudentMiddleInitial() { return $this->studentMiddleInitial; }
    public function getStudentLastName() { return $this->studentLastName; }
    public function getStudentUsername() { return $this->studentUsername; }
    public function getStudentSchoolID() { return $this->schoolId; }

    // Setters
    public function setStudentID($studentId) { $this->studentId = $studentId; }
    public function setStudentFirstName($studentFirstName) { $this->studentFirstName = $studentFirstName; }
    public function setStudentMiddleInitial($studentMiddleInitial) { $this->studentMiddleInitial = $studentMiddleInitial; }
    public function setStudentLastName($studentLastName) { $this->studentLastName = $studentLastName; }
    public function setStudentUsername($studentUsername) { $this->studentUsername = $studentUsername; }
    public function setStudentSchoolID($schoolId) { $this->schoolId = $schoolId; }

} // Ends Student class

class School {

    private $schoolId;
    private $schoolName;

    // This function will be changed depending on the frontend team's styling
    public function getTableData() {

        return "<tr>
            <td>{$this->schoolId}</td>
            <td>{$this->schoolName}</td>
        </tr>\n";

    } // Ends getTableData function

    // Getters
    public function getSchoolID() { return $this->schoolId; }
    public function getSchoolName() { return $this->schoolName; }

    // Setters
    public function setSchoolID($schoolId) { $this->schoolId = $schoolId; }
    public function setSchoolName($schoolName) { $this->schoolName = $schoolName; }

} // Ends School class

class User {

    private $userId;
    private $userFirstName;
    private $userLastName;
    private $userEmail;
    private $userUsername;
    private $userPassword;
    private $userClassification;
    private $schoolId;

    // This function will be changed depending on the frontend team's styling
    public function getTableData() {

        return "<tr>
            <td>{$this->userId}</td>
            <td>{$this->userFirstName}</td>
            <td>{$this->userLastName}</td>
            <td>{$this->userEmail}</td>
            <td>{$this->userUsername}</td>
            <td>{$this->userPassword}</td>
            <td>{$this->userClassification}</td>
            <td>{$this->schoolId}</td>
        </tr>\n";

    } // Ends getTableData function

    // Getters
    public function getUserID() { return $this->userId; }
    public function getUserFirstName() { return $this->userFirstName; }
    public function getUserLastName() { return $this->userLastName; }
    public function getUserEmail() { return $this->userEmail; }
    public function getUserUsername() { return $this->userUsername; }
    public function getUserPassword() { return $this->userPassword; }
    public function getUserClassification() { return $this->userClassification; }
    public function getUserSchoolID() { return $this->schoolId; }

    // Setters
    public function setUserID($userId) { $this->userId = $userId; }
    public function setUserFirstName($userFirstName) { $this->userFirstName = $userFirstName; }
    public function setUserLastName($userLastName) { $this->userLastName = $userLastName; }
    public function setUserEmail($userEmail) { $this->userEmail = $userEmail; }
    public function setUserUsername($userUsername) { $this->userUsername = $userUsername; }
    public function setUserPassword($userPassword) { $this->userPassword = $userPassword; }
    public function setUserClassification($userClassification) { $this->userClassification = $userClassification; }
    public function setUserSchoolID($userSchoolId) { $this->schoolId = $userSchoolId; }

} // Ends User class