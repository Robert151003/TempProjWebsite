<?php
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include 'credentials.php';

    // Establish the connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check if the connection was successful
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $userID = $_SESSION['id'];

    // SQL Query
    $SqlGetPosts = "SELECT * FROM `To-Dos` WHERE `EmployeeID` = $userID";
    $result = mysqli_query($conn, $SqlGetPosts);

    if (!$result) {
        die("Error in SQL query: " . mysqli_error($conn));
    }

    // Turn resulting posts into JSON
    $JSON = array(); 
    while ($row = mysqli_fetch_assoc($result)) {
        $obj = array(
            "To-DoID" => $row["ToDoID"],
            "Title" => $row["Title"],
            "Description" => $row["Description"],
            "Deadline" => $row["Deadline"],
            "EmployeeID" => $row["EmployeeID"],
            "Status" => $row["Status"]
        );
        array_push($JSON, $obj);
    }

    header('Content-Type: application/json');

    echo json_encode($JSON, JSON_PRETTY_PRINT);

    // Close the connection
    mysqli_close($conn);
?>
