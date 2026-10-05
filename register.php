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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name             = cleanInput($_POST['name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $message = "Please fill in all required fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long.";
        $messageType = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $messageType = "error";
    } else {
        // Check if email already exists using prepared statement
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $message = "An account with this email already exists.";
                $messageType = "error";
                $stmt->close();
            } else {
                $stmt->close();
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insertStmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                if ($insertStmt) {
                    $insertStmt->bind_param("sss", $name, $email, $hashed_password);
                    if ($insertStmt->execute()) {
                        $insertStmt->close();
                        header("Location: login.php?registered=1");
                        exit();
                    } else {
                        $message = "Registration failed! Please try again.";
                        $messageType = "error";
                        $insertStmt->close();
                    }
                } else {
                    $message = "Database error preparing statement.";
                    $messageType = "error";
                }
            }
        } else {
            $message = "Database error: " . $conn->error;
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
    <title>Register - CodeDiliver AI</title>
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
        <h2>Create Account</h2>

        <?php
        if ($message !== "") {
            if ($messageType === "error") {
                echo showError($message);
            } else {
                echo showMessage($message);
            }
        }
        ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="John Doe" value="<?php echo htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="john@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="At least 6 characters">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repeat password">
            </div>

            <button type="submit" name="register" class="auth-btn">
                Register
            </button>
        </form>

        <div class="auth-link">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>
</div>

<footer>
    © 2026 CodeDiliver AI. All Rights Reserved.
</footer>

</body>
</html>
