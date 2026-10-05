<?php
include_once '../includes/auth.php';
include_once '../includes/connection.php';
include_once '../includes/functions.php';

// Sanitize the inputs received from query parameters
$qualityScore = (int)($_GET['quality'] ?? 0);
$bugsFound = (int)($_GET['bugs'] ?? 0);
$securityIssues = (int)($_GET['security'] ?? 0);
$performanceIssues = (int)($_GET['performance'] ?? 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analysis Result - CodeDiliver AI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .result-container {
            max-width: 1000px;
            margin: 60px auto;
            padding: 0 20px;
        }
        .header-title {
            text-align: center;
            margin-bottom: 40px;
            font-size: 38px;
            font-weight: 800;
            background: linear-gradient(to right, #60a5fa, #22d3ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            border-color: rgba(96, 165, 250, 0.5);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
        }
        .card h2 {
            font-size: 18px;
            color: #94a3b8;
            margin-bottom: 15px;
            font-weight: 500;
        }
        .value {
            font-size: 44px;
            font-weight: 800;
            color: #ffffff;
        }
        .value.score-high { color: #10b981; }
        .value.score-medium { color: #f59e0b; }
        .value.score-low { color: #ef4444; }
        
        .actions-section {
            text-align: center;
        }
        .back-btn {
            display: inline-block;
            padding: 14px 35px;
            background: linear-gradient(45deg, #2563eb, #06b6d4);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            transition: 0.3s;
        }
        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
        }
    </style>
</head>
<body>

<nav>
    <div class="logo">CodeDiliver AI</div>
    <ul>
        <li><a href="../dashboard.php">Dashboard</a></li>
        <li><a href="../chat.php">AI Chat</a></li>
        <li><a href="../my_projects.php">My Projects</a></li>
        <li><a href="../dashboard_reports.php">Reports</a></li>
        <li><a href="../logout.php" style="color: #f43f5e;">Logout</a></li>
    </ul>
</nav>

<div class="result-container">
    <h1 class="header-title">CodeDiliver AI Analysis Report</h1>

    <div class="cards-grid">
        <div class="card">
            <h2>Code Quality</h2>
            <div class="value <?php echo ($qualityScore >= 80) ? 'score-high' : (($qualityScore >= 60) ? 'score-medium' : 'score-low'); ?>">
                <?php echo $qualityScore; ?>/100
            </div>
        </div>

        <div class="card">
            <h2>Bugs Found</h2>
            <div class="value <?php echo ($bugsFound > 0) ? 'score-low' : 'score-high'; ?>">
                <?php echo $bugsFound; ?>
            </div>
        </div>

        <div class="card">
            <h2>Security Issues</h2>
            <div class="value <?php echo ($securityIssues > 0) ? 'score-low' : 'score-high'; ?>">
                <?php echo $securityIssues; ?>
            </div>
        </div>

        <div class="card">
            <h2>Performance Issues</h2>
            <div class="value <?php echo ($performanceIssues > 0) ? 'score-medium' : 'score-high'; ?>">
                <?php echo $performanceIssues; ?>
            </div>
        </div>
    </div>

    <div class="actions-section">
        <a href="../dashboard.php" class="back-btn">
            Back To Dashboard
        </a>
    </div>
</div>

<footer>
    © 2026 CodeDiliver AI. All Rights Reserved.
</footer>

</body>
</html>