<?php

// Load environment variables
require_once __DIR__ . '/config/env_loader.php';
EnvLoader::load();

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', 1);
    ini_set('session.gc_maxlifetime', 1800); // 30 minutes
    
    session_set_cookie_params([
        'lifetime' => 1800,
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set to true if using HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    
    session_start();
    
    if (isset($_SESSION['login_time'])) {
        if (time() - $_SESSION['login_time'] > 1800) {
            session_unset();
            session_destroy();
            header('Location: login?error=session_expired');
            exit;
        }
    }
}

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'hokkaido_showroom_db');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Koneksi Database Gagal. Silakan hubungi administrator.");
}

require_once __DIR__ . '/src/Helpers/Logger.php';
require_once __DIR__ . '/src/Helpers/Validator.php';
require_once __DIR__ . '/src/Helpers/FileUploadHandler.php';

define('APP_NAME', 'Smart Presentation Hokkaido Showroom');
define('APP_VERSION', '1.0.0');
define('UPLOAD_MAX_SIZE', 10485760); // 10MB in bytes

// Timezone
date_default_timezone_set('Asia/Tokyo');
?>