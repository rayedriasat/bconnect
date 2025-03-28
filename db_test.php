<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database configuration
require_once 'config/database.php';

// Try to connect to the database
try {
    $db = new Database();
    $conn = $db->connect();
    echo "Database connection successful!";

    // Test a simple query
    $stmt = $conn->query("SELECT 1");
    $result = $stmt->fetch();
    echo "<br>Query test successful!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
