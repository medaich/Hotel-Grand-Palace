<?php
// ============================================================
// Hotel Grand Palace - Configuration File
// ============================================================

// ── Load .env ────────────────────────────────────────────────
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // Strip inline comments and surrounding quotes
        $value = preg_replace('/\s+#.*$/', '', $value);
        $value = trim($value, '"\'');

        if ($key !== '') {
            putenv("$key=$value");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// ── Helper ───────────────────────────────────────────────────
function env(string $key, $default = null) {
    $val = $_ENV[$key] ?? getenv($key);
    return ($val !== false && $val !== null) ? $val : $default;
}

// ── Constants ────────────────────────────────────────────────
define('DB_HOST',     env('DB_HOST',     'localhost'));
define('DB_USER',     env('DB_USER',     'root'));
define('DB_PASS',     env('DB_PASS',     ''));
define('DB_NAME',     env('DB_NAME',     'hotel_db'));

define('APP_NAME',    env('APP_NAME',    'Hotel Grand Palace'));
define('APP_VERSION', env('APP_VERSION', '2.3.1'));
define('BASE_URL',    env('BASE_URL',    'http://localhost:8000/'));
define('DEBUG_MODE',  env('DEBUG_MODE',  'false') === 'true');
define('SECRET_KEY',  env('SECRET_KEY',  'changeme'));

define('UPLOAD_DIR',  __DIR__ . '/' . trim(env('UPLOAD_DIR', 'uploads/'), '/') . '/');
define('LOG_FILE',    __DIR__ . '/' . env('LOG_FILE', 'logs/app.log'));

// ── Error reporting ──────────────────────────────────────────
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

session_start();

// ── Database ─────────────────────────────────────────────────
function db_connect() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error() .
            " | Host: " . DB_HOST . " | User: " . DB_USER);
    }
    return $conn;
}

// ── Logging ──────────────────────────────────────────────────
function log_action($action) {
    $ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user = $_SESSION['username']   ?? 'anonymous';
    $line = date('Y-m-d H:i:s') . " | $ip | $user | $action\n";
    file_put_contents(LOG_FILE, $line, FILE_APPEND);
}
?>
