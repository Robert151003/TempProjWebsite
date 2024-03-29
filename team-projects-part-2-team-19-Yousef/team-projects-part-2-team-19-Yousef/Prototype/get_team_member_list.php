<?php

    include 'credentials.php';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    //Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //SQL Query to fetch projects from the database
    $sql = "SELECT `EmployeeID`, `Username` FROM `Employees`;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        //output data of each row
        while($row = $result->fetch_assoc()) {
            //Add each row to the teamMembers array
            $teamMembers[] = $row;
        }
    } else {
        echo "0 results";
    }

    //Close connection
    $conn->close();

    //Convert the $teamMembers array to JSON and output it
    header('Content-Type: application/json');
    echo json_encode($teamMembers);

?>
