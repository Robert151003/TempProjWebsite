<?php

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

echo json_encode(["status" => "Session started", "perm" => $_SESSION["perm"], "username" => $_SESSION["username"], "id" => $_SESSION['id']]);

?>
