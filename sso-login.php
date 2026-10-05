<?php
/**
 * ═══════════════════════════════════════════════════
 * UNIVERSAL CENTRAL DEMO AUTHENTICATION SYSTEM (UCDAS)
 * Project SSO Adapter for CodeDiliver AI (ai_bot)
 * ═══════════════════════════════════════════════════
 */

declare(strict_types=1);

if (file_exists(__DIR__ . '/includes/functions.php')) {
    require_once __DIR__ . '/includes/functions.php';
    if (function_exists('secureSessionStart')) {
        secureSessionStart();
    } else if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} else if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = trim($_GET['role'] ?? 'admin');

// Ensure valid session values satisfying validateSession() in functions.php
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = ($role === 'developer') ? 'Lead Developer' : 'System Architect';
$_SESSION['user_email'] = 'demo@codecrush.local';
$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0';
$_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$_SESSION['last_activity'] = time();

if ($role === 'developer') {
    header('Location: my_projects.php');
    exit;
}

header('Location: dashboard.php');
exit;
