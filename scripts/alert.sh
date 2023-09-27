#!/bin/bash

# globals
db='u107823177_Team1_SQL_DB'
dbuser='u107823177_Team1_SQL_User'
dbpw='71H7KqGKH6'
singleStudentThreshold=10 # number of failed logins from one that triggers the alert for that student
systemwideThreshold=20 # number of failed logins systemwide to trigger a global alert
lastHour=$(date -d "5 hours ago" +%F' '%H) # the hour of login attempts we're looking through

######### per student check ###########

# get all students
query="SELECT studentId FROM student"
results=$(mysql --user=$dbuser --password=$dbpw --database=$db -N -e "$query")

# iterate through students
for id in $results
do
    # check for failed logins in the last hour for each student
    query="SELECT loginAttemptUsername FROM loginAttempt WHERE studentId = $id AND loginAttemptSuccess = 0 AND loginAttemptTimeEntered LIKE '$lastHour%'"
    # this query lists the student's name the number of times they failed, so you can get the number of fails with 'wordcount'
    results=$(mysql --user=$dbuser --password=$dbpw --database=$db -N -e "$query")
    resultnum=$(echo $results | wc -w)
    # if the number of fails is greater than the per-student threshold, issue an alert for the student
    if [[ $resultnum -gt $singleStudentThreshold ]]
    then
        # get their username from the results
        username=$(echo $results | cut -d ' ' -f 1)
        # add the alert to the alerts table
        query="INSERT INTO alert (alertDescription, alertDismissed, alertStudent) VALUES ('Number of failed logins ($resultnum) exceeded per-student threshold ($singleStudentThreshold) at the hour $lastHour for user $username', 0, $id)"
        mysql --user=$dbuser --password=$dbpw --database=$db -e "$query"
    fi
done

######### systemwide check ##########

# get all failed logins in the last hour
query="SELECT loginAttemptId FROM loginAttempt WHERE loginAttemptSuccess = 0 AND loginAttemptTimeEntered LIKE '$lastHour%'"
    # this query lists all failed logins in the last hour, so you can get the number of fails with 'wordcount'
    results=$(mysql --user=$dbuser --password=$dbpw --database=$db -N -e "$query")
    resultnum=$(echo $results | wc -w)
    # if there are more fails than the systemwide threshold, issue an alert
    if [[ $resultnum -gt $systemwideThreshold ]]
    then
        # add the alert to the alerts table
        query="INSERT INTO alert (alertDescription, alertDismissed, alertStudent) VALUES ('Number of failed logins ($resultnum) exceeded global threshold ($systemwideThreshold) at the hour $lastHour', 0, 0)"
        mysql --user=$dbuser --password=$dbpw --database=$db -e "$query"
    fi