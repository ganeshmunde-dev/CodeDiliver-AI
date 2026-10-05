<?php
include_once 'includes/auth.php';
include_once 'includes/connection.php';

$user_id = $_SESSION['user_id'];

// Get user projects count and reports count for dashboard stats
$projects_stmt = $conn->prepare("SELECT COUNT(*) FROM project WHERE user_id = ?");
$projects_stmt->bind_param("i", $user_id);
$projects_stmt->execute();
$projects_stmt->bind_result($project_count);
$projects_stmt->fetch();
$projects_stmt->close();

$reports_stmt = $conn->prepare("SELECT COUNT(*) FROM reports r JOIN project p ON r.project_id = p.id WHERE p.user_id = ?");
$reports_stmt->bind_param("i", $user_id);
$reports_stmt->execute();
$reports_stmt->bind_result($reports_count);
$reports_stmt->fetch();
$reports_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CodeDiliver AI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .welcome-header {
            margin-bottom: 45px;
            animation: fadeUp 1s ease;
        }
        .welcome-header h1 {
            font-size: 40px;
            font-weight: 800;
            background: linear-gradient(to right, #60a5fa, #22d3ee, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .welcome-header p {
            color: #cbd5e1;
            font-size: 16px;
            margin-top: 5px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 45px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            transition: 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: rgba(96, 165, 250, 0.5);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.15);
        }
        .stat-num {
            font-size: 48px;
            font-weight: 800;
            color: #60a5fa;
            margin: 10px 0;
        }
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
        }
        .action-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            padding: 35px;
            border-radius: 24px;
            transition: 0.4s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .action-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 0 25px rgba(59, 130, 246, 0.3);
            border-color: rgba(96, 165, 250, 0.6);
        }
        .action-card h3 {
            font-size: 24px;
            margin-bottom: 12px;
            color: #ffffff;
        }
        .action-card p {
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .action-card .btn {
            margin: 0;
            text-align: center;
        }
    </style>
</head>
<body>

<nav>
    <div class="logo">CodeDiliver AI</div>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="chat.php">AI Chat</a></li>
        <li><a href="my_projects.php">My Projects</a></li>
        <li><a href="dashboard_reports.php">Reports</a></li>
        <li><a href="logout.php" style="color: #f43f5e;">Logout</a></li>
    </ul>
</nav>

<div class="dashboard-container">
    <div class="welcome-header">
        <?php 
        $display_user = str_ireplace([' (UCDAS)', '(UCDAS)'], '', $_SESSION['user_name'] ?? 'System Architect');
        ?>
        <h1>Welcome, <?php echo htmlspecialchars($display_user, ENT_QUOTES, 'UTF-8'); ?></h1>
        <p>Analyze and optimize your codebase with cutting-edge static analysis.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Projects Uploaded</h3>
            <div class="stat-num"><?php echo $project_count; ?></div>
            <p>Active code bases in scanner</p>
        </div>
        <div class="stat-card">
            <h3>Reports Generated</h3>
            <div class="stat-num"><?php echo $reports_count; ?></div>
            <p>Security and performance audits</p>
        </div>
    </div>

    <div class="actions-grid">
        <div class="action-card">
            <div>
                <h3>Upload New Project</h3>
                <p>Submit a new ZIP archive containing your PHP, HTML, CSS, or JS project to trigger automated audits.</p>
            </div>
            <a href="upload_project.php" class="btn">Upload ZIP</a>
        </div>

        <div class="action-card">
            <div>
                <h3>My Projects</h3>
                <p>Manage and review all your uploaded source codes, view past analyses, and trigger new scan runs.</p>
            </div>
            <a href="my_projects.php" class="btn btn-secondary">View Projects</a>
        </div>

        <div class="action-card">
            <div>
                <h3>Analysis Reports</h3>
                <p>Examine all security check alerts, bug detection counts, and performance scores for your scanned projects.</p>
            </div>
            <a href="dashboard_reports.php" class="btn btn-secondary">Review Reports</a>
        </div>
    </div>
</div>

<footer>
    © 2026 CodeDiliver AI. All Rights Reserved.
</footer>

<script src="/projects/ucdas-widget.js"></script>
</body>
</html>