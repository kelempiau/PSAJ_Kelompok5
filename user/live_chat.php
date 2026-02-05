<?php
require '../core/config.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$username = $_SESSION['username'];
$page_title = "Chat Assistant";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Neydream Studio</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Base CSS (Navbar stuff) -->
    <link rel="stylesheet" href="../assets/css/reservasi.css?v=2">
    <!-- Chat Page Specific CSS -->
    <link rel="stylesheet" href="../assets/css/chat_page.css">
</head>
<body>
    
    <!-- Navbar (Simplified Version) -->
    <nav class="navbar" style="background: white; padding: 15px 5%; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100;">
        <div class="logo">
            <a href="../index.php" style="text-decoration: none; font-weight: 700; font-size: 1.5rem; color: #333;">
                Neydream<span style="color: #ea3671;">.</span>
            </a>
        </div>
        <div class="nav-links">
            <a href="../index.php" style="text-decoration: none; color: #666; margin-right: 20px;">Beranda</a>
            <a href="reservasi.php" style="text-decoration: none; color: #666; margin-right: 20px;">Reservasi</a>
            <a href="../auth/logout.php" style="text-decoration: none; color: #ea3671; font-weight: 600;">Logout</a>
        </div>
    </nav>

    <!-- Chat Container -->
    <div class="chat-page-container">
        
        <!-- Header -->
        <div class="chat-page-header">
            <div class="chat-header-user">
                <div class="chat-avatar-large">
                    🤖
                </div>
                <div class="chat-info">
                    <h2>Neydream Assistant</h2>
                    <p><span class="status-dot"></span> Online • Siap Membantu</p>
                </div>
            </div>
            <div class="chat-actions">
                <button onclick="clearChat()" style="background: #fee2e2; color: #ef4444; border: none; padding: 8px 15px; border-radius: 20px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                    Hapus Chat 🗑️
                </button>
            </div>
        </div>

        <!-- Messages Area (ID must match smart_chatbot.js) -->
        <div class="chat-messages-area" id="chatBox">
            <!-- Messages will be loaded here by JS -->
            <div class="message admin">
                Halo Kak <b><?= htmlspecialchars($username) ?></b>! ✨<br>
                Selamat datang di layanan chat Neydream. Ada yang bisa saya bantu hari ini?
            </div>
        </div>

        <!-- Input Area -->
        <div class="chat-input-wrapper">
            <input type="text" id="userInput" class="chat-input-field" placeholder="Ketik pesan Anda disini..." onkeypress="handleKeyPress(event)">
            <button class="chat-send-btn" onclick="sendMessage()">
                ➤
            </button>
        </div>

    </div>

    <!-- Hidden Modals if needed by JS (Delete Confirm) -->
    <div id="deleteConfirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; width: 90%; max-width: 350px;">
            <h3>Hapus Riwayat?</h3>
            <p style="color: #666; margin: 15px 0;">Chat yang dihapus tidak bisa dikembalikan.</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button onclick="closeDeleteModal()" style="padding: 10px 20px; border: none; background: #eee; border-radius: 8px; cursor: pointer;">Batal</button>
                <button onclick="executeClearChat()" style="padding: 10px 20px; border: none; background: #ef4444; color: white; border-radius: 8px; cursor: pointer;">Hapus</button>
            </div>
        </div>
    </div>

    <!-- Script Reuse -->
    <!-- We link correct path to API calls inside js, assuming js is generic relative to api/ or absolute -->
    <!-- NOTE: smart_chatbot.js uses '../api/...' which works from 'user/chat.php' as well -->
    <script src="../assets/js/smart_chatbot.js"></script>

</body>
</html>
