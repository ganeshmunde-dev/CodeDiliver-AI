<?php
/**
 * Dedicated AJAX endpoint for Gemini 2.5 Flash AI responses.
 * Returns JSON always — never redirects (AJAX-safe).
 */

// Set PHP execution time limit high enough for AI model thinking latency
set_time_limit(120);

// Force JSON response header always — must be before any output
header('Content-Type: application/json');

// Bootstrap session + auth manually (do NOT use auth.php which redirects)
include_once __DIR__ . '/../includes/functions.php';
include_once __DIR__ . '/../includes/connection.php';

secureSessionStart();

// Auth check — return JSON 401 instead of redirect
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Session expired. Please log in again.']);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$project_id = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
$user_msg = trim($_POST['message'] ?? '');

if ($project_id <= 0) {
    echo json_encode(['error' => 'No project selected.']);
    exit();
}

// Verify project belongs to current user
$stmt = $conn->prepare("SELECT id, project_name FROM project WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $project_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    $stmt->close();
    echo json_encode(['error' => 'Project not found or access denied.']);
    exit();
}
$project = $res->fetch_assoc();
$stmt->close();

// Handle attached file
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $file_name = $_FILES['attachment']['name'];
    $tmp_name  = $_FILES['attachment']['tmp_name'];
    $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $upload_dir = '../uploads/attachments/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $safe_file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file_name);
    $dest_path = $upload_dir . $safe_file_name;

    if (move_uploaded_file($tmp_name, $dest_path)) {
        $allowed_code_exts = ['php', 'html', 'css', 'js', 'sql', 'txt', 'json', 'py', 'ts'];
        if (in_array($ext, $allowed_code_exts)) {
            $code_content = file_get_contents($dest_path);
            $user_msg .= "\n\n--- Attached file: $file_name ---\n" . $code_content;
        } else {
            $user_msg .= "\n\n[User attached: $file_name (non-text file, not readable inline)]";
        }
    }
}

if (empty($user_msg)) {
    echo json_encode(['error' => 'Empty message.']);
    exit();
}

// Scan extracted project files for context
$project_path = '../uploads/extracted/project_' . $project_id . '/';
$project_files = [];
if (is_dir($project_path)) {
    try {
        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($project_path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iter as $file) {
            if ($file->isFile()) {
                $project_files[] = $file->getFilename();
            }
        }
    } catch (Throwable $e) {
        // Fallback gracefully without breaking chat
    }
}

// Build Gemini system + user prompt
$system_context  = "You are CodeDiliver AI — a specialized developer assistant that only helps with code-related topics.\n";
$system_context .= "Project: {$project['project_name']}\n";
if (!empty($project_files)) {
    $system_context .= "Project files: " . implode(', ', $project_files) . "\n";
}
$system_context .= "\nRules:\n";
$system_context .= "- Only answer coding, debugging, architecture, or security questions.\n";
$system_context .= "- Refuse off-topic (politics, entertainment, personal advice) politely.\n";
$system_context .= "- Use PHP, HTML, CSS, JS, SQL best practices.\n";
$system_context .= "- Always use prepared statements for SQL. Never interpolate variables directly.\n";
$system_context .= "- Format code answers in markdown code blocks with language identifier.\n";
$system_context .= "- Be concise and accurate.\n\n";
$system_context .= "Developer Query:\n" . $user_msg;

// Gemini 2.5 Flash preview API call
$model   = "gemini-2.5-flash";
$api_key = "YOUR_GEMINI_API_KEY";

$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => $system_context]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature"     => 0.7,
        "maxOutputTokens" => 8192
    ]
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT        => 90,          // 90-second max for thinking model
    CURLOPT_CONNECTTIMEOUT => 10,          // 10-second connection timeout
]);

$response = curl_exec($ch);
$curl_error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curl_error) {
    echo json_encode(['error' => 'Network error: ' . $curl_error]);
    exit();
}

$result = json_decode($response, true);

// Extract AI text from response
if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    $ai_text = $result['candidates'][0]['content']['parts'][0]['text'];
} elseif (isset($result['error']['message'])) {
    echo json_encode(['error' => 'Gemini API: ' . $result['error']['message']]);
    exit();
} else {
    echo json_encode(['error' => 'Unexpected API response. HTTP ' . $http_code . ': ' . $response]);
    exit();
}

// Persist chat to database
$ins = $conn->prepare("INSERT INTO chats (user_id, project_id, user_message, ai_response) VALUES (?, ?, ?, ?)");
$ins->bind_param("iiss", $user_id, $project_id, $user_msg, $ai_text);
$ins->execute();
$ins->close();

echo json_encode(['response' => $ai_text]);
exit();
