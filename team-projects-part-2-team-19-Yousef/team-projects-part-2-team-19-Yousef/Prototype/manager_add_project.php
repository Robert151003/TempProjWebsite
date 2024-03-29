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
    
    $projectName = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $teamLeader = $_POST['teamLeader'] === '0' ? NULL : $_POST['teamLeader'];
    
    /*
    error_log("Team Leader: " . $teamLeader);
    error_log("First: " . ($teamLeader === 0 ? 'true' : 'false'));
    error_log("Second: " . ($teamLeader === '0' ? 'true' : 'false'));
    error_log("Third: " . ($teamLeader === '' ? 'true' : 'false'));
    error_log("Fourth: " . ($teamLeader === NULL ? 'true' : 'false')); */

    $stmt = $conn->prepare("INSERT INTO `Projects` (`ProjectName`, `Description`, `Deadline`, `TeamLeader`) VALUES (?, ?, ?, ?)");

    // Bind parameters to the prepared statement
    $stmt->bind_param("ssss", $projectName, $description, $date, $teamLeader);
    
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
    $stmt = $conn->prepare("UPDATE Employees SET AccessLevel = 'Team Leader' WHERE EmployeeID = '$teamLeader' AND AccessLevel = 'Team Member'");
    if (!$stmt->execute()) {
        // Handle error
        error_log("Error: " . $stmt->error);
    } else {
        // Handle success
        error_log("Role changed successfully");
    }

    $stmt->close();
    $conn->close();
    exit();
?>
