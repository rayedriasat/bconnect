<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base URL
define('BASE_URL', '/bconnect');

// Check if user is logged in
if (isset($_SESSION['user'])) {
    // User is logged in, redirect to dashboard
    header('Location: ' . BASE_URL . '/views/dashboard/');
    exit();
} else {
    // User is not logged in, redirect to login page
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit();
}
