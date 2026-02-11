<?php
require '../core/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php include 'includes/admin_styles.php'; ?>
    <link rel="stylesheet" href="css/conversations.css">
    <style>
        .chat-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .btn-delete-chat {
            background: var(--primary-dark, #ff5c8a);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        .btn-delete-chat:hover { opacity: 0.8; }
        
        /* Modal Styles */
        .admin-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .admin-modal-content {
            background: var(--card-bg, #fff);
            padding: 24px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .admin-modal-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            justify-content: center;
        }
        .btn-modal {
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            font-weight: 500;
        }
        .btn-modal.cancel { background: #f1f2f6; color: #2f3542; }
        .btn-modal.confirm { background: #ff4757; color: white; }
    </style>
</head>
<body>
    <?php include 'includes/loading.php'; ?>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-wrapper">
            <header class="top-header">
                <div class="breadcrumbs">
                    <a href="dashboard.php" class="sep">Dashboard</a>
                    <span class="sep">/</span>
                    <span class="current">Chat</span>
                </div>
                <div class="header-actions">
                </div>
            </header>

            <div class="page-header-context">
                <div class="context-avatar">💬</div>
                <div class="context-info">
                    <div class="title">Bantuan Langsung</div>
                    <div class="subtitle">Pusat kendali komunikasi dengan pelanggan.</div>
                </div>
            </div>

            <div class="conversations-container">
                <div class="conversations-sidebar">
                    <div class="conversations-header">
                        <h3>Antrian Pesan</h3>
                        <div class="search-wrapper">
                            <input type="text" placeholder="Cari nama pelanggan..." class="search-input">
                        </div>
                    </div>
                    <div class="conversation-list" id="conversationList">
                        <div class="loading">Memuat percakapan...</div>
                    </div>
                </div>

                <div class="chat-panel">
                    <div class="chat-header" id="chatHeader">
                        <button class="back-to-queue-btn" id="backToQueueBtn" style="display: none;">
                            <span style="font-size: 18px;">←</span>
                            <span class="back-text">Kembali</span>
                        </button>
                        <div class="chat-info">
                            <h4 id="activeChatName">Pilih percakapan</h4>
                            <div class="status-text" id="activeChatStatus"></div>
                        </div>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <div class="empty-state">
                            <p>👈 Pilih salah satu pelanggan di samping untuk mulai membalas pesan.</p>
                        </div>
                    </div>
                    <div class="chat-input-container" id="chatInputContainer" style="display: none;">
                        <input type="file" id="imageInput" accept="image/*,video/*" style="display: none;" onchange="handleAdminFileSelect(this)">
                        <button class="attach-btn" id="adminAttachBtn" onclick="document.getElementById('imageInput').click()">📎</button>
                        <input type="text" id="messageInput" placeholder="Tulis balasan Anda..." onkeypress="handleKeyPress(event)">
                        <button class="send-btn" id="adminSendBtn" onclick="sendAdminMessage()">Kirim Pesan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="admin-modal">
        <div class="admin-modal-content">
            <h3 style="margin-bottom: 10px;">Hapus Riwayat Chat?</h3>
            <p style="color: #666; font-size: 14px;">Apakah Anda yakin ingin menghapus seluruh riwayat chat dengan pelanggan ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="admin-modal-actions">
                <button class="btn-modal cancel" onclick="closeAdminDeleteModal()">Tidak</button>
                <button class="btn-modal confirm" onclick="executeAdminDeleteChat()">Hapus</button>
            </div>
        </div>
    </div>

    <script src="js/conversations.js"></script>
</body>
</html>
