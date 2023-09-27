#!/bin/bash

DB_HOST="localhost"
DB_NAME="u107823177_Team1_SQL_DB"
DB_USER="u107823177_Team1_SQL_User"
DB_PASSWORD="71H7KqGKH6"

# Get current time and time one hour ago in UTC
NOW=$(date -u +"%Y-%m-%d %H:%M:%S")
ONE_HOUR_AGO=$(date -u -d '-1 hour' +"%Y-%m-%d %H:%M:%S")

# SCRIPT 1: CHECKS TOTAL NUMBER OF FAILED ATTEMPTS
# Connect to the database and run the query
FAILED_LOGIN_ATTEMPTS=$(mysql -u $DB_USER -p$DB_PASS -D $DB_NAME -se "SELECT COUNT(*) FROM log
    INNER JOIN loginAttempt ON log.loginAttemptId = loginAttempt.loginAttemptId
    WHERE logType = 0 AND loginAttemptSuccess = 0 AND logTimeCreated BETWEEN '$ONE_HOUR_AGO' AND '$NOW';")

# If there are more than 10 failed login attempts, insert a record into the alerts table
if [ $FAILED_LOGIN_ATTEMPTS -gt 10 ]
then
    ALERT_DESCRIPTION="There have been $FAILED_LOGIN_ATTEMPTS failed login attempts within the last hour."
    mysql -u $DB_USER -p$DB_PASS -D $DB_NAME -e "INSERT INTO alerts (alertClass, alertDescription) VALUES (0, '$ALERT_DESCRIPTION');"
fi

# Function to check for failed login attempts in the last hour
function check_failed_login_attempts {
    # Get the current time and time one hour ago
    current_time=$(date +"%Y-%m-%d %H:%M:%S")
    one_hour_ago=$(date -d '1 hour ago' +"%Y-%m-%d %H:%M:%S")

    # Count the number of failed login attempts in the last hour
    num_failed_logins=$(mysql -u<username> -p<password> -e "SELECT COUNT(*) FROM log JOIN loginAttempt ON log.loginAttemptId=loginAttempt.loginAttemptId WHERE loginAttemptSuccess=0 AND logType=0 AND logTimeCreated BETWEEN '$one_hour_ago' AND '$current_time';" <database_name> | tail -1)

    # If there were more than 10 failed login attempts, insert a record into the alerts table
    if [ $num_failed_logins -gt 10 ]; then
        alert_description="There were $num_failed_logins failed login attempts in the last hour."
        mysql -u<username> -p<password> -e "INSERT INTO alerts (alertClass, alertDescription) VALUES (0, '$alert_description');" <database_name>
    fi
}

# Function to check for file creation alerts in the last hour
function check_file_creation_alerts {
    # Get the current time and time one hour ago
    current_time=$(date +"%Y-%m-%d %H:%M:%S")
    one_hour_ago=$(date -d '1 hour ago' +"%Y-%m-%d %H:%M:%S")

    # Count the number of file creation logs in the last hour
    num_file_creations=$(mysql -u<username> -p<password> -e "SELECT COUNT(*) FROM log WHERE logType=1 AND logTimeCreated BETWEEN '$one_hour_ago' AND '$current_time';" <database_name> | tail -1)

    # If there were more than 10 file creations, insert a record into the alerts table
    if [ $num_file_creations -gt 10 ]; then
        alert_description="There were $num_file_creations file creations in the last hour."
        mysql -u<username> -p<password> -e "INSERT INTO alerts (alertClass, alertDescription) VALUES (0, '$alert_description');" <database_name>
    fi
}

#!/bin/bash

# Get the current timestamp minus one hour
one_hour_ago=$(date -d "1 hour ago" "+%Y-%m-%d %H:%M:%S")

# Query the database to get the count of failed login attempts for each student in the last hour
mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME -e "
  SELECT student.studentUsername, COUNT(*) AS num_failed_logins
  FROM loginAttempt
  LEFT JOIN student ON loginAttempt.studentId = student.studentId
  WHERE loginAttempt.loginAttemptSuccess = 0
    AND loginAttempt.loginAttemptTimeEntered >= '$one_hour_ago'
  GROUP BY student.studentUsername
  HAVING num_failed_logins >= 10;
" | while read student_username num_failed_logins; do
  echo "Student $student_username has had $num_failed_logins failed login attempts in the last hour."
done





# Get the current time and the time 1 hour ago
current_time=$(date +"%Y-%m-%d %H:%M:%S")
hour_ago=$(date -d '1 hour ago' +"%Y-%m-%d %H:%M:%S")

# Loop through each student in the database
while read -r student_id student_username; do
  # Count the number of failed login attempts for the student in the last hour
  num_failed_attempts=$(mysql -N -e "SELECT COUNT(*) FROM loginAttempt la JOIN log l ON la.loginAttemptId = l.loginAttemptId WHERE la.studentId = $student_id AND la.loginAttemptSuccess = 0 AND l.logTimeCreated >= '$hour_ago' AND l.logTimeCreated <= '$current_time';")
  
  # Check if the number of failed login attempts is greater than or equal to 10
  if ((num_failed_attempts >= 10)); then
    # Insert an alert record for the student
    alert_description="Student with username $student_username has had multiple login failures in the last hour"
    mysql -e "INSERT INTO alert (alertDescription, alertDismissed, alertStudent) VALUES ('$alert_description', 0, $student_id);"
  fi
done < <(mysql -N -e "SELECT studentId, studentUsername FROM student;")

echo "Alerts inserted for students with more than 10 failed login attempts in the last hour."