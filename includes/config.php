<?php
// Error reporting first
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$db_host = 'localhost';
$db_name = 'hostel_management';
$db_user = 'root';
$db_pass = '';

// Application configuration
define('SITE_URL', 'http://localhost/hostel-management');
define('SITE_NAME', 'Hostel Management System');

// Time zone
date_default_timezone_set('Asia/Kolkata');

// Define common paths
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Create uploads directory if it doesn't exist
if (!file_exists(UPLOADS_PATH)) {
    mkdir(UPLOADS_PATH, 0777, true);
}

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
} 