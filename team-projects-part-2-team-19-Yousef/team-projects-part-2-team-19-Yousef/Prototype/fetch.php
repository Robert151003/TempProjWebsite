<?php
include 'credentials.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

// Import namespaces with use statements
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

$conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//--------------- Task Functions ------------------//

function processQuery() {
    global $conn;

    $query = "";
    $query = $_GET['query'];
    $result = mysqli_query($conn, $query);
    $array = array();

    if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_assoc($result)){
            $array[] = $row;
        }
    }
    $json_data = json_encode($array);
    echo $json_data;
}

function cleanInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    $output = str_replace(';', '', $input); // Measure against SQL Injection

    return $input;
}

function loadTasks() {
    global $conn;

    $id = $_SESSION['id'];
    $query = "SELECT 
    t.TaskID AS id,
    t.Title AS title,
    t.Description AS description,
    p.ProjectName AS project,
    t.Deadline AS duedate,
    t.Pin AS pin,
    t.Status AS status,
    e.Email AS email,
    e.Username AS assignedBy
FROM
(SELECT * FROM Tasks WHERE AssignedTo = $id AND Status != 2) t
JOIN 
    Employees e ON t.AssignedBy = e.EmployeeID
JOIN
    Projects p ON t.ProjectID = p.ProjectID;";

    $result = mysqli_query($conn, $query);
    $array = array();

    if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_assoc($result)){
            $array[] = $row;
        }
    }
    $json_data = json_encode($array);
    echo $json_data;
}

function loadTeamTasks() {
    global $conn;

    if ($_SESSION['perm'] == "Team Member") {
        echo "Error: Permission denied.";
        return;
    } else if ($_SESSION['perm'] == "Manager") {
        $projectId = cleanInput($_GET['projectId']);
    } else if ($_SESSION['perm'] == "Team Leader") {
        $id = $_SESSION['id'];

        $query = "SELECT ProjectID FROM `Project-Employees` WHERE EmployeeID = $id AND isLeader = 1;";
        $result = mysqli_query($conn, $query);
        $array = array();
        if (mysqli_num_rows($result) > 0){
            while ($row = mysqli_fetch_assoc($result)){
                $array[] = $row;
            }
        }
        if (!$array) {
            echo "Error: Team Leader has no entry to project employees";
            return;
        }
        $projectId = $array[0]['ProjectID'];
    }
    $subquery = "(SELECT * FROM Tasks WHERE ProjectID = '$projectId')";
    if ($_SESSION['perm'] == "Manager" && $projectId == "all") {
        $subquery = "(SELECT * FROM Tasks WHERE AssignedTo IS NULL)";
    }

    $query = "SELECT 
    t.TaskID AS id,
    t.Title AS title,
    t.Description AS description,
    t.Deadline AS duedate,
    t.Status AS status,
    t.ProjectID AS project,
    e.Email AS email,
    e.Username AS assignedBy,
    f.EmployeeID AS assignedTo
    FROM
    $subquery t
    LEFT JOIN 
    Employees e ON t.AssignedBy = e.EmployeeID
    LEFT JOIN
    Employees f ON t.AssignedTo = f.EmployeeID;";

    $result = mysqli_query($conn, $query);
    $array = array();
    if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_assoc($result)){
            $array[] = $row;
        }
    }
    $json_data = json_encode($array);
    echo $json_data;
}

function pinTask() {
    global $conn;

    $togglePin = cleanInput($_GET['togglePin']);
    $id = cleanInput($_GET['id']);
    $query = "UPDATE Tasks SET pin = $togglePin WHERE TaskID = $id;";
    $result = mysqli_query($conn, $query);

    echo $result;
}

function confirmTask() {
    global $conn;



    $id = cleanInput($_GET['id']);
    $userid = cleanInput($_SESSION['id']);
    $query = "SELECT TaskID FROM Tasks WHERE TaskID = $id AND AssignedTo = $userid;";
    if ($_SESSION['perm'] == "Manager" && $_GET['final']) {
        $query = "SELECT TaskID FROM Tasks WHERE TaskID = $id;";
    }
    else if ($_SESSION['perm'] == "Team Leader" && $_GET['final']) {
        $query = "SELECT TaskID FROM Tasks WHERE TaskID = $id AND AssignedBy IN (SELECT EmployeeID FROM Employees WHERE EmployeeID = $userid OR AccessLevel = 'Manager');";
    }
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) <= 0){
        echo "DENIED";
        die();
    }

    if ($_GET['final']) {
        $status = 2;
    } else {
        $status = 1;
    }

    $query = "UPDATE Tasks SET Status = $status WHERE TaskID = $id;";
    $result = mysqli_query($conn, $query);
    echo "SUCCESS";
}

//---------------------- Login Functions -------------------------//
function handleLogin() {
    global $conn;

    $uname = cleanInput($_GET['username']);
    $pass = cleanInput($_GET['password']);
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    $query = "SELECT Username, Password, EmployeeID, AccessLevel FROM Employees WHERE Username = '$uname';";
    $result = mysqli_query($conn, $query);
    $array = array();

    if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_assoc($result)){
            $array[] = $row;
        }
    }
    else {
        echo "failure";
        die();
    }
    $fetched_pass = $array[0]["Password"];
    if (password_verify($pass, $fetched_pass)) {
        $_SESSION['weAllLoveJaiden'] = 1;
        $_SESSION['id'] = $array[0]["EmployeeID"];
        $_SESSION['username'] = $array[0]["Username"];
        $_SESSION['perm'] = $array[0]["AccessLevel"];
        echo "success";
    }
}


function handleLogout() {
    session_start();
    $_SESSION = [];
    session_destroy();
}

function sendForgotEmail() {
    global $conn;

    $email = cleanInput($_GET['email']);
    echo $email;
    $query = "SELECT Email FROM Employees WHERE Email = '$email';";
    $result = mysqli_query($conn, $query);
    $token = "";

    if (mysqli_num_rows($result) <= 0){
        echo -1; // Invalid email error
        die();
    }

    $query = "SELECT Date FROM `Employee-Token` WHERE Email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if (!mysqli_num_rows($result) <= 0) {
        $row = mysqli_fetch_assoc($result);
        $currentTime = strtotime(gmdate("Y-M-d H:i:s", time()));
        $timeDiff = $currentTime - strtotime($row['Date']); // Check difference of time between token creation and now
        if ($timeDiff >= 300) { // If token has been created more than 5 minutes
            // Generate new token
            $token = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(1,10))), 20, 20);
            $query = "UPDATE `Employee-Token` SET Token = '$token' WHERE Email = '$email';";
            mysqli_query($conn, $query);
        } else {
            echo -2; // Token already generated less than 5 minutes prior.
            die();
        }
        echo $timeDiff;
    } else {
        // If token doesn't exist, generate one
        $token = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(1,10))), 20, 20);
        $query = "INSERT INTO `Employee-Token` (`Email`, `Token`) VALUES ('$email', '$token');";
        mysqli_query($conn, $query);
    }
    // After generating token and updating database, send recovery email to recipient.
    sendRecoveryEmail($email, $token);
    
}

function sendRecoveryEmail($email, $token) {

    $to = $email;
    $subject = 'Account Recovery';
    $token = cleanInput($token);

    if ($token == "") {
        die();
    }

    $recoveryLink = "http://localhost/team-projects-part-2-team-19/Prototype/resetPassword.html?token=$token";

    // Email generic format/message
    $message = "Subject: Password Recovery Process Initiated\nDear [Recipient's Name],\nOur system has received a request to recover your account password. To facilitate this process, please follow the instructions below:\n
    Recovery Link: $recoveryLink\nThis link will allow you to reset your password securely. Please note that the link is valid for ten minutes for security purposes.
    If you did not initiate this password recovery process, you can safely ignore this email. Our system automatically initiated this process based on the request received.\n";
    $message = wordwrap($message, 70, "\r\n");

    $headers = "From: System@make-it-all.co.uk\r\n";
    $headers .= "Reply-To: System@make-it-all.co.uk\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'team19test@gmail.com';
    $mail->Password = 'Teamtest19!';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Port = 587;

    $mail->setFrom('team19test@gmail.com', 'System');
    $mail->addAddress($email, '');

    $mail->Subject = $subject;
    $mail->Body = $message;

// Send email
    $mail->send();
}

function registerUser() {
    global $conn;

    // Extract email and check validity
    $email = cleanInput($_GET['email']);
    $emailSplit = explode("@", $email);
    if (isSet($emailSplit[1])) {
        $domain = $emailSplit[1];
    } else {
        echo -9; // Invalid Email
        die();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $domain != "make-it-all.co.uk") {
        echo -9; // Invalid Email
        die();
    }

    // Extract username if available, or create it from email address
    if ($_GET['username'] != "") {
        $username = cleanInput($_GET['username']);
        if (preg_match('/[^a-zA-Z0-9-_]/', $username)) {
            echo -12; // Username has illegal characters
            die();
        }
    } else {
        $usernameString = explode("@", $email)[0];
        $username = $usernameString;
    }

    // Extract passwords and check their validitiy
    $newPassword1 = cleanInput($_GET['pass1']);
    $newPassword2 = cleanInput($_GET['pass2']);

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
    $hashedpass = password_hash($newPassword1, PASSWORD_DEFAULT);

    // Update the password, and check if it successfully did so
    $query = "SELECT EmployeeID FROM Employees WHERE Email = '$email';";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        echo -10; // return -10, email already registered
    } else {
        $query = "INSERT INTO Employees (Username, ManagerID, Email, Password, AccessLevel) VALUES ('$username', 0, '$email', '$hashedpass', 'Team Member');";
        $result = mysqli_query($conn, $query);
        if ($result) echo 1;
        else         echo -11;
    }
    mysqli_close($conn);
}
// ----------------- Team Leader functions ------------------------ //

function createTask() {
    global $conn;

    if ($_SESSION['perm'] == "Team Member") {
        echo "Error: Permission denied.";
        return;
    } else if ($_SESSION['perm'] == "Manager") {
        $projectID = cleanInput($_GET['projectId']);
    } else if ($_SESSION['perm'] == "Team Leader") {
        $id = $_SESSION['id'];

        $query = "SELECT ProjectID FROM `Project-Employees` WHERE EmployeeID = $id AND isLeader = 1;";
        $result = mysqli_query($conn, $query);
        $array = array();
        if (mysqli_num_rows($result) > 0){
            while ($row = mysqli_fetch_assoc($result)){
                $array[] = $row;
            }
        }
        if (!$array) {
            echo "Error: Team Leader has no entry to project employees";
            return;
        }
        $projectId = $array[0]['ProjectID'];
    }

    $title = cleanInput($_GET['title']);
    $desc = cleanInput($_GET['desc']);
    $deadline = cleanInput($_GET['deadline']);
    
    
    if ($deadline == "") {
        echo -1;
        die();
    }
    

    $query = "INSERT INTO Tasks (Title, Description, Deadline, ProjectID) VALUES ('$title', '$desc', '$deadline', '$projectID');";
    $result = mysqli_query($conn, $query);
    if ($result) {
        echo "1";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

function assignTask() {
    global $conn;

    if ($_SESSION['perm'] == "Team Member") {
        echo "Error: Permission denied.";
        return;
    }

    $assigner = cleanInput($_SESSION['id']);
    $assigned = cleanInput($_GET['assignedId']);
    $selectedTask = cleanInput($_GET['taskId']);

    $query = "UPDATE Tasks SET AssignedTo = '$assigned', AssignedBy = '$assigner' WHERE TaskID = '$selectedTask'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        echo "1";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

function unassignTask() {
    global $conn;

    if ($_SESSION['perm'] == "Team Member") {
        echo "Error: Permission denied.";
        return;
    }

    $selectedTask = cleanInput($_GET['taskId']);

    $query = "UPDATE Tasks SET AssignedTo = NULL, AssignedBy = NULL, Pin = 0, Status = 0 WHERE TaskID = '$selectedTask'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        echo "1";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

function memberTaskTotals() {
    global $conn;

    if ($_SESSION['perm'] == "Team Member") {
        echo "Error: Permission denied.";
        return;
    } else if ($_SESSION['perm'] == "Team Leader") {
        $userid = $_SESSION['id'];
        $query = "SELECT ProjectID FROM `Project-Employees` WHERE EmployeeID = '$userid' AND isLeader = 1;";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) < 0) {
            echo -1;
            die();
        } else {
            $row = mysqli_fetch_assoc($result);
            $selectedProject = $row['ProjectID'];
        }
    } else {
        $selectedProject = cleanInput($_GET['projectId']);
    }

    $query = "SELECT e.Username AS EmployeeName, COUNT(t.AssignedTo) AS TotalTasks, SUM(CASE WHEN t.Status = 2 THEN 1 ELSE 0 END) AS FinishedTasks FROM Employees e INNER JOIN Tasks t ON e.EmployeeID = t.AssignedTo AND t.ProjectID = '$selectedProject' GROUP BY e.EmployeeID, e.Username;";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Error: " . mysqli_error($conn);
    } else {
        $array = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $array[] = $row;
            }
        }
        $json_data = json_encode($array);
        echo $json_data;
    }
    mysqli_free_result($result);
}

function fetchProjectName() {
    global $conn;

    if ($_SESSION['perm'] == "Team Member") {
        echo "Error: Permission denied.";
        return;
    }
    $userid = $_SESSION['id'];
    $query = "SELECT ProjectName FROM Projects WHERE ProjectID IN (SELECT ProjectID FROM `Project-Employees` WHERE EmployeeID = '$userid' AND isLeader = 1);";

    if ($_SESSION['perm'] == "Manager") {
        $projectId = $_GET['projectId'];
        $query = "SELECT ProjectName FROM Projects WHERE ProjectID = '$projectId';";
    }

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $json_data = json_encode($row);
    echo $json_data;
}
// ------------------- Manager functions -------------------------- //
function changeTeamLead() {
    global $conn;

    if ($_SESSION['perm'] != "Manager") {
        echo "Error: Permission denied.";
        return;
    }

    $userid = cleanInput($_GET['userid']);
    $selectedProject = cleanInput($_GET['projectId']);
    // Query from the database how many teams are led by the current TeamLeader of the target team.
    $query1 = "SELECT COUNT(*) AS team_count FROM Projects WHERE TeamLeader IN (SELECT TeamLeader FROM Projects WHERE TeamLeader = '$userid');";
    $result1 = mysqli_query($conn, $query1);
    $numTeamsLed = mysqli_fetch_assoc($result1);
    if ($numTeamsLed['team_count'] <= 1) {
        // If the current Team Leader only leads one team (current chosen team), change his permission to team member unless he's Manager
        $tempQuery = "UPDATE Employees SET AccessLevel = 'Team Member' WHERE EmployeeID IN (SELECT TeamLeader FROM Projects WHERE ProjectID = $selectedProject) AND AccessLevel != 'Manager';
                      UPDATE `Project-Employees` SET isLeader = 0 WHERE EmployeeID IN (SELECT TeamLeader FROM Projects WHERE ProjectID = $selectedProject) AND ProjectID = '$selectedProject'";
        $result = mysqli_multi_query($conn, $tempQuery);
        while(mysqli_next_result($conn)) {
            if (!mysqli_more_results($conn)) break;
        }
    }

    // Update team's teamleader, and change their permission to Team Leader if they aren't already manager.
    $query = "UPDATE Projects SET TeamLeader = '$userid' WHERE ProjectID = '$selectedProject'; 
    UPDATE Employees SET AccessLevel = 'Team Leader' WHERE EmployeeID = '$userid' AND AccessLevel != 'Manager';
    UPDATE `Project-Employees` SET isLeader = 1 WHERE EmployeeID = '$userid' AND ProjectID = '$selectedProject';";

    $result = mysqli_multi_query($conn, $query);
    if ($result) {
        echo "1";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
function getAllEmployees() {
    global $conn;

    if ($_SESSION['perm'] != "Manager") {
        echo "Error: Permission denied.";
        return;
    }
    $query = "SELECT Username, EmployeeID FROM Employees;";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_assoc($result)){
            $array[] = $row;
        }
    }
    $json_data = json_encode($array);
    echo $json_data;
}
// --------------------- Global Functions ------------------------- //

//function test() {
//    include_once('check_session.php');
//    $bruh = checkSession();
//    echo $bruh;
//}

function test() {
    global $conn;

    if ($_SESSION['perm'] != "Manager") {
        echo "Error: Permission denied.";
        return;
    }

    $projectName = cleanInput($_GET['projectName']);
    $desc = cleanInput($_GET['desc']);
    $deadline = cleanInput($_GET['deadline']);

    $dateTime = DateTime::createFromFormat('Y-m-d', $deadline);
    $isDate = $dateTime && $dateTime->format('Y-m-d') === $deadline;

    if (!$isDate) {
        return "Error: Invalid Date";
    }

    $query = "INSERT INTO `Projects` (`ProjectID`, `ProjectName`, `Description`, `Deadline`, `TeamLeader`) VALUES (NULL, '$projectName', '$desc', '$deadline', NULL);";
    $result = mysqli_query($conn, $query);
    if ($result) {
        echo "1";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// ---------------------------------------------------------------- //

function fetchHomePosts() {
    global $conn;

    $query = "SELECT Title, Content FROM Posts WHERE Topic = 'Company Updates';";
    $result = mysqli_query($conn, $query);
    $array = array();

    if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_assoc($result)){
            $array[] = $row;
        }
    }
    $json_data = json_encode($array);
    echo $json_data;
}

function fetchHomeTasks() {
    global $conn;

    $id = $_SESSION['id'];

    $query = "SELECT t.Title AS title, t.Description AS description, p.ProjectName AS project, t.Deadline AS duedate 
    FROM
    (SELECT * FROM Tasks WHERE AssignedTo = $id AND Pin = '1') t
    JOIN 
    Employees e ON t.AssignedBy = e.EmployeeID
    JOIN
    Projects p ON t.ProjectID = p.ProjectID;";
    $result = mysqli_query($conn, $query);
    $array = array();

    if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_assoc($result)){
            $array[] = $row;
        }
    }
    $json_data = json_encode($array);
    echo $json_data;
}

// ---------------------------------------------------------------- //

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        //case 'processQuery':
        //    processQuery();
        //    break;
        case 'loadTasksTemp':
            loadTasksTemp();
            break;
        case 'handleLogin':
            handleLogin();
            break;
        case 'handleLogout':
            handleLogout();
            break;
        case 'loadTasks':
            loadTasks();
            break;
        case 'pinTask':
            pinTask();
            break;
        case 'confirmTask':
            confirmTask();
            break;
        case 'changeTeamLead':
            changeTeamLead();
            break;
        case 'createTask':
            createTask();
            break;
        case 'assignTask':
            assignTask();
            break;
        case 'unassignTask':
            unassignTask();
            break;
        case 'memberTaskTotals':
            memberTaskTotals();
            break;
        case 'test':
            test();
            break;
        case 'sendForgotEmail':
            sendForgotEmail();
            break;
        case 'sendRecoveryEmail':
            sendRecoveryEmail();
            break;
        case 'registerUser':
            registerUser();
            break;
        case 'fetchHomePosts':
            fetchHomePosts();
            break;
        case 'fetchHomeTasks':
            fetchHomeTasks();
            break;
        case 'loadTeamTasks':
            loadTeamTasks();
            break;
        case 'fetchProjectName':
            fetchProjectName();
            break;
        case 'getAllEmployees':
            getAllEmployees();
            break;
        default:
            break;
    }
}



?>

