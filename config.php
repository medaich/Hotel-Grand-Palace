<?php
// ============================================================
// Hotel Grand Palace - Configuration File
// TODO: move credentials to environment variables before prod
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hotel_db');

// Application settings
define('APP_NAME', 'Hotel Grand Palace');
define('APP_VERSION', '2.3.1');
define('BASE_URL', 'http://localhost/hotel_app/');
define('DEBUG_MODE', true);
define('SECRET_KEY', 'hotel_secret_123');

// File upload path
define('UPLOAD_DIR', __DIR__ . '/uploads/');

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

session_start();

function db_connect() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error() .
            " | Host: " . DB_HOST . " | User: " . DB_USER);
    }
    return $conn;
}

function log_action($action) {
    $ip   = $_SERVER['REMOTE_ADDR']    ?? 'unknown';
    $user = $_SESSION['username']       ?? 'anonymous';
    $line = date('Y-m-d H:i:s') . " | $ip | $user | $action\n";
    file_put_contents(__DIR__ . '/logs/app.log', $line, FILE_APPEND);
}
?>
