<?php
include_once 'includes/auth.php';
include_once 'includes/connection.php';
include_once 'includes/functions.php';

$user_id = $_SESSION['user_id'];
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

// Fetch all projects for sidebar
$projects_list = [];
$p_stmt = $conn->prepare("SELECT id, project_name FROM project WHERE user_id = ? ORDER BY id DESC");
$p_stmt->bind_param("i", $user_id);
$p_stmt->execute();
$p_res = $p_stmt->get_result();
while ($row = $p_res->fetch_assoc()) {
    $projects_list[] = $row;
}
$p_stmt->close();

// Auto-select first project
if ($project_id === 0 && !empty($projects_list)) {
    $project_id = $projects_list[0]['id'];
    header("Location: chat.php?project_id=" . $project_id);
    exit();
}

// Verify project ownership
$project = null;
if ($project_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM project WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $project_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();
    }
    $stmt->close();
}

// Fetch previous chat history from DB
$chat_history = [];
if ($project_id > 0) {
    $hist_stmt = $conn->prepare("SELECT * FROM chats WHERE user_id = ? AND project_id = ? ORDER BY id ASC");
    $hist_stmt->bind_param("ii", $user_id, $project_id);
    $hist_stmt->execute();
    $hist_res = $hist_stmt->get_result();
    while ($row = $hist_res->fetch_assoc()) {
        $chat_history[] = $row;
    }
    $hist_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Developer Chat - CodeDiliver AI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- highlight.js for syntax highlighting of code blocks -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <!-- marked.js for markdown to HTML rendering -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        .chat-container {
            max-width: 1150px;
            margin: 30px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 20px;
            height: calc(100vh - 175px);
            min-height: 500px;
        }
        .chat-sidebar {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .chat-sidebar h3 {
            font-size: 15px;
            font-weight: 600;
            color: #60a5fa;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding-bottom: 10px;
        }
        .project-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            border-radius: 12px;
            text-decoration: none;
            color: #cbd5e1;
            margin-bottom: 6px;
            font-size: 13px;
            transition: 0.25s;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.04);
        }
        .project-item:hover,
        .project-item.active {
            background: rgba(96,165,250,0.15);
            color: #fff;
            border-color: rgba(96,165,250,0.35);
        }
        .project-item .icon { font-size: 16px; flex-shrink: 0; }

        /* Main chat panel */
        .chat-main {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .chat-header {
            padding: 18px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .chat-header-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            background: #22d3ee;
            box-shadow: 0 0 8px #22d3ee;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        .chat-header-info h2 { font-size: 17px; font-weight: 700; color: #fff; }
        .chat-header-info p  { font-size: 12px; color: #64748b; }
        .model-badge {
            margin-left: auto;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(96,165,250,0.1);
            border: 1px solid rgba(96,165,250,0.25);
            color: #60a5fa;
            font-size: 11px;
            font-weight: 500;
        }

        /* Messages */
        .chat-messages {
            flex: 1;
            padding: 20px 25px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .msg-row {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .msg-row.user { flex-direction: row-reverse; }
        .avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .avatar.ai  { background: linear-gradient(135deg, #2563eb, #06b6d4); }
        .avatar.user { background: linear-gradient(135deg, #7c3aed, #db2777); }

        .chat-bubble {
            max-width: 78%;
            padding: 13px 18px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.65;
            word-break: break-word;
        }
        .chat-bubble.user {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        .chat-bubble.ai {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.08);
            color: #cbd5e1;
            border-bottom-left-radius: 4px;
        }

        /* Markdown rendered content inside AI bubble */
        .chat-bubble.ai h1, .chat-bubble.ai h2, .chat-bubble.ai h3 { color: #60a5fa; margin: 10px 0 5px; }
        .chat-bubble.ai p  { margin: 6px 0; }
        .chat-bubble.ai ul, .chat-bubble.ai ol { padding-left: 18px; margin: 6px 0; }
        .chat-bubble.ai li { margin: 3px 0; }
        .chat-bubble.ai code {
            background: rgba(0,0,0,0.35);
            border-radius: 4px;
            padding: 2px 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #22d3ee;
        }
        .chat-bubble.ai pre {
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 10px;
            padding: 14px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .chat-bubble.ai pre code {
            background: none;
            padding: 0;
            color: inherit;
            font-size: 13px;
        }
        .chat-bubble.ai strong { color: #e2e8f0; }
        .chat-bubble.ai blockquote {
            border-left: 3px solid #60a5fa;
            padding-left: 12px;
            color: #94a3b8;
            margin: 8px 0;
        }

        /* Typing indicator */
        .typing-indicator {
            display: flex;
            gap: 5px;
            align-items: center;
            padding: 14px 18px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            border-bottom-left-radius: 4px;
            width: fit-content;
        }
        .typing-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #60a5fa;
            animation: bounce 1.3s infinite;
        }
        .typing-dot:nth-child(2) { animation-delay: 0.15s; }
        .typing-dot:nth-child(3) { animation-delay: 0.30s; }
        @keyframes bounce {
            0%,60%,100% { transform: translateY(0); opacity: 0.5; }
            30% { transform: translateY(-7px); opacity: 1; }
        }

        /* Input area */
        .chat-input-area {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
            background: rgba(0,0,0,0.2);
        }
        .chat-form {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .attach-label {
            padding: 13px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: 0.25s;
            font-size: 17px;
            color: #94a3b8;
            flex-shrink: 0;
        }
        .attach-label:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(96,165,250,0.4);
            color: #60a5fa;
        }
        .chat-input {
            flex: 1;
            padding: 13px 18px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            color: #fff;
            outline: none;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            transition: 0.25s;
        }
        .chat-input::placeholder { color: #4b5563; }
        .chat-input:focus {
            border-color: #60a5fa;
            background: rgba(255,255,255,0.08);
        }
        .send-btn {
            padding: 13px 28px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(45deg, #2563eb, #06b6d4);
            color: #fff;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            cursor: pointer;
            transition: 0.25s;
            flex-shrink: 0;
        }
        .send-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37,99,235,0.4);
        }
        .send-btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }
        .attach-hint {
            margin-top: 7px;
            font-size: 12px;
            color: #60a5fa;
            display: none;
        }
        .no-project-msg {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
            color: #cbd5e1;
        }
        .no-project-msg h3 { font-size: 20px; margin-bottom: 10px; }
        .no-project-msg p { font-size: 14px; color: #94a3b8; }
        @media(max-width:768px) {
            .chat-container { grid-template-columns: 1fr; height: auto; }
            .chat-sidebar { max-height: 200px; }
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
        <li><a href="logout.php" style="color:#f43f5e;">Logout</a></li>
    </ul>
</nav>

<div class="chat-container">

    <!-- Sidebar: project list -->
    <div class="chat-sidebar">
        <h3>My Projects</h3>
        <?php foreach ($projects_list as $proj): ?>
            <a href="chat.php?project_id=<?php echo $proj['id']; ?>"
               class="project-item <?php echo ($project_id === $proj['id']) ? 'active' : ''; ?>">
                <span class="icon">📁</span>
                <?php echo htmlspecialchars($proj['project_name'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
        <?php endforeach; ?>
        <?php if (empty($projects_list)): ?>
            <p style="font-size:13px;color:#64748b;text-align:center;margin-top:20px;">
                No projects yet. Upload a ZIP to get started.
            </p>
        <?php endif; ?>
    </div>

    <!-- Main chat panel -->
    <div class="chat-main">

        <?php if ($project): ?>

        <!-- Header -->
        <div class="chat-header">
            <div class="chat-header-dot"></div>
            <div class="chat-header-info">
                <h2>AI Assistant — <?php echo htmlspecialchars($project['project_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p>Gemini 2.5 Flash · Code analysis, debugging, refactoring</p>
            </div>
            <span class="model-badge">Gemini 2.5 Flash</span>
        </div>

        <!-- Messages -->
        <div class="chat-messages" id="chat-box">

            <!-- Welcome bubble -->
            <div class="msg-row">
                <div class="avatar ai">🤖</div>
                <div class="chat-bubble ai">
                    Hello! I'm <strong>CodeDiliver AI</strong> powered by <strong>Gemini 2.5 Flash</strong>.<br><br>
                    Ask me anything about your code — debugging, security fixes, refactoring, or write new code from scratch.
                    You can also attach a file (PHP, JS, HTML, CSS, SQL) using the paperclip icon.
                </div>
            </div>

            <!-- Chat history from DB -->
            <?php foreach ($chat_history as $chat): ?>
                <div class="msg-row user">
                    <div class="avatar user">👤</div>
                    <div class="chat-bubble user">
                        <?php echo nl2br(htmlspecialchars($chat['user_message'], ENT_QUOTES, 'UTF-8')); ?>
                    </div>
                </div>
                <div class="msg-row">
                    <div class="avatar ai">🤖</div>
                    <div class="chat-bubble ai rendered-md">
                        <?php echo htmlspecialchars($chat['ai_response'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <!-- Input -->
        <div class="chat-input-area">
            <form id="chat-form" class="chat-form" enctype="multipart/form-data">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                <label class="attach-label" for="file-attach" title="Attach a code file">📎
                    <input type="file" id="file-attach" name="attachment" style="display:none;"
                           accept=".php,.js,.html,.css,.sql,.txt,.json,.py,.ts">
                </label>
                <input type="text" id="chat-input" name="message" class="chat-input"
                       placeholder="Ask about your code, paste an error, or request new code..." autocomplete="off">
                <button type="submit" id="send-btn" class="send-btn">Send</button>
            </form>
            <div class="attach-hint" id="attach-hint">📎 <span id="attach-name"></span> &nbsp;
                <span style="cursor:pointer;color:#f43f5e;" onclick="clearAttach()">✕</span>
            </div>
        </div>

        <?php else: ?>
        <div class="no-project-msg">
            <h3>Select a Project to Chat</h3>
            <p>Pick a project from the sidebar or upload a new one to start the AI session.</p>
            <a href="upload_project.php" class="btn" style="margin-top:20px;">Upload ZIP</a>
        </div>
        <?php endif; ?>

    </div>
</div>

<footer>© 2026 CodeDiliver AI. All Rights Reserved.</footer>

<script>
// =============================================
// Markdown renderer with highlight.js
// =============================================
marked.setOptions({
    highlight: function(code, lang) {
        if (lang && hljs.getLanguage(lang)) {
            return hljs.highlight(code, { language: lang }).value;
        }
        return hljs.highlightAuto(code).value;
    },
    breaks: true,
    gfm: true
});

function renderMarkdown(el) {
    const raw = el.textContent || el.innerText;
    el.innerHTML = marked.parse(raw);
    // Highlight any code blocks that need it
    el.querySelectorAll('pre code').forEach(block => {
        hljs.highlightElement(block);
    });
}

// Render all stored chat history on load
document.querySelectorAll('.rendered-md').forEach(renderMarkdown);

// =============================================
// Scroll chat to bottom
// =============================================
const chatBox = document.getElementById('chat-box');
function scrollBottom() {
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
}
scrollBottom();

// =============================================
// Attachment display
// =============================================
const fileAttach = document.getElementById('file-attach');
const attachHint = document.getElementById('attach-hint');
const attachName = document.getElementById('attach-name');

function clearAttach() {
    if (fileAttach) fileAttach.value = '';
    attachHint.style.display = 'none';
    attachName.textContent = '';
}

if (fileAttach) {
    fileAttach.addEventListener('change', () => {
        if (fileAttach.files.length > 0) {
            attachName.textContent = fileAttach.files[0].name;
            attachHint.style.display = 'block';
        } else {
            clearAttach();
        }
    });
}

// =============================================
// AJAX chat submit — no page reload
// =============================================
const chatForm = document.getElementById('chat-form');
const chatInput = document.getElementById('chat-input');
const sendBtn = document.getElementById('send-btn');

function appendMessage(role, text) {
    const row = document.createElement('div');
    row.className = 'msg-row' + (role === 'user' ? ' user' : '');

    const avatar = document.createElement('div');
    avatar.className = 'avatar ' + role;
    avatar.textContent = role === 'user' ? '👤' : '🤖';

    const bubble = document.createElement('div');
    bubble.className = 'chat-bubble ' + role;

    if (role === 'user') {
        bubble.textContent = text;
    } else {
        // Render markdown for AI responses
        bubble.innerHTML = marked.parse(text);
        bubble.querySelectorAll('pre code').forEach(block => {
            hljs.highlightElement(block);
        });
    }

    row.appendChild(avatar);
    row.appendChild(bubble);
    chatBox.appendChild(row);
    scrollBottom();
    return row;
}

function showTyping() {
    const row = document.createElement('div');
    row.className = 'msg-row';
    row.id = 'typing-row';

    const avatar = document.createElement('div');
    avatar.className = 'avatar ai';
    avatar.textContent = '🤖';

    const indicator = document.createElement('div');
    indicator.className = 'typing-indicator';
    indicator.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';

    row.appendChild(avatar);
    row.appendChild(indicator);
    chatBox.appendChild(row);
    scrollBottom();
}

function removeTyping() {
    const row = document.getElementById('typing-row');
    if (row) row.remove();
}

if (chatForm) {
    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const msgText = chatInput.value.trim();
        if (!msgText) return;

        // Show user message immediately
        appendMessage('user', msgText);
        chatInput.value = '';

        // Lock input while waiting
        sendBtn.disabled = true;
        sendBtn.textContent = '...';
        chatInput.disabled = true;

        // Show typing dots
        showTyping();

        const formData = new FormData(chatForm);
        // Re-add message after clearing input
        formData.set('message', msgText);

        try {
            const res = await fetch('api/chat_ai.php', {
                method: 'POST',
                body: formData
            });

            removeTyping();

            if (!res.ok) {
                appendMessage('ai', '⚠️ Server error: HTTP ' + res.status);
            } else {
                const data = await res.json();
                if (data.response) {
                    appendMessage('ai', data.response);
                } else if (data.error) {
                    appendMessage('ai', '⚠️ ' + data.error);
                } else {
                    appendMessage('ai', '⚠️ Unexpected response format.');
                }
            }
        } catch (err) {
            removeTyping();
            appendMessage('ai', '⚠️ Network error: ' + err.message);
        } finally {
            sendBtn.disabled = false;
            sendBtn.textContent = 'Send';
            chatInput.disabled = false;
            chatInput.focus();
            clearAttach();
        }
    });
}

// Allow Ctrl+Enter or Shift+Enter to send
chatInput && chatInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        chatForm.dispatchEvent(new Event('submit'));
    }
});
</script>

</body>
</html>
