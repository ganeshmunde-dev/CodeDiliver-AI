<?php
include_once __DIR__ . '/includes/connection.php';
include_once __DIR__ . '/includes/functions.php';

secureSessionStart();

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$message = "";
$messageType = "";

if (isset($_GET['registered'])) {
    $message = "Registration successful! Please login below.";
    $messageType = "success";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = "Please enter both email/username and password.";
        $messageType = "error";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ? OR name = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $email, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();

                if (password_verify($password, $row['password'])) {
                    @session_regenerate_id(true);

                    $_SESSION['user_id']       = $row['id'];
                    $_SESSION['user_name']     = $row['name'];
                    $_SESSION['user_email']    = $row['email'];
                    $_SESSION['user_agent']    = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $_SESSION['user_ip']       = $_SERVER['REMOTE_ADDR'] ?? '';
                    $_SESSION['last_activity'] = time();

                    $stmt->close();
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Invalid password. Please try again.";
                    $messageType = "error";
                }
            } else {
                $message = "No account found with this email or username.";
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Database query error.";
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CodeDiliver AI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<nav>
    <div class="logo">CodeDiliver AI</div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    </ul>
</nav>

<div class="auth-container">
    <div class="auth-card">
        <h2>Welcome Back</h2>

        <!-- UCDAS 1-Click Demo Login Banner -->
        <div style="margin-bottom: 22px; padding: 16px; background: rgba(96, 165, 250, 0.1); border: 1px dashed rgba(96, 165, 250, 0.45); border-radius: 14px; text-align: center;">
            <div style="font-size: 12px; font-weight: 700; color: #60a5fa; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.07em;">
                ⚡ 1-Click Demo Access
            </div>
            <p style="font-size: 12px; color: #94a3b8; margin-bottom: 12px;">Instant access with 0 credentials needed:</p>
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                <a href="sso-login.php?role=admin" style="display: inline-block; padding: 9px 16px; background: linear-gradient(45deg, #2563eb, #06b6d4); color: white; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; transition: 0.2s;">
                    👔 Demo as Admin
                </a>
                <a href="sso-login.php?role=developer" style="display: inline-block; padding: 9px 16px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.2); color: #e2e8f0; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; transition: 0.2s;">
                    💻 Demo as Developer
                </a>
            </div>
        </div>

        <div style="text-align: center; margin: 15px 0 20px 0; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">
            ─── or sign in manually ───
        </div>

        <?php
        if ($message !== "") {
            if ($messageType === "error") {
                echo showError($message);
            } else {
                echo showMessage($message);
            }
        }
        ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email or Username</label>
                <input type="text" id="email" name="email" required placeholder="admin or email@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" name="login" class="auth-btn">
                Login
            </button>
        </form>

        <div class="auth-link">
            <a href="register.php">Don't have an account? Create one</a>
        </div>
    </div>
</div>

<footer>
    © 2026 CodeDiliver AI. All Rights Reserved.
</footer>

</body>
</html>
