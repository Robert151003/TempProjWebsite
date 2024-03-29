<?php
// Creates Tasks

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'credentials.php';

// Establish the connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection was successful
if (!$conn) {
    echo ("Connection failed: " . mysqli_connect_error());
    exit();
}

// Check if the status is set in the POST request
if(isset($_POST['status'])) {
    // Get the task ID from the AJAX request
    $taskId = $_POST['task_id'];
    // Get the new status from the AJAX request
    $status = $_POST['status'];

    // SQL query to update the task status
    $pass = "UPDATE `To-Dos` SET 
        `Status` = '$status' 
        WHERE `ToDoId` = $taskId";

    // Execute the query
    $result = mysqli_query($conn, $pass);

    if (!$result) {
        echo("Error description: " . mysqli_error($conn));
        exit();
    }

    // Close the connection
    mysqli_close($conn);

    // Return a success message
    echo "Task status updated successfully";
} else {
    // If the status is not set in the POST request, return an error message
    echo "Error: Status not set in the request";
}
?>
