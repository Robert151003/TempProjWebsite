<?php
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
    
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    //$status = $_POST['status']

    // SQL query
    //`Status` = '$status' 
    $pass = "UPDATE `To-Dos` SET 
         `Title` = '$title', 
         `Description` = '$description', 
         `Deadline` = '$date' 
         
         WHERE `ToDoId` = $id";

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
