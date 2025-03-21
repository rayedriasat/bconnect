<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Define base URL
define('BASE_URL', '/bconnect');

// Redirect to login page
header('Location: ' . BASE_URL . '/views/auth/login.php');
exit();
