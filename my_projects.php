<?php
include_once 'includes/auth.php';
include_once 'includes/connection.php';
include_once 'includes/functions.php';

$user_id = $_SESSION['user_id'];

// Use prepared statements to query user projects securely
$stmt = $conn->prepare("SELECT * FROM project WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Projects - CodeDiliver AI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .projects-container {
            max-width: 1000px;
            margin: 60px auto;
            padding: 0 20px;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        .header-section h2 {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(to right, #60a5fa, #22d3ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .project-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .project-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 25px 30px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
        }
        .project-card:hover {
            transform: translateX(5px);
            border-color: rgba(96, 165, 250, 0.4);
            background: rgba(255, 255, 255, 0.08);
        }
        .project-info h3 {
            font-size: 20px;
            color: #ffffff;
            margin-bottom: 5px;
        }
        .project-info p {
            font-size: 13px;
            color: #94a3b8;
            margin: 3px 0;
        }
        .project-actions .btn {
            padding: 10px 25px;
            font-size: 14px;
            margin: 0;
        }
        .no-projects {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 20px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
        }
        .no-projects p {
            color: #cbd5e1;
            margin-bottom: 20px;
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

<div class="projects-container">
    <div class="header-section">
        <h2>My Projects</h2>
        <a href="upload_project.php" class="btn">New Project</a>
    </div>

    <div class="project-list">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="project-card">
                    <div class="project-info">
                        <h3><?php echo htmlspecialchars($row['project_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><strong>ZIP File:</strong> <?php echo htmlspecialchars($row['zip_file'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Uploaded:</strong> <?php echo htmlspecialchars($row['upload_date'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="project-actions">
                        <a href="project_details.php?id=<?php echo $row['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="no-projects">
                <p>No projects uploaded yet.</p>
                <a href="upload_project.php" class="btn">Upload Your First Project</a>
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