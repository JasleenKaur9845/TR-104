<?php
// ============================================================
// config.php — Database Configuration
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ← change to your MySQL username
define('DB_PASS', '');           // ← change to your MySQL password
define('DB_NAME', 'backup_monitor');

/**
 * Returns a new MySQLi connection.
 * Exits with JSON error on failure.
 */
function getDB(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
        header('Content-Type: application/json');
        die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
?>
