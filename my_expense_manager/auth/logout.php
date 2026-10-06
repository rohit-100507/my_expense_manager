<?php

session_start();

/* Clear all session variables */
$_SESSION = array();

/* Destroy the session */
session_destroy();

/* Prevent browser from using cached pages */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

/* Redirect to login page */
header("Location: login.php");
exit();

?>