<?php
include_once __DIR__ . '/../includes/auth.php';
include_once __DIR__ . '/../includes/connection.php';
include_once __DIR__ . '/../includes/functions.php';

// Ensure the request method is POST and form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $project_name = cleanInput($_POST['project_name'] ?? '');

    if (empty($project_name)) {
        die("Please provide a project name.");
    }

    if (!isset($_FILES['project_zip']) || $_FILES['project_zip']['error'] !== UPLOAD_ERR_OK) {
        die("File upload error occurred. Please select a valid ZIP archive.");
    }

    $file_name = $_FILES['project_zip']['name'];
    $tmp_name  = $_FILES['project_zip']['tmp_name'];
    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if ($extension !== "zip") {
        die("Only ZIP archives (.zip) are allowed.");
    }

    // Ensure uploads directory exists
    $uploads_dir = __DIR__ . '/../uploads';
    if (!is_dir($uploads_dir)) {
        @mkdir($uploads_dir, 0755, true);
    }

    // Sanitize file name to avoid path traversal / special characters
    $safe_file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $file_name);
    $dest_path      = $uploads_dir . "/" . $safe_file_name;

    if (move_uploaded_file($tmp_name, $dest_path)) {
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("INSERT INTO project (user_id, project_name, zip_file) VALUES (?, ?, ?)");
        if (!$stmt) {
            die("Database statement error: " . $conn->error);
        }
        $stmt->bind_param("iss", $user_id, $project_name, $safe_file_name);
        
        if ($stmt->execute()) {
            $project_id = $stmt->insert_id;
            $stmt->close();

            // Extract the ZIP file in a dedicated project directory
            $extract_dir = __DIR__ . "/../uploads/extracted/project_" . $project_id;
            if (!is_dir($extract_dir)) {
                @mkdir($extract_dir, 0755, true);
            }

            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip->open($dest_path) === TRUE) {
                    $zip->extractTo($extract_dir);
                    $zip->close();
                }
            }

            // Redirect to project details page
            header("Location: ../project_details.php?id=" . $project_id);
            exit();
        } else {
            $stmt->close();
            die("Failed to record project in database.");
        }
    } else {
        die("Upload directory permission error. Please verify writable permissions on uploads folder.");
    }
}
?>
