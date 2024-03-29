<?php
    
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    include 'credentials.php';

    // Establish the connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check if the connection was successful
    if (!$conn) {
        echo ("Connection failed: " . mysqli_connect_error());
    }
    
    $projectID = $_POST['id'];

    $SqlGetPosts = "SELECT * FROM `Employees` WHERE `TeamNum` = '$projectID';";
    $result = mysqli_query($conn, $SqlGetPosts);

    if (!$result) {
        die("Error in SQL query: " . mysqli_error($conn));
    }

    // Turn resulting posts into JSON
    $JSON = array(); 
    while ($row = mysqli_fetch_assoc($result)) {
        $obj = array(
            "EmployeeID" => $row["EmployeeID"],
            "ManagerID" => $row["ManagerID"],
            "Username" => $row["Username"],
            "Password" => $row["Password"],
            "Email" => $row["Email"],
            "AccessLevel" => $row["AccessLevel"],
            "TeamNum" => $row["TeamNum"]
        );
        array_push($JSON, $obj);
    }

    echo json_encode($JSON, JSON_PRETTY_PRINT);
    
    // Close the connection
    mysqli_close($conn);
?>
