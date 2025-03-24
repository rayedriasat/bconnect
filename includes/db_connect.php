<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->connect();

// Make available to other files through inclusion
return $conn;