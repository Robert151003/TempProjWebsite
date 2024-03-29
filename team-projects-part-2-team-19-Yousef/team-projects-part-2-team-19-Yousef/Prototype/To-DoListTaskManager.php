<?php
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

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
    
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $userID = $_SESSION['id'];

    // SQL query
    $pass = "INSERT INTO `To-Dos` (`ToDoID`, `Title`, `Description`, `Deadline`, `EmployeeID`, `Status`)
            VALUES (0, '$title', '$description', '$date', '$userID', 'todoList')
            ON DUPLICATE KEY UPDATE `EmployeeID` = IF(`EmployeeID` IS NULL, VALUES(`EmployeeID`), `EmployeeID`);";

    // Execute the query
    $result = mysqli_query($conn, $pass);

    if (!$result) {
        echo("Error description: " . mysqli_error($conn));
        exit();
    }

    // Close the connection
    mysqli_close($conn);

    // Redirect to tasks.html
    header("Location:todo.html");
    exit();
?>
