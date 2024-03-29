<?php
function cleanInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);

    return $input;
}


// Database credentials
include 'credentials.php';

// Establish the connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection was successful
if (!$conn) {
    echo ("Connection failed: " . mysqli_connect_error());
}

// Check if token is in database
$token = cleanInput($_GET['token']);
$query = "SELECT Email, Date FROM `Employee-Token` WHERE Token = '$token';";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) <= 0) {
    echo -1; // Token not found, retry
    die();
}


$newPassword1 = $_GET['pass1'];
$newPassword2 = $_GET['pass2'];

if (!(strlen($newPassword1) >= 6)) {
    echo -3; // Password shorter than 6 characters
    die();
}
if (!(strlen($newPassword1) <= 20)) {
    echo -4;  // Password longer than 20 characters
    die();
}
if (!preg_match('/[A-Z]/', $newPassword1)) {
    echo -5;  // Password contains no capital letter
    die();
}
if (!preg_match('/\d/', $newPassword1)) {
    echo -6;  // Password contains no number
    die();
}
if (!preg_match('/[!@#$%^&*()_+{}\[\]:<>,.?~\\/-]/', $newPassword1)) {
    echo -7;  // Password contains no special character
    die();
}

if ($newPassword1 != $newPassword2) {
    echo 0; // Password mismatch
    die();
}

// Get email and date from token
$row = mysqli_fetch_assoc($result);
$email = $row['Email'];
$currentTime = strtotime(gmdate("Y-M-d H:i:s", time()));
$timeDiff = $currentTime - strtotime($row['Date']); // Check difference of time between token creation and now
if ($timeDiff >= 600) { // If token has been created more than 10 minutes, terminate
    $query = "DELETE FROM `Employee-Token` WHERE Token = '$token';"; // Remove token as its too old to be used
    mysqli_query($conn, $query);
    echo -2;  // return -2 error code
    die();
} else {
    // Update the password, and check if it successfully did so
    $query = "UPDATE Employees SET Password = '$newPassword1' WHERE Email = '$email'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        // Delete the token
        $query = "DELETE FROM `Employee-Token` WHERE Token = '$token';";
        mysqli_query($conn, $query);
        
        echo 1; // return 1, indicating success
    } else {
        echo -8; // return -8, indicating error in setting password
    }
    mysqli_close($conn);
}
?>
