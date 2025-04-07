<?php
// start session - needed to clear session data
session_start();

// remove all session variables
session_unset();

// destroy the session completely
session_destroy();

// redirect to login page after logging out
header("Location: login.php");
exit();
?>
