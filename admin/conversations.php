<?php
require '../core/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$theme = 'light';
$check_theme = $conn->query("SELECT theme FROM admin_settings WHERE user_id = " . $_SESSION['user_id']);
if ($check_theme && $check_theme->num_rows > 0) {
    $theme = $check_theme->fetch_assoc()['theme'];
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversations - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/conversations.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Neydream Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="conversations.php" class="nav-item active">
                    <span class="icon">💬</span>
                    <span>Conversations</span>
                    <span class="badge" id="totalUnread">0</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <button class="settings-btn" onclick="openSettings()">⚙️</button>
                <div class="user-info">
                    <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="conversations-container">
                <div class="conversations-sidebar">
                    <div class="conversations-header">
                        <h3>Conversations</h3>
                        <input type="text" placeholder="Search..." class="search-input">
                    </div>
                    <div class="conversation-list" id="conversationList">
                        <div class="loading">Loading conversations...</div>
                    </div>
                </div>

                <div class="chat-panel">
                    <div class="chat-header" id="chatHeader">
                        <div class="chat-info">
                            <h4>Select a conversation</h4>
                        </div>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <div class="empty-state">
                            <p>👈 Select a conversation to start chatting</p>
                        </div>
                    </div>
                    <div class="chat-input-container" id="chatInputContainer" style="display: none;">
                        <input type="file" id="imageInput" accept="image/*" style="display: none;">
                        <button class="attach-btn" onclick="document.getElementById('imageInput').click()">📎</button>
                        <input type="text" id="messageInput" placeholder="Type a message..." onkeypress="handleKeyPress(event)">
                        <button class="send-btn" onclick="sendAdminMessage()">Send</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal" id="settingsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Settings</h3>
                <button class="close-btn" onclick="closeSettings()">×</button>
            </div>
            <div class="modal-body">
                <div class="setting-item">
                    <label>Theme</label>
                    <div class="theme-toggle">
                        <button class="theme-btn" data-theme="light" onclick="setTheme('light')">☀️ Light</button>
                        <button class="theme-btn" data-theme="dark" onclick="setTheme('dark')">🌙 Dark</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/conversations.js"></script>
    <script src="js/theme.js"></script>
</body>
</html>
