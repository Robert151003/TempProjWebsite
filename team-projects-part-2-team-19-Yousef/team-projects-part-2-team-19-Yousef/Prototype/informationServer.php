<?php
session_start();
$title = $_POST['TITLE'];
$newTitle = "";
$desc = $_POST['DESC'];
$action = $_POST['ACTION'];
$employeeID = $_POST['EMPLOYEEID'];
$currentTopic = $_POST['CURRENTTOPIC'];
include 'credentials.php';
$conn = mysqli_connect($servername, $username,  $password, $dbname);
if ($action == "getPosts"){
	GetPosts($employeeID,$title,$currentTopic);
}
else if ($action == "addPost") {
	$newTitle = $_POST['NEWTITLE'];
	$postTopic = $_POST['POSTTOPIC'];
	$userid = $_SESSION['id'];
	AddPost($title , $desc, $employeeID, $newTitle,$postTopic,$currentTopic,$userid);
}

else if ($action == "deletePost") {
	DeletePost($employeeID,$title,$currentTopic);
}

else if ($action == "editPost") {
	$newTitle = $_POST['NEWTITLE'];
	EditPost($title,$desc,$employeeID,$newTitle,$currentTopic);
}

function EditPost($title,$desc,$employeeID,$newTitle,$currentTopic){ //POSTID only needs to be set for delete and edit post
	global $conn;							
	$postID = $_POST['POSTID'];
	
	
	
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	//SQL Query
	$SqlUpdatePost = "UPDATE Posts SET Title = '$newTitle', Content = '$desc' WHERE PostID = '$postID'";
	$result = mysqli_query($conn, $SqlUpdatePost);
	
	GetPosts($employeeID,$title,$currentTopic);
}

function DeletePost($employeeID,$title,$currentTopic){ //POSTID only needs to be set for delete and edit post
	global $conn;	
	$postID = $_POST['POSTID'];
	
	
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	//SQL Query
	$SqlGetUsername = "DELETE FROM Posts WHERE PostID = '$postID'";
	$result = mysqli_query($conn, $SqlGetUsername);
	
	GetPosts($employeeID,$title,$currentTopic);
}
function GetPostUser($employeeID){
	
	
	global $conn;	
	//Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	//SQL Query
	$SqlGetUsername = "SELECT Username FROM Employees WHERE EmployeeID='$employeeID'";
	$result = mysqli_query($conn, $SqlGetUsername);
	
	$username = "";
	while ($row = mysqli_fetch_array($result)){
			$username = $row[0];
		}
	return $username;
	
}

function AddPost($title, $desc, $employeeID, $newTitle, $postTopic, $currentTopic,$userid){
	global $conn;	
$SqlInsertPost = "INSERT INTO Posts VALUES (DEFAULT,'$newTitle', '$desc', DEFAULT, DEFAULT, '$userid', '$postTopic')";



// Check connection
if (!$conn) {
die("Connection failed: " . mysqli_connect_error());
}
$result = mysqli_query($conn, $SqlInsertPost);

GetPosts($employeeID,$title,$currentTopic);

}


function GetPosts($employeeID,$title,$currentTopic){
	
	global $conn;	
	
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	$SqlGetPosts = "";
	if(isset($_POST['COMPANYUPDATES'])){
	$SqlGetPosts = "SELECT * FROM Posts WHERE Topic = 'Company Updates'";	
	}
	else{
		if ($employeeID == "none"){
			if ($currentTopic == "all"){
				$SqlGetPosts = "SELECT PostID,Title,LEFT(Content, 50) AS Content,EditDate,Edited,EmployeeID,Topic FROM Posts WHERE Topic != 'Company Updates' AND Title LIKE CONCAT('%', '$title', '%')";
			}
			else{
				$SqlGetPosts = "SELECT PostID,Title,LEFT(Content, 50) AS Content,EditDate,Edited,EmployeeID,Topic FROM Posts WHERE Topic = '$currentTopic' AND Title LIKE CONCAT('%', '$title', '%')";
			}
		}
		else{
			if ($currentTopic == "all"){
				$SqlGetPosts = "SELECT PostID,Title,LEFT(Content, 50) AS Content,EditDate,Edited,EmployeeID,Topic FROM Posts WHERE Topic != 'Company Updates' AND EmployeeID = '$employeeID' AND Title LIKE CONCAT('%', '$title', '%')";
			}
			else{
				$SqlGetPosts = "SELECT PostID,Title,LEFT(Content, 50) AS Content,EditDate,Edited,EmployeeID,Topic FROM Posts WHERE Topic = '$currentTopic' AND EmployeeID = '$employeeID' AND Title LIKE CONCAT('%', '$title', '%')";
			}
		}
	};
	$result = mysqli_query($conn, $SqlGetPosts);
	
	
	$JSON = array(); //Turn resulting posts into JSON
	while ($row = mysqli_fetch_array($result)){
	$username = GetPostUser($row[5]);
	$obj = array("PostID"=>$row[0],"Title"=>$row[1],"Content"=>$row[2],"EditDate"=>$row[3],"State"=>$row[4],"EmployeeID"=>$row[5],"Username"=>$username); 
	array_push($JSON,$obj);
	
	}
	echo json_encode($JSON, JSON_PRETTY_PRINT);;  //return all posts
	
	
	
}
?>
