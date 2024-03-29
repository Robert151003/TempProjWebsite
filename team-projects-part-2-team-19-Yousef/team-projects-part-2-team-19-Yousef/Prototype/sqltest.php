<?php
$mysqli = new mysqli('localhost', 'team019', 'JM9kmieAWEm3PfUtnwtu', 'team019');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
echo 'Connected successfully.';
$mysqli->close();
?>
