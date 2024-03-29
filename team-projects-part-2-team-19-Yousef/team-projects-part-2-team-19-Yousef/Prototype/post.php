<?php

$postID = 0;
$postID = $_POST['POSTID'];
GetPost($postID);

function GetPostUser($employeeID){
	//Database credentials
	include "credentials.php";
	
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	//SQL Query
	$SqlGetUsername = "SELECT Username FROM Employees WHERE EmployeeID='$employeeID'";
	$result = mysqli_query($conn, $SqlGetUsername);
	$conn->close();
	$username = "";
	while ($row = mysqli_fetch_array($result)){
			$username = $row[0];
		}
	return $username;
	
}
function GetPost($postID){
	//Database credentials
	include "credentials.php";
	
	//Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	//Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	//SQL Query
	$SqlGetPosts = "SELECT Title,Content,Edited,EmployeeID FROM Posts WHERE PostID='$postID'";
	$result = mysqli_query($conn, $SqlGetPosts);
	$conn->close();
	
	//Turn resulting posts into JSON
	$JSON = array(); 
	while ($row = mysqli_fetch_array($result)){
	$username = GetPostUser($row[3]);
	$obj = array("Title"=>$row[0],"Content"=>$row[1],"State"=>$row[2],"EmployeeName"=>$username); 
	array_push($JSON,$obj);	
	}

	//return all posts
	echo json_encode($JSON, JSON_PRETTY_PRINT);;  
}
?>
