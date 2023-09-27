<?php 

class DB {

    private $dbh;

/********************************GENERAL FUNCTIONS*************************************/
    
    function __construct() {

        require '../models/pdo-config.php';

        try {

            $this->dbh = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Message displayed on successful connection
            // echo("Connected to $dbname at $host successfully.");

        } catch(PDOException $pe) {
            
            // Message displayed on failed connection
            die("Could not connect to the database $dbname :" . $pe->getMessage());
        
        } // Ends try catch

    } // Ends __construct

    // Function to call databse with the provided query and provides results as objects
    function getAllObjects($stmtInput, $classInput) {

        $data = array();

        try {

            require_once "../controllers/DB.Controller.class.php";

            $stmt = $this->dbh->prepare($stmtInput);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS,$classInput);

            while ($row=$stmt->fetch()) {
                $data[] = $row;
            } // Ends while

            return $data;

        } catch(PDOException $pe) {
            echo $pe->getMessage();
            return array();
        } // Ends try catch

    } // Ends getAllObjects

/********************************ACTIVITY FUNCTIONS*************************************/

    // Returns a table with all of the information from the activity table
    public function getAllActivityObjectsAsTable() {

        $data = $this->getAllObjects("SELECT * FROM activity", "Activity");

        if (count($data) > 0) {
            
            $outputTable = "<thead><tr>
                            <th>Activity ID</th>
                            <th>Activity User ID</th>
                            <th>Activyty Log ID</th>
                            <th>Activity Student ID</th>
                            <th>Activity Datetime</th>
            </tr></thead>\n";

            foreach ($data as $activity) {

                $outputTable .= $activity->getTableData();

            } // Ends activity foreach

        } else {
            $outputTable = "<h2>No activities exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getAllActivityObjectsAsTable

    // Returns a table of recently viewed students. userID is the ID of the user you are trying to get the table for, and limitNum is the number limit of row you want to get, or can be set to 0 to get all records.
    public function getActivityRecentStudents($userID, $limitNum) {

        if ($limitNum == 0) {
            $data = $this->getAllObjects("SELECT * FROM activity WHERE activityUserId = $userID AND activityLogId IS NULL ORDER BY activityDatetime DESC", "Activity");
        } else {
            $data = $this->getAllObjects("SELECT * FROM activity WHERE activityUserId = $userID AND activityLogId IS NULL ORDER BY activityDatetime DESC LIMIT $limitNum", "Activity");
        } // Ends limit if

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Student First Name</th>
                            <th>Student Middle Initial</th>
                            <th>Student Last Name</th>
                            <th>Student Username</th>
                            <th>Student School</th>
            </tr></thead>\n";
    
            foreach ($data as $activity) {

                $activityStudentID = $activity->getActivityStudentID();
                $activityStudentObject = $this->getAllObjects("SELECT * FROM student WHERE studentId = $activityStudentID", "Student");

                foreach ($activityStudentObject as $student) {

                    $studentSchoolID = $student->getStudentSchoolID();
                    $schoolObject = $this->getAllObjects("SELECT * FROM school WHERE schoolId = $studentSchoolID", "School");
                
                    foreach ($schoolObject as $school) {

                        $studentSchoolName = $school->getSchoolName();
                        $student->setStudentSchoolID($studentSchoolName);

                    } // Ends school foreach

                    $outputTable .= $student->getTableLinkingRow();

                } // Ends student foreach
            
            } // Ends activity foreach
    
        } else {
            $outputTable = "<h2>You have not previously viewed any students...</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getAllActivityRecentStudents

    // Returns a table of recently viewed logs. userID is the ID of the user you are trying to get the table for, and limitNum is the number limit of row you want to get, or can be set to 0 to get all records.
    public function getActivityRecentLogs($userID, $limitNum) {

        if ($limitNum == 0) {
            $data = $this->getAllObjects("SELECT * FROM activity WHERE activityUserId = $userID AND activityStudentId IS NULL ORDER BY activityDatetime DESC", "Activity");
        } else {
            $data = $this->getAllObjects("SELECT * FROM activity WHERE activityUserId = $userID AND activityStudentId IS NULL ORDER BY activityDatetime DESC LIMIT $limitNum", "Activity");
        } // Ends limit if

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Log Type</th>
                            <th>Associated Student Username</th>
                            <th>Log Time Created</th>
            </tr></thead>\n";
    
            foreach ($data as $activity) {

                $activityLogID = $activity->getActivityLogID();
                $activityLogObject = $this->getAllObjects("SELECT * FROM log WHERE logId = $activityLogID", "Log");

                foreach ($activityLogObject as $log) {

                    $logStudentID = $log->getLogStudentID();
                    $studentObject = $this->getAllObjects("SELECT * FROM student WHERE studentId = $logStudentID", "Student");
                
                    foreach ($studentObject as $student) {

                        $logStudentUsername = $student->getStudentUsername();
                        $log->setLogStudentID($logStudentUsername);

                    } // Ends student foreach

                    $outputTable .= $log->getTableLinkingRow();

                } // Ends log foreach
            
            } // Ends activity foreach
    
        } else {
            $outputTable = "<h2>You have not previously viewed any logs...</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getAllActivityRecentLogs

    // Returns the logs appropriate for the specific user. Used for pagination
    public function getActivityLogObjectsAsTable($userID, $currentPageNumber, $recordsPerPage) {

        $offset = ($currentPageNumber - 1) * $recordsPerPage;

        $data = $this->getAllObjects("SELECT * FROM activity WHERE activityUserId = $userID 
        AND activityStudentId IS NULL ORDER BY activityDatetime DESC 
        LIMIT $offset, $recordsPerPage", "Activity");

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Log Type</th>
                            <th>Log Time Created</th>
                            <th>Student Username</th>
            </tr></thead>\n";
    
            foreach ($data as $activity) {

                $activityLogID = $activity->getActivityLogID();
                $activityLogObject = $this->getAllObjects("SELECT * FROM log WHERE logId = $activityLogID", "Log");

                foreach ($activityLogObject as $log) {

                    $logStudentID = $log->getLogStudentID();
                    $studentObject = $this->getAllObjects("SELECT * FROM student WHERE studentId = $logStudentID", "Student");
                
                    foreach ($studentObject as $student) {

                        $logStudentUsername = $student->getStudentUsername();
                        $log->setLogStudentID($logStudentUsername);

                    } // Ends student foreach

                    $outputTable .= $log->getTableLinkingRow();

                } // Ends log foreach
            
            } // Ends activity foreach
    
        } else {
            $outputTable = "<h3>You have not previously viewed any logs...</h3>";
        }// Ends if

        return $outputTable;

    } // Ends getActivityLogObjectsAsTable

    // Gets the count of all log activity records associated with a user
    public function getActivityLogObjectsCount($userID) {

        $data = $this->getAllObjects("SELECT * FROM activity WHERE activityUserId = $userID 
        AND activityStudentId IS NULL", "Activity");

        if (count($data) > 0) {
            return count($data);
        } else {
            return 0;
        } // Ends if

    } // Ends getActivityLogObjectsCount

    // Returns the students appropriate for the specific user. Used for pagination
    public function getActivityStudentObjectsAsTable($userID, $currentPageNumber, $recordsPerPage) {

        $offset = ($currentPageNumber - 1) * $recordsPerPage;

        $data = $this->getAllObjects("SELECT * FROM activity WHERE activityUserId = $userID AND activityLogId IS NULL 
        ORDER BY activityDatetime DESC LIMIT $offset, $recordsPerPage", "Activity"); 

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Student First Name</th>
                            <th>Student Middle Initial</th>
                            <th>Student Last Name</th>
                            <th>Student Username</th>
                            <th>Student School</th>
            </tr></thead>\n";

            foreach ($data as $activity) {

                $activityStudentID = $activity->getActivityStudentID();
                $activityStudentObject = $this->getAllObjects("SELECT * FROM student WHERE studentId = $activityStudentID", "Student");

                foreach ($activityStudentObject as $student) {

                    $studentSchoolID = $student->getStudentSchoolID();
                    $schoolObject = $this->getAllObjects("SELECT * FROM school WHERE schoolId = $studentSchoolID", "School");
                
                    foreach ($schoolObject as $school) {

                        $studentSchoolName = $school->getSchoolName();
                        $student->setStudentSchoolID($studentSchoolName);

                    } // Ends school foreach

                    $outputTable .= $student->getTableLinkingRow();

                } // Ends student foreach
            
            } // Ends activity foreach

        } else {
            $outputTable = "<h2>You have not previously viewed any students...</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getActivityStudentObjectsAsTable

    // Gets the count of all student activity records associated with a user
    public function getActivityStudentObjectsCount($userID) {

        $data = $this->getAllObjects("SELECT * FROM activity WHERE activityUserId = $userID AND activityLogId IS NULL", "Activity");

        if (count($data) > 0) {
            return count($data);
        } else {
            return 0;
        } // Ends if

    } // Ends getActivityStudentObjectsCount

    // Inserts a record with the current user ID and the ID of the student the user was viewing. Updates the record if the user has previously viewed the student
    public function insertActivityViewedStudent($userID, $studentID) {

        require_once("DB.Controller.class.php");

        $Activity = new Activity;
        $Activity->setActivityUserID($userID);
        $Activity->setActivityStudentID($studentID);

        $data = $this->getAllObjects("SELECT * FROM activity WHERE activityUserId = $userID AND activityStudentId = $studentID", "Activity");

        try {

            if (count($data) > 0) {

                $stmt = $this->dbh->prepare("
                    UPDATE activity
                    activityUserId = :activityUserId, activityStudentId = :activityStudentId, activityDatetime = NOW()
                    WHERE activityUserId = :activityUserId AND activityStudentId = :activityStudentId
                ");

                $stmt->execute(array(
                    "activityUserId"=>$Activity->getActivityUserID(),
                    "activityStudentId"=>$Activity->getActivityStudentID()
                ));

            } else {

                $stmt = $this->dbh->prepare("
                    INSERT INTO activity (activityUserId, activityStudentId)
                    VALUES (:activityUserId, :activityStudentId)
                ");

                $stmt->execute(array(
                    "activityUserId"=>$Activity->getActivityUserID(),
                    "activityStudentId"=>$Activity->getActivityStudentID()
                ));

            } // Ends if

        } catch (PDOException $pe) {
            echo $pe->getMessage();
            return -1;
        } // Ends try catch

    } // Ends insertActivityViewedStudent function

    // Inserts a record with the current user ID and the ID of the log the user was viewing
    public function insertActivityViewedLog($userID, $logID) {

        require_once("DB.Controller.class.php");

        $Activity = new Activity;
        $Activity->setActivityUserID($userID);
        $Activity->setActivityLogID($logID);

        $data = $this->getAllObjects("SELECT * FROM activity WHERE activityUserId = $userID AND activityLogId = $logID", "Activity");

        try {

            if (count($data) > 0) {

                $stmt = $this->dbh->prepare("
                    UPDATE activity
                    SET activityUserId = :activityUserId, activityLogId = :activityLogId, activityDatetime = NOW()
                    WHERE activityUserId = :activityUserId AND activityLogId = :activityLogId
                ");

                $stmt->execute(array(
                    "activityUserId"=>$Activity->getActivityUserID(),
                    "activityLogId"=>$Activity->getActivityLogID()
                ));

            } else {

                $stmt = $this->dbh->prepare("
                    INSERT INTO activity (activityUserId, activityLogId)
                    VALUES (:activityUserId, :activityLogId)
                ");

                $stmt->execute(array(
                    "activityUserId"=>$Activity->getActivityUserID(),
                    "activityLogId"=>$Activity->getActivityLogID()
                ));

            } // Ends if

        } catch (PDOException $pe) {
            echo $pe->getMessage();
            return -1;
        } // Ends try catch

    } // Ends insertActivityViewedLog function

/********************************ALERT FUNCTIONS*************************************/    

    // Returns a table with all of the information from the alert table
    public function getAllAlertObjectsAsTable() {

        $data = $this->getAllObjects("SELECT * FROM alert", "Alert");

        if (count($data) > 0) {
            
            $outputTable = "<thead><tr>
                            <th>Alert Description</th>
                            <th></th>
            </tr></thead>\n";

            foreach ($data as $alert) {

                $outputTable .= $alert->getTableData();

            } // Ends alert foreach

        } else {
            $outputTable = "<h3>No alerts exist.</h3>";
        }// Ends if

        return $outputTable;

    } // Ends getAllAlertObjectsAsTable

    // Gets all the relevant alerts for a user that have not been dismissed
    public function getAlertsNotDismissedAsTable($userID, $userType, $currentPageNumber, $recordsPerPage) {

        $offset = ($currentPageNumber - 1) * $recordsPerPage;

        if ($userType == "Admin") {

            $data = $this->getAllObjects("SELECT * FROM alert 
            WHERE alertDismissed = 0 AND alertStudent = 0 
            LIMIT $offset, $recordsPerPage", "Alert");

        } else if ($userType == "Professor") {

            $data = $this->getAllObjects("SELECT alert.* FROM alert
            WHERE alert.alertDismissed = 0
            AND alert.alertStudent IN (
                SELECT student.studentId FROM student
                INNER JOIN classEntry ON student.studentId = classEntry.studentId 
                INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
            )
            LIMIT $offset, $recordsPerPage", "Alert");

        } else if ($userType == "Support") {

            $data = $this->getAllObjects("SELECT alert.* FROM alert
            WHERE alert.alertDismissed = 0
            AND alert.alertStudent IN (
                SELECT student.studentId FROM student
                INNER JOIN school ON student.schoolId = school.schoolId 
                INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
            )
            LIMIT $offset, $recordsPerPage", "Alert");

        } // Ends if

        if (count($data) > 0) {
            
            $outputTable = "<thead><tr>
                            <th>Alert Description</th>
                            <th></th>
            </tr></thead>\n";

            foreach ($data as $alert) {

                $outputTable .= $alert->getTableData();

            } // Ends alert foreach

        } else {
            $outputTable = "<h4>No new alerts.</h4>";
        }// Ends if

        return $outputTable;

    } // Ends getAlertsNotDismissedAsTable

    // Gets the count of relevant alerts for a user that have not been dismissed
    public function getAlertsNotDismissedCount($userID, $userType) {

        if ($userType == "Admin") {

            $data = $this->getAllObjects("SELECT * FROM alert 
            WHERE alertDismissed = 0 AND alertStudent = 0", "Alert");

        } else if ($userType == "Professor") {

            $data = $this->getAllObjects("SELECT alert.* FROM alert
            WHERE alert.alertDismissed = 0
            AND alert.alertStudent IN (
                SELECT student.studentId FROM student
                INNER JOIN classEntry ON student.studentId = classEntry.studentId 
                INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
            )", "Alert");

        } else if ($userType == "Support") {

            $data = $this->getAllObjects("SELECT alert.* FROM alert
            WHERE alert.alertDismissed = 0
            AND alert.alertStudent IN (
                SELECT student.studentId FROM student
                INNER JOIN school ON student.schoolId = school.schoolId 
                INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
            )", "Alert");

        } // Ends if

        if (count($data) > 0) {
            return count($data);
        } else {
            return 0;
        } // Ends if

    } // Ends getAlertsNotDismissedCount

    // Gets the details for a single alert
    public function getAlertById($alertID) {

            $data = $this->getAllObjects("SELECT * FROM alert WHERE alertId = '$alertID'", "Alert");
    
            if (count($data) > 0) {
    
                $outputAlert[] = $data[0]->getAlertID();
                $outputAlert[] = $data[0]->getAlertDescription();
                $outputAlert[] = $data[0]->getAlertDismissed();
                $outputAlert[] = $data[0]->getAlertStudent();
        
            } elseif (count($data) > 1) {
    
                $outputAlert = "ERROR500";
    
            } else {
    
                $outputAlert = "ERROR404";
    
            }// Ends if
    
            return $outputAlert;

    } // Ends getAlertById

    // Marks the alert as dismissed
    public function updateAlertDismiss($alertID) {

        require_once("DB.Controller.class.php");

        $currentAlert = $this->getAlertById($alertID);

        $Alert = new Alert;
        $Alert->setAlertID($alertID);
        $Alert->setAlertDescription($currentAlert[1]);
        $Alert->setAlertDismissed(1);
        $Alert->setAlertStudent($currentAlert[3]);

        try {

            $stmt = $this->dbh->prepare("
                UPDATE alert
                SET alertDescription = :alertDescription, alertDismissed = :alertDismissed, alertStudent = :alertStudent 
                WHERE alertId = :alertId
            ");

            $stmt->execute(array(
                "alertDescription"=>$Alert->getAlertDescription(),
                "alertDismissed"=>$Alert->getAlertDismissed(),
                "alertStudent"=>$Alert->getAlertStudent(),
                "alertId"=>$Alert->getAlertID()
            ));

        } catch (PDOException $pe) {
            echo $pe->getMessage();
            return -1;
        } // Ends try catch

    } // Ends updateAlertDismiss function

/********************************CLASS FUNCTIONS*************************************/
    // NOTE: Since "class" is a reserved word, the PHP class to interact with the database table "class" is called "ClassTable"

    // Returns a table with all of the information from the class table
    public function getAllClassObjectsAsTable() {

        $data = $this->getAllObjects("SELECT * FROM class", "ClassTable");

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Class ID</th>
                            <th>Class Name</th>
                            <th>Class Professor</th>
                            <th>School ID</th>
            </tr></thead>\n";

            foreach ($data as $class) {

                // TODO: Code below needs to be refactored later to display school name instead of currently displayed school ID
                // Should update class object and replace the schoolId with the name of the school
                $classSchoolID = $class->getClassSchoolID();
                $schoolObject = $this->getAllObjects("SELECT schoolName FROM school WHERE schoolId = $classSchoolID", "School");
            
                foreach ($schoolObject as $school) {

                    $classSchoolName = $school->getSchoolName();
                    $class->setClassSchoolID($classSchoolName);

                } // Ends school foreach

                $outputTable .= $class->getTableData();

            } // Ends class foreach

        } else {
            $outputTable = "<h2>No classes exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getAllClassObjectsAsTable

    // Gets an array of class data that is appropriate for a user
    public function getClassArray($userID, $userType) {

        if ($userType == "Admin") {

            $data = $this->getAllObjects("SELECT classId, className FROM class", "ClassTable");

        } else if ($userType == "Professor") {

            $data = $this->getAllObjects("SELECT classId, className FROM class WHERE classProfessor = $userID", "ClassTable");

        } else if ($userType == "Support") {

            $supportUser = $this->getUserByID($userID);
            $data = $this->getAllObjects("SELECT classId, className FROM class WHERE schoolId = $supportUser[7]", "ClassTable");

        } // Ends if

        if (!empty($data)) {

            foreach ($data as $item) {

                $class = array();
                $class['classId'] = $item->getClassID();
                $class['className'] = $item->getClassName();
                $resultArray[] = $class;

            } // Ends foreach

            return $resultArray;

        } else {

            return "ERROR404";

        } // Ends if

    } // Ends getClassArray

    // Gets the class abbreviation (ex. ISTE-501, SWEN-100) for a class
    public function getClassAbbreviationByID($classID) {

        $data = $this->getAllObjects("SELECT * FROM class WHERE classId = '$classID'", "ClassTable");

        if (count($data) > 0) {

            $className = $data[0]->getClassName();

            $trimStart = strpos($className, '(') + 1;
            $trimEnd = strpos($className, ')', $trimStart);
            $trimTemp = substr($className, $trimStart, $trimEnd - $trimStart);
            $outputClassCode = trim($trimTemp);
    
        } elseif (count($data) > 1) {

            $outputClassCode = "ERROR500";

        } else {

            $outputClassCode = "ERROR404";

        }// Ends if

        return $outputClassCode;

    } // Ends getClassAbbreviationByID

/********************************CLASSENTRY FUNCTIONS*************************************/
    
    // Returns a table with all of the information from the classentry table
    public function getAllClassEntryObjectsAsTable() {

        $data = $this->getAllObjects("SELECT * FROM classEntry", "ClassEntry");

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Student ID</th>
                            <th>Class ID</th>
            </tr></thead>\n";
    
            foreach ($data as $classEntry) {

                $outputTable .= $classEntry->getTableData();

            } // Ends classEntry foreach
    
        } else {
            $outputTable = "<h2>No classEntry records exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getAllClassEntryObjectsAsTable

/********************************FILE FUNCTIONS*************************************/
    
    // Returns a table with all of the information from the file table
    public function getAllFileObjectsAsTable() {

        $data = $this->getAllObjects("SELECT * FROM file", "File");

        if (count($data) > 0) {
            
            $outputTable = "<thead><tr>
                                <th>File Name</th>
                                <th>File Location</th>
                                <th>File Time Created</th>
                                <th>File Time Edited</th>
            </tr></thead>\n";
    
            foreach ($data as $file) {

                $fileStudentID = $file->getFileStudentID();
                $studentObject = $this->getAllObjects("SELECT * FROM student WHERE studentId = $fileStudentID", "Student");
             
                foreach ($studentObject as $student) {

                    $fileStudentUsername = $student->getStudentUsername();
                    $file->setFileStudentID($fileStudentUsername);

                } // Ends student foreach

                $outputTable .= $file->getTableData();

            } // Ends file foreach
    
        } else {
            $outputTable = "<h2>No files exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getAllFileObjectsAsTable

    // Gets all of the files that are associated with a student
    public function getFileObjectsByStudentAsTable($studentID, $currentPageNumber, $recordsPerPage) {

        $offset = ($currentPageNumber - 1) * $recordsPerPage;

        $data = $this->getAllObjects("SELECT * FROM file WHERE studentId = $studentID LIMIT $offset, $recordsPerPage", "File");

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>File Name</th>
                            <th>File Location</th>
                            <th>File Time Created</th>
                            <th>File Time Edited</th>
            </tr></thead>\n";
    
            foreach ($data as $file) {

                $fileStudentID = $file->getFileStudentID();
                $studentObject = $this->getAllObjects("SELECT * FROM student WHERE studentId = $fileStudentID", "Student");
             
                foreach ($studentObject as $student) {

                    $fileStudentUsername = $student->getStudentUsername();
                    $file->setFileStudentID($fileStudentUsername);

                } // Ends student foreach

                $outputTable .= $file->getTableData();

            } // Ends file foreach
    
        } else {
            $outputTable = "<h2>No files exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getFileObjectsByStudentAsTable

    // Gets the count of files associated with a student
    public function getFileObjectsByStudentCount($studentID) {

        $data = $this->getAllObjects("SELECT * FROM file WHERE studentId = $studentID", "File");

        if (count($data) > 0) {
            return count($data);
        } else {
            return 0;
        } // Ends if

    } // Ends getFileObjectsByStudentCount

    // Gets details for a specific file
    public function getFileByID($fileID) {

        $data = $this->getAllObjects("SELECT * FROM file WHERE fileId = '$fileID'", "File");

        if (count($data) > 0) {

            $outputFile[] = $data[0]->getFileID();
            $outputFile[] = $data[0]->getFileName();
            $outputFile[] = $data[0]->getFileTimeCreated();
            $outputFile[] = $data[0]->getFileTimeEdited();
            $outputFile[] = $data[0]->getFileLocation();
            $outputFile[] = $data[0]->getFileStudentID();
    
        } elseif (count($data) > 1) {

            $outputFile = "ERROR500";

        } else {

            $outputFile = "ERROR404";

        }// Ends if

        return $outputFile;

    } // Ends getFileByID

/********************************LOG FUNCTIONS*************************************/
    
    // Returns a table with all of the information from the log table
    public function getAllLogObjectsAsTable() {

        $data = $this->getAllObjects("SELECT * FROM log", "Log");

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Log Type</th>
                            <th>Log Time Created</th>
                            <th>Student Username</th>
            </tr></thead>\n";
    
            foreach ($data as $log) {

                $logStudentID = $log->getLogStudentID();
                $studentObject = $this->getAllObjects("SELECT * FROM student WHERE studentId = $logStudentID", "Student");
             
                foreach ($studentObject as $student) {

                    $logStudentUsername = $student->getStudentUsername();
                    $log->setLogStudentID($logStudentUsername);

                } // Ends student foreach

                $outputTable .= $log->getTableData();

            } // Ends log foreach
    
        } else {
            $outputTable = "<h2>No logs exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getAllLogObjectsAsTable

    // Returns the number of logs that were created today
    public function getLogsCreatedTodayCount($userID, $userType) {

        if ($userType == "Admin") { // Gets all logs that were created today

            $data = $this->getAllObjects("SELECT * FROM log WHERE DATE(logTimeCreated) = CURDATE()", "Log");

        } else if ($userType == "Professor") { // Gets all logs for students that are in the classes under the professor

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId INNER JOIN classEntry ON student.studentId = classEntry.studentId INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
            WHERE DATE(log.logTimeCreated) = CURDATE()", "Log");

        } else if ($userType == "Support") { // Gets all logs for students that are in the same school under a support

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId INNER JOIN school ON student.schoolId = school.schoolId INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
            WHERE DATE(log.logTimeCreated) = CURDATE()", "Log");

        } // Ends if

        if (is_array($data)) {
            return count($data);
        } else {
            return 0;
        } // Ends if

    } // Ends getCountLogsCreatedToday

    // Returns all log objects that were created today
    public function getLogsCreatedTodayAsTable($userID, $userType, $currentPageNumber, $recordsPerPage) {

        $offset = ($currentPageNumber - 1) * $recordsPerPage;

        if ($userType == "Admin") { // Gets all logs that were created today

            $data = $this->getAllObjects("SELECT * FROM log WHERE DATE(logTimeCreated) = CURDATE() LIMIT $offset, $recordsPerPage", "Log");

        } else if ($userType == "Professor") { // Gets all logs for students that are in the classes under the professor

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId INNER JOIN classEntry ON student.studentId = classEntry.studentId INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
            WHERE DATE(log.logTimeCreated) = CURDATE() LIMIT $offset, $recordsPerPage", "Log");

        } else if ($userType == "Support") { // Gets all logs for students that are in the same school under a support

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId INNER JOIN school ON student.schoolId = school.schoolId INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
            WHERE DATE(log.logTimeCreated) = CURDATE() LIMIT $offset, $recordsPerPage", "Log");

        } // Ends if

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Log Type</th>
                            <th>Associated Student Username</th>
                            <th>Log Time Created</th>
            </tr></thead>\n";
    
            foreach ($data as $log) {

                $logStudentID = $log->getLogStudentID();
                $studentObject = $this->getAllObjects("SELECT * FROM student WHERE studentId = $logStudentID", "Student");
             
                foreach ($studentObject as $student) {

                    $logStudentUsername = $student->getStudentUsername();
                    $log->setLogStudentID($logStudentUsername);

                } // Ends student foreach

                $outputTable .= $log->getTableLinkingRow();

            } // Ends log foreach
    
        } else {
            $outputTable = "<h2>No logs exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getLogsCreatedTodayAsTable

    // Returns the number of logs that were created today
    public function getLogsCreatedTimeframeCount($userID, $userType, $timeframe) {

        $queryTime = "";

        if ($timeframe == "day") {
            $queryTime = "WHERE DATE(logTimeCreated) = CURDATE()";
        } else if ($timeframe == "week") {
            $queryTime = "WHERE logTimeCreated >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } else if ($timeframe == "month") {
            $queryTime = "WHERE logTimeCreated >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        }

        if ($userType == "Admin") { // Gets all logs that were created today

            $data = $this->getAllObjects("SELECT * FROM log $queryTime", "Log");

        } else if ($userType == "Professor") { // Gets all logs for students that are in the classes under the professor

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId INNER JOIN classEntry ON student.studentId = classEntry.studentId INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
            $queryTime", "Log");

        } else if ($userType == "Support") { // Gets all logs for students that are in the same school under a support

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId INNER JOIN school ON student.schoolId = school.schoolId INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
            $queryTime", "Log");

        } // Ends if

        if (is_array($data)) {
            return count($data);
        } else {
            return 0;
        } // Ends if

    } // Ends getLogsCreatedTimeframeCount

    // Returns all log objects that were created today
    public function getLogsCreatedTimeframeTable($userID, $userType, $timeframe, $currentPageNumber, $recordsPerPage) {

        $offset = ($currentPageNumber - 1) * $recordsPerPage;

        $queryTime = "";

        if ($timeframe == "day") {
            $queryTime = "WHERE DATE(logTimeCreated) = CURDATE()";
        } else if ($timeframe == "week") {
            $queryTime = "WHERE logTimeCreated >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } else if ($timeframe == "month") {
            $queryTime = "WHERE logTimeCreated >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        } // Ends if

        if ($userType == "Admin") { // Gets all logs that were created today

            $data = $this->getAllObjects("SELECT * FROM log $queryTime LIMIT $offset, $recordsPerPage", "Log");

        } else if ($userType == "Professor") { // Gets all logs for students that are in the classes under the professor

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId INNER JOIN classEntry ON student.studentId = classEntry.studentId INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
            $queryTime LIMIT $offset, $recordsPerPage", "Log");

        } else if ($userType == "Support") { // Gets all logs for students that are in the same school under a support

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId INNER JOIN school ON student.schoolId = school.schoolId INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
            $queryTime LIMIT $offset, $recordsPerPage", "Log");

        } // Ends if

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Log Type</th>
                            <th>Associated Student Username</th>
                            <th>Log Time Created</th>
            </tr></thead>\n";
    
            foreach ($data as $log) {

                $logStudentID = $log->getLogStudentID();
                $studentObject = $this->getAllObjects("SELECT * FROM student WHERE studentId = $logStudentID", "Student");
             
                foreach ($studentObject as $student) {

                    $logStudentUsername = $student->getStudentUsername();
                    $log->setLogStudentID($logStudentUsername);

                } // Ends student foreach

                $outputTable .= $log->getTableLinkingRow();

            } // Ends log foreach
    
        } else {
            $outputTable = "<h4>No logs exist.</h4>";
        }// Ends if

        return $outputTable;

    } // Ends getLogsCreatedTimeframeTable

    // Returns the logs appropriate for the specific user.
    public function getLogObjectsByRoleAsTable($userID, $userType, $currentPageNumber, $recordsPerPage) {

        $offset = ($currentPageNumber - 1) * $recordsPerPage;

        if ($userType == "Admin") {

            $data = $this->getAllObjects("SELECT * FROM log ORDER BY logTimeCreated DESC LIMIT $offset, $recordsPerPage", "Log");

        } else if ($userType == "Professor") { // Gets all logs for students that are in the classes under the professor

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId 
            INNER JOIN classEntry ON student.studentId = classEntry.studentId 
            INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
            ORDER BY logTimeCreated DESC
            LIMIT $offset, $recordsPerPage", 
            "Log");

        } else if ($userType == "Support") { // Gets all logs for students that are in the same school under a support

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId 
            INNER JOIN school ON student.schoolId = school.schoolId 
            INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
            ORDER BY logTimeCreated DESC
            LIMIT $offset, $recordsPerPage", 
            "Log");

        } // Ends if

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                                <th>Log Type</th>
                                <th>Associated Student Username</th>
                                <th>Log Time Created</th>
            </tr></thead>\n";
    
            foreach ($data as $log) {

                $logStudentID = $log->getLogStudentID();
                $studentObject = $this->getAllObjects("SELECT * FROM student WHERE studentId = $logStudentID", "Student");
             
                foreach ($studentObject as $student) {

                    $logStudentUsername = $student->getStudentUsername();
                    $log->setLogStudentID($logStudentUsername);

                } // Ends student foreach

                $outputTable .= $log->getTableLinkingRow();

            } // Ends log foreach
    
        } else {
            $outputTable = "<h2>No logs exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getLogObjectsByRoleAsTable

    // Returns the logs appropriate for the specific user, and filters it based on what is passed in.
    public function getLogObjectsByRoleFilteredAsTable($userID, $userType, $currentPageNumber, $recordsPerPage, $sortBy, $filterByUsername, $filterByTime, $filterByType) {

        $offset = ($currentPageNumber - 1) * $recordsPerPage;

        $sortQuery = "";
        if ($sortBy === "type") {
            $sortQuery = "GROUP BY log.logId ORDER BY log.logType, log.logTimeCreated DESC";
        } elseif ($sortBy === "student") {
            $sortQuery = "GROUP BY log.logId ORDER BY student.studentUsername, log.logTimeCreated DESC";
        } elseif ($sortBy === "mostRecent") {
            $sortQuery = "GROUP BY log.logId ORDER BY log.logTimeCreated DESC";
        }

        $filterConditions = array();
        if (!empty($filterByUsername)) {
            $filteredStudent = $this->getStudentByUsername($filterByUsername);
            
            if (is_array($filteredStudent)) {
                $filterConditions[] = "log.studentId = $filteredStudent[0]";
            } else {
                return "<h2>No matching records found with that username.</h2>";
            }
        }

        if (!empty($filterByType) && $filterByType != "Any") {
            
            if ($filterByType == "Successful Login") {
                $filterConditions[] = "log.logType = 0";
                $filterConditions[] = "loginAttempt.loginAttemptSuccess = 1";
            } elseif ($filterByType == "Failed Login") {
                $filterConditions[] = "log.logType = 0";
                $filterConditions[] = "loginAttempt.loginAttemptSuccess = 0";
            } elseif ($filterByType == "File Created") {
                $filterConditions[] = "log.logType = 1";
            } elseif ($filterByType == "File Modified") {
                $filterConditions[] = "log.logType = 2";
            }
            
        }

        if ($filterByTime === "Last Day") {
            $filterConditions[] = "log.logTimeCreated >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
        } elseif ($filterByTime === "Last Three Days") {
            $filterConditions[] = "log.logTimeCreated >= DATE_SUB(NOW(), INTERVAL 3 DAY)";
        } elseif ($filterByTime === "Last Week") {
            $filterConditions[] = "log.logTimeCreated >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } elseif ($filterByTime === "Last Month") {
            $filterConditions[] = "log.logTimeCreated >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        }

        if ($userType == "Admin") {

            $query = "SELECT * FROM log
            INNER JOIN loginAttempt on log.loginAttemptId = loginAttempt.loginAttemptId
            INNER JOIN student ON log.studentId = student.studentId";

        } elseif ($userType == "Professor") {

            $query = "SELECT log.* FROM log
            INNER JOIN loginAttempt on log.loginAttemptId = loginAttempt.loginAttemptId
            INNER JOIN student ON log.studentId = student.studentId
            INNER JOIN classEntry ON student.studentId = classEntry.studentId
            INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID";

        } elseif ($userType == "Support") {

            $query = "SELECT log.* FROM log
            INNER JOIN loginAttempt on log.loginAttemptId = loginAttempt.loginAttemptId
            INNER JOIN student ON log.studentId = student.studentId
            INNER JOIN school ON student.schoolId = school.schoolId
            INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID";

        } // Ends if
        
        if (!empty($filterConditions)) {
            $query .= " WHERE " . implode(" AND ", $filterConditions);
        }

        if (empty($sortQuery)) {
            $sortQuery = "GROUP BY log.logId";
        }
        
        $query .= " $sortQuery LIMIT $offset, $recordsPerPage";

        $data = $this->getAllObjects($query, "Log");

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                <th>Log Type</th>
                <th>Associated Student Username</th>
                <th>Log Time Created</th>
            </tr></thead>\n";
    
            foreach ($data as $log) {

                $logStudentID = $log->getLogStudentID();
                $studentObject = $this->getAllObjects("SELECT * FROM student WHERE studentId = $logStudentID", "Student");
             
                foreach ($studentObject as $student) {

                    $logStudentUsername = $student->getStudentUsername();
                    $log->setLogStudentID($logStudentUsername);

                } // Ends student foreach

                $outputTable .= $log->getTableLinkingRow();

            } // Ends log foreach
    
        } else {
            $outputTable = "<h3>No logs found.</h3>";
        }// Ends if

        return $outputTable;

    } // Ends getLogObjectsByRoleFilteredAsTable

    // Returns the number of logs that will be shown for a specific user. Used for pagination math
    public function getLogObjectsByRoleCount($userID, $userType) {

        if ($userType == "Admin") {

            $data = $this->getAllObjects("SELECT * FROM log", "Log");

        } else if ($userType == "Professor") { // Gets all logs for students that are in the classes under the professor

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId
            INNER JOIN classEntry ON student.studentId = classEntry.studentId
            INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID",
            "Log");

        } else if ($userType == "Support") { // Gets all logs for students that are in the same school under a support

            $data = $this->getAllObjects("SELECT log.* FROM log
            INNER JOIN student ON log.studentId = student.studentId
            INNER JOIN school ON student.schoolId = school.schoolId
            INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID",
            "Log");

        } // Ends if

        if (count($data) > 0) {
            return count($data);
        } else {
            return 0;
        } // Ends if

    } // Ends getLogObjectsByRoleCount

    // Returns the count of logs appropriate for the specific user, and filters it based on what is passed in.
    public function getLogObjectsByRoleFilteredCount($userID, $userType, $sortBy, $filterByUsername, $filterByTime, $filterByType) {

        if ($sortBy === "type") {
            $sortQuery = "GROUP BY log.logId ORDER BY log.logType";
        } elseif ($sortBy === "student") {
            $sortQuery = "GROUP BY log.logId ORDER BY log.studentId";
        } elseif ($sortBy === "mostRecent") {
            $sortQuery = "GROUP BY log.logId ORDER BY log.logTimeCreated DESC";
        }

        $filterConditions = array();
        if (!empty($filterByUsername)) {
            $filteredStudent = $this->getStudentByUsername($filterByUsername);
            
            if (is_array($filteredStudent)) {
                $filterConditions[] = "log.studentId = $filteredStudent[0]";
            } else {
                return "<h2>No matching records found with that username.</h2>";
            }
        }

        if (!empty($filterByType) && $filterByType != "Any") {
            
            if ($filterByType == "Successful Login") {
                $filterConditions[] = "log.logType = 0";
                $filterConditions[] = "loginAttempt.loginAttemptSuccess = 1";
            } elseif ($filterByType == "Failed Login") {
                $filterConditions[] = "log.logType = 0";
                $filterConditions[] = "loginAttempt.loginAttemptSuccess = 0";
            } elseif ($filterByType == "File Created") {
                $filterConditions[] = "log.logType = 1";
            } elseif ($filterByType == "File Modified") {
                $filterConditions[] = "log.logType = 2";
            }
            
        }

        if ($filterByTime === "Last Day") {
            $filterConditions[] = "log.logTimeCreated >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
        } elseif ($filterByTime === "Last Three Days") {
            $filterConditions[] = "log.logTimeCreated >= DATE_SUB(NOW(), INTERVAL 3 DAY)";
        } elseif ($filterByTime === "Last Week") {
            $filterConditions[] = "log.logTimeCreated >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } elseif ($filterByTime === "Last Month") {
            $filterConditions[] = "log.logTimeCreated >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        }

        if ($userType == "Admin") {

            $query = "SELECT * FROM log
            INNER JOIN loginAttempt on log.loginAttemptId = loginAttempt.loginAttemptId
            INNER JOIN student ON log.studentId = student.studentId";

        } elseif ($userType == "Professor") {

            $query = "SELECT log.* FROM log
            INNER JOIN loginAttempt on log.loginAttemptId = loginAttempt.loginAttemptId
            INNER JOIN student ON log.studentId = student.studentId
            INNER JOIN classEntry ON student.studentId = classEntry.studentId
            INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID";

        } elseif ($userType == "Support") {

            $query = "SELECT log.* FROM log
            INNER JOIN loginAttempt on log.loginAttemptId = loginAttempt.loginAttemptId
            INNER JOIN student ON log.studentId = student.studentId
            INNER JOIN school ON student.schoolId = school.schoolId
            INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID";

        } // Ends if
        
        if (!empty($filterConditions)) {
            $query .= " WHERE " . implode(" AND ", $filterConditions);
        }

        if (empty($sortQuery)) {
            $sortQuery = "GROUP BY log.logId";
        }
        
        $query .= " $sortQuery";

        $data = $this->getAllObjects($query, "Log");

        if (count($data) > 0) {
            return count($data);
        } else {
            return 0;
        } // Ends if

    } // Ends getLogObjectsByRoleFilteredCount

    // Returns information for one log in an array
    public function getLogByID($logID) {

        $data = $this->getAllObjects("SELECT * FROM log WHERE logId = '$logID'", "Log");

        if (count($data) > 0) {

            $outputLog[] = $data[0]->getLogID();
            $outputLog[] = $data[0]->getLogType();
            $outputLog[] = $data[0]->getLogTimeCreated();
            $outputLog[] = $data[0]->getLogLoginAttemptID();
            $outputLog[] = $data[0]->getLogStudentID();
    
        } elseif (count($data) > 1) {

            $outputLog = "ERROR500";

        } else {

            $outputLog = "ERROR404";

        }// Ends if

        return $outputLog;

    } // Ends getLogByID

    // Gets data on one log using the login attempt associated with the log
    public function getLogByLoginAttemptID($loginAttemptID) {

        $data = $this->getAllObjects("SELECT * FROM log WHERE loginAttemptId = '$loginAttemptID'", "Log");

        if (count($data) > 0) {

            $outputLog[] = $data[0]->getLogID();
            $outputLog[] = $data[0]->getLogType();
            $outputLog[] = $data[0]->getLogTimeCreated();
            $outputLog[] = $data[0]->getLogLoginAttemptID();
            $outputLog[] = $data[0]->getLogStudentID();
    
        } elseif (count($data) > 1) {

            $outputLog = "ERROR500";

        } else {

            $outputLog = "ERROR404";

        }// Ends if

        return $outputLog;

    } // Ends getLogByLoginAttemptID

    // Gets the details on the most recent log associated with a student
    public function getLogLatestByStudentID($studentID) {

        $data = $this->getAllObjects("SELECT * FROM log
        WHERE studentId = '$studentID'
        ORDER BY logTimeCreated DESC
        LIMIT 1", "Log");

        if (count($data) > 0) {

            $outputLog[] = $data[0]->getLogID();
            $outputLog[] = $data[0]->getLogType();
            $outputLog[] = $data[0]->getLogTimeCreated();
            $outputLog[] = $data[0]->getLogLoginAttemptID();
            $outputLog[] = $data[0]->getLogStudentID();
    
        } elseif (count($data) > 1) {

            $outputLog = "ERROR500";

        } else {

            $outputLog = "ERROR404";

        }// Ends if

        return $outputLog;

    } // Ends getLogLatestByStudentID

/********************************LOGINATTEMPT FUNCTIONS*************************************/
    
    // Returns a table with all of the information from the loginattempt table
    public function getAllLoginAttemptObjectsAsTable() {

        $data = $this->getAllObjects("SELECT * FROM loginAttempt", "LoginAttempt");

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Login Attempt Username</th>
                            <th>Login Attempt Time Entered</th>
                            <th>Login Attempt Success</th>
                            <th>Student ID</th>
            </tr></thead>\n";
    
            foreach ($data as $loginAttempt) {

                $outputTable .= $loginAttempt->getTableData();

            } // Ends loginAttempt foreach
    
        } else {
            $outputTable = "<h2>No login attempt records exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getAllLoginAttemptObjectsAsTable
    
    // Returns the number of login attempts from today
    public function getLoginAttemptsTimeframeCount($successType, $userID, $userType, $timeframe) {

        $queryTime = "";

        if ($timeframe == "day") {
            $queryTime = "WHERE DATE(loginAttemptTimeEntered) = CURDATE()";
        } else if ($timeframe == "week") {
            $queryTime = "WHERE loginAttemptTimeEntered >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } else if ($timeframe == "month") {
            $queryTime = "WHERE loginAttemptTimeEntered >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        } // Ends if

        switch ($successType) {

            case "all":

                if ($userType == "Admin") { // Gets all login attempts from today

                    $data = $this->getAllObjects("SELECT * FROM loginAttempt $queryTime", "LoginAttempt");
        
                } else if ($userType == "Professor") { // Gets all loginAttempts for students that are in the classes under the professor
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN classEntry ON student.studentId = classEntry.studentId INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
                    $queryTime", "LoginAttempt");
        
                } else if ($userType == "Support") { // Gets all loginAttempts for students that are in the same school under a support
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN school ON student.schoolId = school.schoolId INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
                    $queryTime", "LoginAttempt");
        
                } // Ends if
                
                break;

            case "failure":

                if ($userType == "Admin") { // Gets all login attempts from today

                    $data = $this->getAllObjects("SELECT * FROM loginAttempt $queryTime AND loginAttemptSuccess = 0", "LoginAttempt");
        
                } else if ($userType == "Professor") { // Gets all loginAttempts for students that are in the classes under the professor
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN classEntry ON student.studentId = classEntry.studentId INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
                    $queryTime AND loginAttemptSuccess = 0", "LoginAttempt");
        
                } else if ($userType == "Support") { // Gets all loginAttempts for students that are in the same school under a support
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN school ON student.schoolId = school.schoolId INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
                    $queryTime AND loginAttemptSuccess = 0", "LoginAttempt");
        
                } // Ends if

                break;

            case "success":

                if ($userType == "Admin") { // Gets all login attempts from today

                    $data = $this->getAllObjects("SELECT * FROM loginAttempt $queryTime AND loginAttemptSuccess = 1", "LoginAttempt");
        
                } else if ($userType == "Professor") { // Gets all loginAttempts for students that are in the classes under the professor
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN classEntry ON student.studentId = classEntry.studentId INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
                    $queryTime AND loginAttemptSuccess = 1", "LoginAttempt");
        
                } else if ($userType == "Support") { // Gets all loginAttempts for students that are in the same school under a support
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN school ON student.schoolId = school.schoolId INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
                    $queryTime AND loginAttemptSuccess = 1", "LoginAttempt");
        
                } // Ends if

                break;

        } // Ends successType switch

        if (is_array($data)) {
            return count($data);
        } else {
            return 0;
        } // Ends if

    } // Ends getLoginAttemptsTimeframeCount

    // Returns the number of login attempts from today
    public function getLoginAttemptsTimeframeAsTable($successType, $userID, $userType, $timeframe, $currentPageNumber, $recordsPerPage) {

        $offset = ($currentPageNumber - 1) * $recordsPerPage;

        $queryTime = "";

        if ($timeframe == "day") {
            $queryTime = "WHERE DATE(loginAttemptTimeEntered) = CURDATE()";
        } else if ($timeframe == "week") {
            $queryTime = "WHERE loginAttemptTimeEntered >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } else if ($timeframe == "month") {
            $queryTime = "WHERE loginAttemptTimeEntered >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        } // Ends if

        switch ($successType) {

            case "all":

                if ($userType == "Admin") { // Gets all login attempts from today

                    $data = $this->getAllObjects("SELECT * FROM loginAttempt $queryTime LIMIT $offset, $recordsPerPage", "LoginAttempt");
        
                } else if ($userType == "Professor") { // Gets all loginAttempts for students that are in the classes under the professor
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN classEntry ON student.studentId = classEntry.studentId INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
                    $queryTime LIMIT $offset, $recordsPerPage", "LoginAttempt");
        
                } else if ($userType == "Support") { // Gets all loginAttempts for students that are in the same school under a support
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN school ON student.schoolId = school.schoolId INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
                    $queryTime LIMIT $offset, $recordsPerPage", "LoginAttempt");
        
                } // Ends if
                
                break;

            case "failure":

                if ($userType == "Admin") { // Gets all login attempts from today

                    $data = $this->getAllObjects("SELECT * FROM loginAttempt $queryTime AND loginAttemptSuccess = 0 LIMIT $offset, $recordsPerPage", "LoginAttempt");
        
                } else if ($userType == "Professor") { // Gets all loginAttempts for students that are in the classes under the professor
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN classEntry ON student.studentId = classEntry.studentId INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
                    $queryTime AND loginAttemptSuccess = 0 LIMIT $offset, $recordsPerPage", "LoginAttempt");
        
                } else if ($userType == "Support") { // Gets all loginAttempts for students that are in the same school under a support
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN school ON student.schoolId = school.schoolId INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
                    $queryTime AND loginAttemptSuccess = 0 LIMIT $offset, $recordsPerPage", "LoginAttempt");
        
                } // Ends if

                break;

            case "success":

                if ($userType == "Admin") { // Gets all login attempts from today

                    $data = $this->getAllObjects("SELECT * FROM loginAttempt $queryTime AND loginAttemptSuccess = 1 LIMIT $offset, $recordsPerPage", "LoginAttempt");
        
                } else if ($userType == "Professor") { // Gets all loginAttempts for students that are in the classes under the professor
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN classEntry ON student.studentId = classEntry.studentId INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
                    $queryTime AND loginAttemptSuccess = 1 LIMIT $offset, $recordsPerPage", "LoginAttempt");
        
                } else if ($userType == "Support") { // Gets all loginAttempts for students that are in the same school under a support
        
                    $data = $this->getAllObjects("SELECT loginAttempt.* FROM loginAttempt
                    INNER JOIN student ON loginAttempt.studentId = student.studentId INNER JOIN school ON student.schoolId = school.schoolId INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
                    $queryTime AND loginAttemptSuccess = 1 LIMIT $offset, $recordsPerPage", "LoginAttempt");
        
                } // Ends if

                break;

        } // Ends successType switch

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Login Attempt Username</th>
                            <th>Login Attempt Time Entered</th>
                            <th>Login Attempt Outcome</th>
            </tr></thead>\n";
    
            foreach ($data as $loginAttempt) {

                if ($loginAttempt->getLoginAttemptSuccess() == 0) {
                    $loginAttempt->setLoginAttemptSuccess("Failed Login");
                } elseif ($loginAttempt->getLoginAttemptSuccess() == 1) {
                    $loginAttempt->setLoginAttemptSuccess("Successful Login");
                } // Ends if

                $relatedLog = $this->getLogByLoginAttemptID($loginAttempt->getLoginAttemptID());
                $outputTable .= $loginAttempt->getTableData($relatedLog[0]);

            } // Ends loginAttempt foreach
    
        } else {
            $outputTable = "<h3>There have been no login attempts created today that match your query.</h3>";
        }// Ends if

        return $outputTable;

    } // Ends getLoginAttemptsTimeframeAsTable

    // Returns information for one login attempt in an array
    public function getLoginAttemptByID($loginAttemptID) {

        $data = $this->getAllObjects("SELECT * FROM loginAttempt WHERE loginAttemptId = '$loginAttemptID'", "LoginAttempt");

        if (count($data) > 0) {

            $outputLoginAttempt[] = $data[0]->getLoginAttemptID();
            $outputLoginAttempt[] = $data[0]->getLoginAttemptUsername();
            $outputLoginAttempt[] = $data[0]->getLoginAttemptTimeEntered();
            $outputLoginAttempt[] = $data[0]->getLoginAttemptSuccess();
            $outputLoginAttempt[] = $data[0]->getLoginAttemptStudentID();
    
        } elseif (count($data) > 1) {

            $outputLoginAttempt = "ERROR500";

        } else {

            $outputLoginAttempt = "ERROR404";

        }// Ends if

        return $outputLoginAttempt;

    } // Ends getLoginAttemptByID

/********************************SCHOOL FUNCTIONS*************************************/
    
    // Returns a table with all of the information from the school table
    public function getAllSchoolObjectsAsTable() {

        $data = $this->getAllObjects("SELECT * FROM school", "School");

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>School ID</th>
                            <th>School Name</th>
            </tr></thead>\n";
    
            foreach ($data as $school) {

                $outputTable .= $school->getTableData();

            } // Ends school foreach
    
        } else {
            $outputTable = "<h2>No schools exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getAllSchoolObjectsAsTable

    // Gets the data for a specific school
    public function getSchoolByID($schoolID) {

        $data = $this->getAllObjects("SELECT * FROM school WHERE schoolId = '$schoolID'", "School");

        if (count($data) > 0) {

            $outputSchool[] = $data[0]->getSchoolID();
            $outputSchool[] = $data[0]->getSchoolName();
    
        } elseif (count($data) > 1) {

            $outputSchool = "ERROR500";

        } else {

            $outputSchool = "ERROR404";

        }// Ends if

        return $outputSchool;

    } // Ends getSchoolByID

/********************************STUDENT FUNCTIONS*************************************/
    
    // Returns a table with all of the information from the student table
    public function getAllStudentObjectsAsTable() {

        $data = $this->getAllObjects("SELECT * FROM student", "Student");

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Student First Name</th>
                            <th>Student Middle Initial</th>
                            <th>Student Last Name</th>
                            <th>Student Username</th>
                            <th>Student School</th>
            </tr></thead>\n";
    
            foreach ($data as $student) {

                $studentSchoolID = $student->getStudentSchoolID();
                $schoolObject = $this->getAllObjects("SELECT * FROM school WHERE schoolId = $studentSchoolID", "School");
             
                foreach ($schoolObject as $school) {

                    $studentSchoolName = $school->getSchoolName();
                    $student->setStudentSchoolID($studentSchoolName);

                } // Ends school foreach

                $outputTable .= $student->getTableData();

            } // Ends student foreach
    
        } else {
            $outputTable = "<h2>No students exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getAllStudentObjectsAsTable

    // Gets all students for the passed in user
    public function getStudentObjectsByRoleAsTable($userID, $userType, $currentPageNumber, $recordsPerPage) {

        $offset = ($currentPageNumber - 1) * $recordsPerPage;

        if ($userType == "Admin") {

            $data = $this->getAllObjects("SELECT * FROM student LIMIT $offset, $recordsPerPage", "Student");

        } else if ($userType == "Professor") { // Gets all students that are in the classes under the professor

            $data = $this->getAllObjects("SELECT student.* FROM student
            INNER JOIN classEntry ON student.studentId = classEntry.studentId 
            INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID
            LIMIT $offset, $recordsPerPage", "Student");

        } else if ($userType == "Support") { // Gets all students that are in the same school under a support

            $data = $this->getAllObjects("SELECT student.* FROM student
            INNER JOIN school ON student.schoolId = school.schoolId 
            INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID 
            LIMIT $offset, $recordsPerPage", "Student");

        } // Ends if

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Student First Name</th>
                            <th>Student Middle Initial</th>
                            <th>Student Last Name</th>
                            <th>Student Username</th>
                            <th>Student School</th>
            </tr></thead>\n";
    
            foreach ($data as $student) {

                $studentSchoolID = $student->getStudentSchoolID();
                $schoolObject = $this->getAllObjects("SELECT * FROM school WHERE schoolId = $studentSchoolID", "School");
             
                foreach ($schoolObject as $school) {

                    $studentSchoolName = $school->getSchoolName();
                    $student->setStudentSchoolID($studentSchoolName);

                } // Ends school foreach

                $outputTable .= $student->getTableLinkingRow();

            } // Ends student foreach
    
        } else {
            $outputTable = "<h2>No students exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getStudentObjectsByRoleAsTable

    // Gets all students for the passed in user, and filters it based on other passed in arguments
    public function getStudentObjectsByRoleFilteredAsTable($userID, $userType, $currentPageNumber, $recordsPerPage, $sortBy, $filterByUsername, $filterByClass, $filterByLastName) {

        $offset = ($currentPageNumber - 1) * $recordsPerPage;

        $sortQuery = "";
        if ($sortBy === "school") {
            $sortQuery = "GROUP BY student.studentId, student.schoolId ORDER BY student.schoolId";
        } elseif ($sortBy === "username") {
            $sortQuery = "GROUP BY student.studentUsername ORDER BY student.studentUsername";
        } elseif ($sortBy === "lastName") {
            $sortQuery = "GROUP BY student.studentLastName ORDER BY student.studentLastName";
        }

        $filterConditions = array();

        if (!empty($filterByUsername)) {
            $filterConditions[] = "student.studentUsername = \"$filterByUsername\"";
        }
        
        if (!empty($filterByLastName)) {
            $filterConditions[] = "student.studentLastName = \"$filterByLastName\"";
        }

        if (!empty($filterByClass)) {
            $filterConditions[] = "class.classId = $filterByClass";
        }

        if ($userType == "Admin") {

            $query = "SELECT student.* FROM student
            INNER JOIN classEntry ON student.studentId = classEntry.studentId
            INNER JOIN class ON classEntry.classId = class.classId";

        } elseif ($userType == "Professor") { // Gets all students that are in the classes under the professor

            $query = "SELECT student.* FROM student
            INNER JOIN classEntry ON student.studentId = classEntry.studentId
            INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID";

        } elseif ($userType == "Support") { // Gets all students that are in the same school under a support

            $query = "SELECT student.* FROM student
            INNER JOIN school ON student.schoolId = school.schoolId
            INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
            INNER JOIN classEntry ON student.studentId = classEntry.studentId
            INNER JOIN class ON classEntry.classId = class.classId";

        } // Ends if

        if (!empty($filterConditions)) {
            $query .= " WHERE " . implode(" AND ", $filterConditions);
        }

        if (empty($sortQuery)) {
            $sortQuery = "GROUP BY student.studentId";
        }

        $query .= " $sortQuery LIMIT $offset, $recordsPerPage";
        
        $data = $this->getAllObjects($query, "Student");

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>Student First Name</th>
                            <th>Student Middle Initial</th>
                            <th>Student Last Name</th>
                            <th>Student Username</th>
                            <th>Student School</th>
            </tr></thead>\n";
    
            foreach ($data as $student) {

                $studentSchoolID = $student->getStudentSchoolID();
                $schoolObject = $this->getAllObjects("SELECT * FROM school WHERE schoolId = $studentSchoolID", "School");
             
                foreach ($schoolObject as $school) {

                    $studentSchoolName = $school->getSchoolName();
                    $student->setStudentSchoolID($studentSchoolName);

                } // Ends school foreach

                $outputTable .= $student->getTableLinkingRow();

            } // Ends student foreach
    
        } else {
            $outputTable = "<h2>No students exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getStudentObjectsByRoleFilteredAsTable

    // Gets the count of students for a passed in user
    public function getStudentObjectsByRoleCount($userID, $userType) {

        if ($userType == "Admin") {

            $data = $this->getAllObjects("SELECT * FROM student", "Student");

        } elseif ($userType == "Professor") { // Gets all students that are in the classes under the professor

            $data = $this->getAllObjects("SELECT student.* FROM student
            INNER JOIN classEntry ON student.studentId = classEntry.studentId 
            INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID", 
            "Student");

        } elseif ($userType == "Support") { // Gets all students that are in the same school under a support

            $data = $this->getAllObjects("SELECT student.* FROM student
            INNER JOIN school ON student.schoolId = school.schoolId 
            INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID", 
            "Student");

        } // Ends if

        if (count($data) > 0) {
            return count($data);
        } else {
            return 0;
        } // Ends if

    } // Ends getStudentObjectsByRoleCount

    // Gets the count of filtered students for a passed in user 
    public function getStudentObjectsByRoleFilteredCount($userID, $userType, $sortBy, $filterByUsername, $filterByClass, $filterByLog) {

        $sortQuery = "";
        if ($sortBy === "school") {
            $sortQuery = "GROUP BY student.studentId, student.schoolId ORDER BY student.schoolId";
        } elseif ($sortBy === "username") {
            $sortQuery = "GROUP BY student.studentUsername ORDER BY student.studentUsername";
        }

        $filterConditions = array();

        if (!empty($filterByUsername)) {
            $filterConditions[] = "student.studentUsername = \"$filterByUsername\"";
        }

        if (!empty($filterByLastName)) {
            $filterConditions[] = "student.studentLastName = \"$filterByLastName\"";
        }
        
        if (!empty($filterByClass)) {
            $filterConditions[] = "class.classId = $filterByClass";
        }

        if ($userType == "Admin") {

            $query = "SELECT student.* FROM student
            INNER JOIN classEntry ON student.studentId = classEntry.studentId
            INNER JOIN class ON classEntry.classId = class.classId";

        } elseif ($userType == "Professor") { // Gets all students that are in the classes under the professor

            $query = "SELECT student.* FROM student
            INNER JOIN classEntry ON student.studentId = classEntry.studentId
            INNER JOIN class ON classEntry.classId = class.classId AND class.classProfessor = $userID";

        } elseif ($userType == "Support") { // Gets all students that are in the same school under a support

            $query = "SELECT student.* FROM student
            INNER JOIN school ON student.schoolId = school.schoolId
            INNER JOIN user ON school.schoolId = user.schoolId AND user.userId = $userID
            INNER JOIN classEntry ON student.studentId = classEntry.studentId
            INNER JOIN class ON classEntry.classId = class.classId";

        } // Ends if

        if (!empty($filterConditions)) {
            $query .= " WHERE " . implode(" AND ", $filterConditions);
        }

        if (empty($sortQuery)) {
            $sortQuery = "GROUP BY student.studentId";
        }

        $query .= " $sortQuery";
        
        $data = $this->getAllObjects($query, "Student");

        if (count($data) > 0) {
            return count($data);
        } else {
            return 0;
        } // Ends if

    } // Ends getStudentObjectsByRoleFilteredAsTable

    // Gets details for a specific student using the provided student ID
    public function getStudentByID($studentID) {

        $data = $this->getAllObjects("SELECT * FROM student WHERE studentId = '$studentID'", "Student");

        if (count($data) > 0) {

            $outputStudent[] = $data[0]->getStudentID();
            $outputStudent[] = $data[0]->getStudentFirstName();
            $outputStudent[] = $data[0]->getStudentMiddleInitial();
            $outputStudent[] = $data[0]->getStudentLastName();
            $outputStudent[] = $data[0]->getStudentUsername();
            $outputStudent[] = $data[0]->getStudentSchoolID();
    
        } elseif (count($data) > 1) {

            $outputStudent = "ERROR500";

        } else {

            $outputStudent = "ERROR404";

        }// Ends if

        return $outputStudent;

    } // Ends getStudentByID

    // Gets the details for a specific student using the provided student username
    public function getStudentByUsername($studentUsername) {

        $data = $this->getAllObjects("SELECT * FROM student WHERE studentUsername = '$studentUsername'", "Student");

        if (count($data) > 0) {

            $outputStudent[] = $data[0]->getStudentID();
            $outputStudent[] = $data[0]->getStudentFirstName();
            $outputStudent[] = $data[0]->getStudentMiddleInitial();
            $outputStudent[] = $data[0]->getStudentLastName();
            $outputStudent[] = $data[0]->getStudentUsername();
            $outputStudent[] = $data[0]->getStudentSchoolID();
    
        } elseif (count($data) > 1) {

            $outputStudent = "ERROR500";

        } else {

            $outputStudent = "ERROR404";

        }// Ends if

        return $outputStudent;

    } // Ends getStudentByUsername

/********************************USER FUNCTIONS*************************************/
    
    // Returns a table with all of the information from the user table
    public function getAllUserObjectsAsTable() {

        $data = $this->getAllObjects("SELECT * FROM user", "User");

        if (count($data) > 0) {

            $outputTable = "<thead><tr>
                            <th>User ID</th>
                            <th>User First Name</th>
                            <th>User Last Name</th>
                            <th>User Email</th>
                            <th>User Username</th>
                            <th>User Password</th>
                            <th>User Classification</th>
                            <th>School ID</th>
            </tr></thead>\n";
    
            foreach ($data as $user) {

                $userSchoolID = $user->getUserSchoolID();
                $schoolObject = $this->getAllObjects("SELECT * FROM school WHERE schoolId = $userSchoolID", "School");
             
                foreach ($schoolObject as $school) {

                    $userSchoolName = $school->getSchoolName();
                    $user->setUserSchoolID($userSchoolName);

                } // Ends school foreach

                $outputTable .= $user->getTableData();

            } // Ends user foreach
    
        } else {
            $outputTable = "<h2>No users exist.</h2>";
        }// Ends if

        return $outputTable;

    } // Ends getAllUserObjectsAsTable

    // Returns 404 error if no record is found 
    // Returns 500 error if more than 1 record is found
    // Returns array with all user information if a record is found
    public function getUserByID($userID) {

        $data = $this->getAllObjects("SELECT * FROM user WHERE userId = '$userID'", "User");

        if (count($data) > 0) {

            $outputUser[] = $data[0]->getUserID();
            $outputUser[] = $data[0]->getUserFirstName();
            $outputUser[] = $data[0]->getUserLastName();
            $outputUser[] = $data[0]->getUserEmail();
            $outputUser[] = $data[0]->getUserUsername();
            $outputUser[] = $data[0]->getUserPassword();
            $outputUser[] = $data[0]->getUserClassification();
            $outputUser[] = $data[0]->getUserSchoolID();
    
        } elseif (count($data) > 1) {

            $outputUser = "ERROR500";

        } else {

            $outputUser = "ERROR404";

        }// Ends if

        return $outputUser;

    } // Ends getUserByID

    // NOTE: This is the primary function for verifying login details. 
    // Returns 404 error if no record is found 
    // Returns 500 error if more than 1 record is found
    // Returns array with ID and classification if a record is found
    public function getUserInfoByLogin($inputUsername, $inputPassword) {

        $data = $this->getAllObjects("SELECT * FROM user WHERE userUsername = '$inputUsername' AND userPassword = '$inputPassword'", "User");

        if (count($data) > 0) {

            $outputUser[] = $data[0]->getUserID();
            $outputUser[] = $data[0]->getUserClassification();
    
        } elseif (count($data) > 1) {

            $outputUser = "ERROR500";

        } else {

            $outputUser = "ERROR404";

        }// Ends if

        return $outputUser;

    } // Ends getUserInfoByLogin

    // NOTE: Secondary login verification function used to check if username exists
    // Returns 404 error if no record is found 
    // Returns 500 error if more than 1 record is found
    // Returns user's password if a record is found
    public function getUserInfoByUsername($inputUsername) {

        $data = $this->getAllObjects("SELECT * FROM user WHERE userUsername = '$inputUsername'", "User");

        if (count($data) > 0) {

            $outputUser = $data[0]->getUserPassword();
    
        } elseif (count($data) > 1) {

            $outputUser = "ERROR500";

        } else {

            $outputUser = "ERROR404";

        }// Ends if

        return $outputUser;

    } // Ends getUserInfoByUsername

} // Ends DB class