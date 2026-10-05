<?php
/**
 * =====================================================================
 * HELPER FUNCTIONS & SECURE SESSION MANAGEMENT
 * CodeDiliver AI / ai_bot
 * Fully compatible with PHP 7.0+, 8.0, 8.1, 8.2, 8.3+
 * =====================================================================
 */

function isHttps()
{
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1')) {
        return true;
    }
    if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
        return true;
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
        return true;
    }
    return false;
}

function secureSessionStart()
{
    if (session_status() === PHP_SESSION_NONE) {
        // Enforce cookie-only session IDs
        @ini_set('session.use_only_cookies', 1);

        if (!headers_sent()) {
            $cookieParams = session_get_cookie_params();
            $isSecure = isHttps();

            if (PHP_VERSION_ID >= 70300) {
                // PHP 7.3+ array syntax
                @session_set_cookie_params([
                    'lifetime' => 0,
                    'path'     => $cookieParams['path'] ?? '/',
                    'domain'   => $cookieParams['domain'] ?? '',
                    'secure'   => $isSecure,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            } else {
                // PHP 7.0-7.2 legacy syntax
                $path = ($cookieParams['path'] ?? '/') . '; SameSite=Lax';
                @session_set_cookie_params(
                    0,
                    $path,
                    $cookieParams['domain'] ?? '',
                    $isSecure,
                    true
                );
            }
        }

        @session_start();
    }
}

function validateSession()
{
    secureSessionStart();

    if (!isLoggedIn()) {
        return false;
    }

    // Inactivity timeout (e.g., 2 hours)
    $timeout = 7200;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        destroySession();
        return false;
    }
    $_SESSION['last_activity'] = time();

    return true;
}

function destroySession()
{
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    $_SESSION = array();

    if (!headers_sent() && ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        @setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"] ?? '/',
            $params["domain"] ?? '',
            $params["secure"] ?? false,
            $params["httponly"] ?? true
        );
    }
    @session_destroy();
}

function cleanInput($data)
{
    if ($data === null) {
        return '';
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

function redirect($page)
{
    header("Location: " . $page);
    exit();
}

function showMessage($message)
{
    return "<div class='alert alert-success' style='color:#10b981; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); padding:12px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; font-size:14px; text-align:center;'>" . cleanInput($message) . "</div>";
}

function showError($message)
{
    return "<div class='alert alert-danger' style='color:#f43f5e; background:rgba(244,63,94,0.1); border:1px solid rgba(244,63,94,0.3); padding:12px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; font-size:14px; text-align:center;'>" . cleanInput($message) . "</div>";
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUserId()
{
    return $_SESSION['user_id'] ?? null;
}
?>
