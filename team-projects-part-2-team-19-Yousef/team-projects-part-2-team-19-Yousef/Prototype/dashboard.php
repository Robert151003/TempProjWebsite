<?php
include 'credentials.php';
session_start();
//ini_set('display_errors', 0);
//ini_set('log_errors', 1);

// Establish the connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection was successful
if (!$conn) {
    echo ("Connection failed: " . mysqli_connect_error());
}
$projectID = "";
$username = $_SESSION['username'];
$id = $_SESSION['id'];

if (!isSet($_POST['ProjectID'])) {
    error_log("No project ID");
    $sql = "SELECT EmployeeID, Username, Email FROM `Employees` WHERE EmployeeID IN (SELECT EmployeeID FROM `Project-Employees` WHERE ProjectID IN (SELECT ProjectID FROM `Project-Employees` WHERE EmployeeID = '$id' AND isLeader = 1));";
}
else {
    error_log("Yes project ID");
    $projectID = $_POST['ProjectID'];

    if ($_SESSION['perm'] != "Manager") {
        echo "Error: Permission denied.";
        return;
    }
    //$sql = "SELECT ProjectID FROM `Project-Employees` WHERE EmployeeID = '$id' AND isLeader = 1;";
    //$result = mysqli_query($conn, $sql);

    //if (mysqli_num_rows($result) < 0) return [];
    //$row = mysqli_fetch_assoc($result);
    //$projectID = $row['ProjectID'];
    
    $sql = "SELECT DISTINCT e.EmployeeID, e.Username, e.Email FROM Employees e JOIN Tasks t ON e.EmployeeID = t.assignedTo WHERE t.ProjectID = '$projectID';";

}
$result = mysqli_query($conn, $sql);

/* if ($result) {

    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);


    error_log(json_encode($rows));
} else {

    error_log("Query failed: " . mysqli_error($conn));
} */

// Turn resulting posts into JSON
$array = array();
if (mysqli_num_rows($result) > 0){
    while ($row = mysqli_fetch_assoc($result)){
        $array[] = $row;
    }
}

echo json_encode($array, JSON_PRETTY_PRINT);

// Close the connection
mysqli_close($conn);
?>
