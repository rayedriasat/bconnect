<?php

// Detect environment based on server name
$environment = (strpos($_SERVER['SERVER_NAME'] ?? '', 'infinityfree') !== false || strpos($_SERVER['SERVER_NAME'] ?? '', '42web.io') !== false) ? 'production' : 'development';

// Set database credentials based on environment
if ($environment === 'production') {
    // InfinityFree database credentials
    define('DB_HOST', 'sql303.infinityfree.com');
    define('DB_USER', 'if0_38623557');
    define('DB_PASS', 'infiNSU1R');
    define('DB_NAME', 'if0_38623557_bloodconnect');
} else {
    // Local development database credentials
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'bloodconnect');
}

class Database
{
    private $connection;

    public function connect()
    {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                array(
                    PDO::ATTR_TIMEOUT => 5, // Add timeout to prevent hanging
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                )
            );
            return $this->connection;
        } catch (PDOException $e) {
            // More detailed error message for debugging
            echo "Database connection failed: " . $e->getMessage();
            exit;
        }
    }
}
