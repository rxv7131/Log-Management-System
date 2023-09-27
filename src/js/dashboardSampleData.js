let sampleDataStringRecentUsers = "" +
    "<thead>" +
    "<tr>" +
    "<th>Student ID</th>" +
    "<th>First Name</th>" +
    "<th>Middle Initial</th>" +
    "<th>Last Name</th>" +
    "<th>Username</th>" +
    "<th>School</th>" +
    "</tr>" +
    "</thead>" +

    "<tr>" +
    "<td>1</td>" +
    "<td>Ryan</td>" +
    "<td>N</td>" +
    "<td>Vay</td>" +
    "<td>rxv7131</td>" +
    "<td>CIS</td>" +
    "</tr>" +

    "<tr>" +
    "<td>2</td>" +
    "<td>Evan</td>" +
    "<td>M</td>" +
    "<td>Vay</td>" +
    "<td>EvanUsername123</td>" +
    "<td>CSEC</td>" +
    "</tr>" +

    "<tr>" +
    "<td>3</td>" +
    "<td>Boris</td>" +
    "<td>G</td>" +
    "<td>McDonald</td>" +
    "<td>BGMD</td>" +
    "<td>CSEC</td>" +
    "</tr>" +

    "<tr>" +
    "<td>5</td>" +
    "<td>Johnny</td>" +
    "<td>B</td>" +
    "<td>Goode</td>" +
    "<td>JBGoode23</td>" +
    "<td>iSchool</td>" +
    "</tr>"
;

let sampleDataStringRecentLogs = "" +
    "<thead>" +
    "<tr>" +
    "<th>Log ID</th>" +
    "<th>Log Time Created</th>" +
    "<th>Log Time Edited</th>" +
    "<th>Login Attempt ID</th>" +
    "<th>Student ID</th>" +
    "</tr>" +
    "</thead>" +

    "<tr>" +
    "<td>1</td>" +
    "<td>2023-01-23 12:45:56</td>" +
    "<td>2023-01-23 12:45:56</td>" +
    "<td>1</td>" +
    "<td>rxv7131</td>" +
    "</tr>" +

    "<tr>" +
    "<td>2</td>" +
    "<td>2023-01-23 12:46:19</td>" +
    "<td>2023-01-23 12:46:19</td>" +
    "<td>2</td>" +
    "<td>rxv7131</td>" +
    "</tr>" +

    "<tr>" +
    "<td>3</td>" +
    "<td>2023-01-25 08:23:42</td>" +
    "<td>2023-01-25 08:23:42</td>" +
    "<td>4</td>" +
    "<td>EvanUsername</td>" +
    "</tr>" +

    "<tr>" +
    "<td>4</td>" +
    "<td>2023-01-28 11:19:07</td>" +
    "<td>2023-01-28 11:19:07</td>" +
    "<td>5</td>" +
    "<td>BGMD</td>" +
    "</tr>"
;


function getSampleData(){
    return sampleDataString;
}


window.onload = function(){
    $("#studentDashboardTable").append( sampleDataStringRecentUsers );
    $("#logDashboardTable").append( sampleDataStringRecentLogs );
};
