
<?php

    // Database credentials
    $servername = "phpmyadmin.sci-project.lboro.ac.uk";
    $username = "team019";
    $password = "JM9kmieAWEm3PfUtnwtu";
    $dbname = "team019";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL Query to Insert data
    if(isset($_POST['submit'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $deadline = $_POST['deadline'];

        
        $sql = "INSERT INTO projects (name, description, deadline) VALUES ('$name', '$description', '$deadline')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // SQL Query to Fetch Data
    $sql = "SELECT id, name, description, deadline FROM projects";
    $result = $conn->query($sql);

    // Output data of each row
    if ($result->num_rows > 0) {       
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"]. " - Name: " . $row["name"]. " - Description: " . $row["description"]. " - Deadline: " . $row["deadline"]. "<br>";
        }
    } 
    else {
        echo "0 results";
    }

    $conn->close();
?>