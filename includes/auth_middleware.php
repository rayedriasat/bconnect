<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

define('BASE_URL', '/bconnect');

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit();
}

$user = $_SESSION['user'];
$db = new Database();
$conn = $db->connect();

// Check if user is a donor
$stmt = $conn->prepare("SELECT * FROM Donor WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$isDonor = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user is an admin
$stmt = $conn->prepare("SELECT * FROM Admin WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$isAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
