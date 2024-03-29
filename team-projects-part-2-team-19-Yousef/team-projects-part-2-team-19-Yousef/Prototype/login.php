<?php

    include 'credentials.php';

    // Establish the connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check if the connection was successful
    if (!$conn) {
        echo ("Connection failed: " . mysqli_connect_error());
    }
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Checks the password
    $pass = "SELECT Password FROM `Employees` WHERE Email = '$username'";

    // Prepare and execute the statement
    $stmt = mysqli_prepare($conn, $pass);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $resultPass);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Checks if password is correct
    if($resultPass == $password){

        // Gets the username
        $member = "SELECT Username FROM `Employees` WHERE Email = '$username'";

        // Prepare and execute the statement
        $stmt = mysqli_prepare($conn, $member);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $resultMember);
        mysqli_stmt_fetch($stmt);    
        mysqli_stmt_close($stmt);


        header("Location:home.html?member=$resultMember");
        exit();
    }
    else{
        // Sends back to login page with incorrect password statement
        header("Location:index.html?login=failure");
        exit();
    }

    // Close the connection
    mysqli_close($conn);
?>

