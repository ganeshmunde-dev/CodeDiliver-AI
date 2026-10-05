<?php
include_once 'includes/auth.php';
include_once 'includes/connection.php';
include_once 'includes/functions.php';

$user_id = $_SESSION['user_id'];

// Secure reports access by joining with projects to ensure ownership
$stmt = $conn->prepare("
    SELECT r.*, p.project_name, p.upload_date 
    FROM reports r 
    JOIN project p ON r.project_id = p.id 
    WHERE p.user_id = ? 
    ORDER BY r.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analysis Reports - CodeDiliver AI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .reports-container {
            max-width: 1000px;
            margin: 60px auto;
            padding: 0 20px;
        }
        .header-section {
            margin-bottom: 40px;
        }
        .header-section h2 {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(to right, #60a5fa, #22d3ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .report-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        .report-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            transition: 0.3s;
        }
        .report-card:hover {
            border-color: rgba(96, 165, 250, 0.4);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.1);
        }
        .report-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .report-title-row h3 {
            font-size: 22px;
            color: #ffffff;
        }
        .report-date {
            font-size: 13px;
            color: #94a3b8;
        }
        .metrics-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .metric-item {
            background: rgba(255, 255, 255, 0.02);
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .metric-item span {
            display: block;
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .metric-val {
            font-size: 22px;
            font-weight: 700;
        }
        .metric-val.high { color: #10b981; }
        .metric-val.medium { color: #f59e0b; }
        .metric-val.low { color: #ef4444; }
        
        .report-suggestions {
            background: rgba(96, 165, 250, 0.05);
            border-left: 4px solid #60a5fa;
            padding: 15px 20px;
            border-radius: 4px;
            color: #cbd5e1;
            font-size: 14px;
            line-height: 1.6;
        }
        .no-reports {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 20px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
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

<div class="reports-container">
    <div class="header-section">
        <h2>Analysis Reports</h2>
    </div>

    <div class="report-list">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $qScore = (int)$row['code_quality_score'];
                $securityCount = (int)$row['security_score'];
                $bugsCount = (int)$row['bugs_found'];
                ?>
                <div class="report-card">
                    <div class="report-title-row">
                        <h3><?php echo htmlspecialchars($row['project_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <span class="report-date"><?php echo htmlspecialchars($row['upload_date'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    
                    <div class="metrics-summary">
                        <div class="metric-item">
                            <span>Code Quality</span>
                            <div class="metric-val <?php echo ($qScore >= 80) ? 'high' : (($qScore >= 60) ? 'medium' : 'low'); ?>">
                                <?php echo $qScore; ?>/100
                            </div>
                        </div>
                        <div class="metric-item">
                            <span>Bugs Found</span>
                            <div class="metric-val <?php echo ($bugsCount > 0) ? 'low' : 'high'; ?>">
                                <?php echo $bugsCount; ?>
                            </div>
                        </div>
                        <div class="metric-item">
                            <span>Security Alerts</span>
                            <div class="metric-val <?php echo ($securityCount > 0) ? 'low' : 'high'; ?>">
                                <?php echo $securityCount; ?>
                            </div>
                        </div>
                    </div>

                    <div class="report-suggestions">
                        <strong>AI Suggestions:</strong><br>
                        <?php echo htmlspecialchars($row['suggestions'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="no-reports">
                <p style="color: #cbd5e1; margin-bottom: 20px;">No analysis reports generated yet.</p>
                <a href="my_projects.php" class="btn">Select a project to analyze</a>
            </div>
            <?php
        }
        $stmt->close();
        ?>
    </div>
</div>

<footer>
    © 2026 CodeDiliver AI. All Rights Reserved.
</footer>

</body>
</html>