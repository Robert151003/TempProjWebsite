<?php
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Database credentials
    include "credentials.php";

    // Establish the connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check if the connection was successful
    if (!$conn) {
        echo ("Connection failed: " . mysqli_connect_error());
        exit();
    }
    
    $projectName = $_POST['project_title'];
    $description = $_POST['project_description'];
    $date = $_POST['project_date'];
    $teamLeader = $_POST['project_teamLeader'];
    $id = $_POST['project_id'];

    $stmt = $conn->prepare("UPDATE `Projects` SET `ProjectName` = '$projectName', `Description` = '$description', `Deadline` = '$date', `TeamLeader` = '$teamLeader' WHERE `ProjectID` = '$id'");

    
    // Execute the prepared statement
    if (!$stmt->execute()) {
        // Handle error
        error_log("Error: " . $stmt->error);
    } else {
        // Handle success
        error_log("Project added successfully");
    }
    
    // Close statement and connection
    $stmt->close();
    $conn->close();

    exit();
?>
