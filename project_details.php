<?php
include_once 'includes/auth.php';
include_once 'includes/connection.php';
include_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    die("Project ID Missing");
}

$id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Use prepared statement checking project ID and user ID ownership
$stmt = $conn->prepare("SELECT * FROM project WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    die("Project not found or access denied.");
}

$project = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details - CodeDiliver AI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .details-container {
            max-width: 700px;
            margin: 60px auto;
            padding: 0 20px;
        }
        .details-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }
        .details-card h2 {
            font-size: 30px;
            font-weight: 800;
            margin-bottom: 25px;
            background: linear-gradient(to right, #60a5fa, #22d3ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .info-row:last-of-type {
            border-bottom: none;
            margin-bottom: 25px;
        }
        .info-label {
            font-weight: 500;
            color: #94a3b8;
        }
        .info-val {
            color: #ffffff;
            font-weight: 600;
        }
        .actions {
            display: flex;
            gap: 15px;
        }
        .actions .btn {
            flex: 1;
            text-align: center;
            margin: 0;
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

<div class="details-container">
    <div class="details-card">
        <h2>Project Details</h2>

        <div class="info-row">
            <span class="info-label">Project Name</span>
            <span class="info-val"><?php echo htmlspecialchars($project['project_name'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <div class="info-row">
            <span class="info-label">ZIP Filename</span>
            <span class="info-val"><?php echo htmlspecialchars($project['zip_file'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <div class="info-row">
            <span class="info-label">Upload Date</span>
            <span class="info-val"><?php echo htmlspecialchars($project['upload_date'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <div class="actions">
            <a href="api/analyze.php?id=<?php echo $project['id']; ?>" class="btn">
                Run AI Analysis
            </a>
            <a href="chat.php?project_id=<?php echo $project['id']; ?>" class="btn btn-secondary">
                Chat with AI
            </a>
            <a href="my_projects.php" class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>
</div>

<footer>
    © 2026 CodeDiliver AI. All Rights Reserved.
</footer>

</body>
</html>