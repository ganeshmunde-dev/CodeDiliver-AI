<?php
include_once 'includes/auth.php';
include_once 'includes/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Project - CodeDiliver AI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .upload-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 20px;
        }
        .upload-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }
        .upload-card h2 {
            margin-bottom: 25px;
            text-align: center;
            background: linear-gradient(to right, #60a5fa, #22d3ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .file-drop-area {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            width: 100%;
            padding: 40px 20px;
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            transition: 0.3s;
            cursor: pointer;
            margin-bottom: 25px;
            background: rgba(255, 255, 255, 0.02);
        }
        .file-drop-area:hover {
            border-color: #60a5fa;
            background: rgba(96, 165, 250, 0.05);
        }
        .file-drop-icon {
            font-size: 40px;
            color: #60a5fa;
            margin-bottom: 10px;
        }
        .file-msg {
            font-size: 14px;
            color: #cbd5e1;
            text-align: center;
        }
        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 100%;
            opacity: 0;
            cursor: pointer;
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

<div class="upload-container">
    <div class="upload-card">
        <h2>Upload Project</h2>

        <form action="api/upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="project_name">Project Name</label>
                <input type="text" id="project_name" name="project_name" required placeholder="My Awesome Web App">
            </div>

            <div class="file-drop-area">
                <div class="file-drop-icon">📦</div>
                <span class="file-msg" id="file-msg-text">Drag and drop or click to select ZIP file</span>
                <input class="file-input" type="file" name="project_zip" accept=".zip" required id="project-zip-input">
            </div>

            <button type="submit" name="upload" class="auth-btn">
                Upload & Scan Project
            </button>
        </form>
    </div>
</div>

<footer>
    © 2026 CodeDiliver AI. All Rights Reserved.
</footer>

<script>
    const fileInput = document.getElementById('project-zip-input');
    const fileMsg = document.getElementById('file-msg-text');
    
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            fileMsg.textContent = "Selected: " + fileInput.files[0].name;
            fileMsg.style.color = "#60a5fa";
        } else {
            fileMsg.textContent = "Drag and drop or click to select ZIP file";
            fileMsg.style.color = "#cbd5e1";
        }
    });
</script>

</body>
</html>