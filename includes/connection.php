<?php
/**
 * =====================================================================
 * DATABASE CONNECTION — ai_bot / CodeDiliver AI
 * Multi-environment connection supporting both Local XAMPP & Live Server
 * =====================================================================
 */

// Disable default MySQLi exceptions in PHP 8.1+ to prevent unhandled fatal 500 errors
mysqli_report(MYSQLI_REPORT_OFF);

// Detect environment: Local XAMPP vs Live Server (ganeshcode.com / MilesWeb)
$serverName = $_SERVER['SERVER_NAME'] ?? '';
$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';

$is_local = ($serverName === 'localhost' || $serverName === '127.0.0.1' || 
             $remoteAddr === '127.0.0.1' || $remoteAddr === '::1');

if ($is_local) {
    // Local development credentials
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "aibot";
} else {
    // Live Server credentials (as configured on cPanel / MilesWeb hosting)
    $servername = "localhost";
    $username   = "YOUR_LIVE_DB_USERNAME";
    $password   = "YOUR_LIVE_DB_PASSWORD";
    $dbname     = "YOUR_LIVE_DB_NAME";
}

// Attempt connection
$conn = @new mysqli($servername, $username, $password, $dbname);

// Check connection status
if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error . " (User: " . $username . ", DB: " . $dbname . ")");
}

// Ensure UTF-8 charset
$conn->set_charset("utf8mb4");
?>
