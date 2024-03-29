<?php

$action = $_POST['ACTION'];
if ($action == "GetTopics"){
	GetTopics();
}
else if ($action == "AddTopic"){
	$topic = $_POST['TOPIC'];
	AddTopic($topic);
}
function GetTopics(){
	//Database credentials
	include 'credentials.php';
	
	//Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	//Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	//SQL Query
	$SqlGetTopics = "SELECT * FROM Topics WHERE Title != 'Company Updates'";
	$result = mysqli_query($conn, $SqlGetTopics);
	$conn->close();
	
	//Turn resulting posts into JSON
	$JSON = array(); 
	while ($row = mysqli_fetch_array($result)){
	$obj = array("topic"=>$row[0]); 
	array_push($JSON,$obj);	
	}

	//return all posts
	echo json_encode($JSON, JSON_PRETTY_PRINT);;  
}

function AddTopic($topic){
	//Database credentials
	include 'credentials.php';
	
	//Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	//Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	//SQL Query
	$SqlAddTopic = "INSERT INTO Topics VALUES('$topic')";
	$result = mysqli_query($conn, $SqlAddTopic);
	$conn->close();
	GetTopics();
}
?>
