<?php
require_once __DIR__ . '/../config/database.php';

// Implement singleton pattern for database connection
if (!isset($GLOBALS['db_connection'])) {
    $db = new Database();
    $GLOBALS['db_connection'] = $db->connect();
}

// Make available to other files through inclusion
return $GLOBALS['db_connection'];