<?php
// config.php - Tambahkan debug

// Cek apakah session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug session
// echo "Session ID: " . session_id() . "<br>";
// echo "Session data: ";
// print_r($_SESSION);

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'harum_bakery');

// URL Base
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base_path = '/project_uas_web';
define('BASE_URL', $protocol . $host . $base_path);

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

// Auto-load Classes
spl_autoload_register(function ($className) {
    $classFile = __DIR__ . '/class/' . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    } else {
        // Debug jika class tidak ditemukan
        error_log("Class not found: $className at $classFile");
    }
});
?>