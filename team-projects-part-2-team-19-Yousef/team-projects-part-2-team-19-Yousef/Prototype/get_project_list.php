<?php

    function getProjects() {

        include 'credentials.php';

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL Query to fetch projects from the database
        $sql = "SELECT ProjectID, ProjectName, Description, Deadline, Username FROM Projects LEFT JOIN Employees ON Projects.TeamLeader = Employees.EmployeeID";       
 	$result = $conn->query($sql);

        $projects = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($projects, $row);
            }
        }

        // Close connection
        $conn->close();

        return $projects;
    }
?>
