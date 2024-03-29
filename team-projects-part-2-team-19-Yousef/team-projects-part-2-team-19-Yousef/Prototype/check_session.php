<?php

if (session_status() !== PHP_SESSION_ACTIVE) session_start(); // If the session isnt started yet, start it

function checkSession()
{   // 'weAllLoveJaiden' is a security index, (no one would guess it, no one would set it) to prevent invalid session
    if (empty($_SESSION) || !isset($_SESSION['weAllLoveJaiden'])) { // If the session is empty, it wasnt initiatized in login (invalid)
        $_SESSION = array();
        session_destroy(); // destroys session
        echo "false"; // echoes false, handled in js to redirect to index
    } else {
        $array = [$_SESSION['id'], $_SESSION['username'], $_SESSION['perm']]; // Get info from session
        $json_data = json_encode($array);
        echo $json_data; // return such info
    }
}
if (isset($_GET['ajax'])) { // if index named ajax, run the function;
    if ($_GET['ajax'] == 1) checkSession();
}
?>
