<?php
include_once __DIR__ . '/../includes/auth.php';
include_once __DIR__ . '/../includes/connection.php';
include_once __DIR__ . '/../includes/functions.php';

include_once __DIR__ . '/../analyzer/performance_checker.php';
include_once __DIR__ . '/../analyzer/quality_score.php';
include_once __DIR__ . '/../analyzer/bug_detector.php';
include_once __DIR__ . '/../analyzer/project_scanner.php';
include_once __DIR__ . '/../analyzer/security_checker.php';
include_once __DIR__ . '/../analyzer/ai_summary.php';

if (!isset($_GET['id'])) {
    die("Project ID is missing.");
}

$project_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Verify project ownership
$stmt = $conn->prepare("SELECT id, zip_file, project_name FROM project WHERE id = ? AND user_id = ?");
if (!$stmt) {
    die("Database query error: " . $conn->error);
}
$stmt->bind_param("ii", $project_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$res || $res->num_rows === 0) {
    $stmt->close();
    die("Project not found or access denied.");
}

$project = $res->fetch_assoc();
$stmt->close();

// Path to extracted files
$project_path = __DIR__ . "/../uploads/extracted/project_" . $project['id'] . "/";

if (!is_dir($project_path)) {
    // If files are missing but ZIP exists, re-extract as fallback
    $zip_path = __DIR__ . "/../uploads/" . $project['zip_file'];
    if (file_exists($zip_path) && class_exists('ZipArchive')) {
        @mkdir($project_path, 0755, true);
        $zip = new ZipArchive();
        if ($zip->open($zip_path) === TRUE) {
            $zip->extractTo($project_path);
            $zip->close();
        }
    }
}

// Perform static code analysis
$result         = scanProject($project_path);
$bugs           = detectBugs($project_path);
$security       = securityCheck($project_path);
$qualityScore   = calculateQualityScore($project_path);
$performance    = performanceCheck($project_path);

$bugsCount        = count($bugs);
$securityCount    = count($security);
$performanceCount = count($performance);

// Generate automated suggestions
$suggestionsArr = [];
if ($bugsCount > 0) {
    $suggestionsArr[] = "Fix the $bugsCount critical bug(s) identified in code files.";
}
if ($securityCount > 0) {
    $suggestionsArr[] = "Remediate $securityCount security risk(s) (e.g., escape user inputs, avoid eval).";
}
if ($performanceCount > 0) {
    $suggestionsArr[] = "Optimize $performanceCount performance bottleneck(s) (e.g., specify column names instead of SELECT *).";
}
if (empty($suggestionsArr)) {
    $suggestionsArr[] = "Your codebase demonstrates solid development standards and clean organization.";
}
$suggestions = implode(" ", $suggestionsArr);

// Insert analysis report securely using prepared statement
$reportStmt = $conn->prepare("INSERT INTO reports (project_id, security_score, code_quality_score, bugs_found, suggestions) VALUES (?, ?, ?, ?, ?)");
if ($reportStmt) {
    $reportStmt->bind_param("iiiis", $project['id'], $securityCount, $qualityScore, $bugsCount, $suggestions);
    $reportStmt->execute();
    $reportStmt->close();
}

// Redirect to the styled reports results view page
header("Location: ../analyzer/analysis_result.php?quality=" . $qualityScore . "&bugs=" . $bugsCount . "&security=" . $securityCount . "&performance=" . $performanceCount);
exit();
?>
