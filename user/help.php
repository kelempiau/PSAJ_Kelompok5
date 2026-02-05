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
    
    <!-- INTERNAL CSS (PINK THEME & FIXED LAYOUT) -->
    <style>
        /* ANTIGRAVITY AD PROTECTION */
        #sb98124, #sb98124_image, #sb98124_close, .tutup2,
        div[id^="sb"][style*="display: block"], 
        div[id^="sb"][style*="position: fixed"],
        a[href*="infinityfree"] {
            display: none !important;
            opacity: 0 !important;
            pointer-events: none !important;
            visibility: hidden !important;
            z-index: -99999 !important;
        }
    </style>
    <script>
        (function(){
            setInterval(function(){
                var ads = document.querySelectorAll('#sb98124, #sb98124_image, .tutup2, div[id^="sb"][style*="fixed"]');
                ads.forEach(function(el){ el.remove(); });
            }, 500);
        })();
    </script>
    <style>
        :root {
            /* PINK THEME PALETTE */
            --primary-pink: #ea3671;  /* Neydream Pink */
            --dark-pink: #be123c;
            --light-pink: #fce7f3;
            --user-bubble: #ea3671;   /* User Bubble Pink */
            
            --light-gray: #f3f4f6;    /* Admin Bubble */
            --white: #ffffff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
        }
        
        body {
            background: #fff1f2 !important; /* Soft Pink Background */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0 !important;
        }

        /* Navbar Reset */
        .navbar {
            width: 100%;
            background: white !important;
            padding: 15px 5%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-sizing: border-box;
        }
        
        .navbar .logo a { text-decoration: none; font-weight: 700; font-size: 1.5rem; color: #333; }
        .navbar .nav-links a { text-decoration: none; color: #666; margin-left: 20px; font-weight: 500; }
        .navbar .nav-links a:hover { color: var(--primary-pink); }

        /* MAIN CARD CONTAINER */
        .chat-page-container {
            width: 95%;
            max-width: 900px;
            height: 80vh;
            background: var(--white);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(234, 54, 113, 0.1); /* Pinkish shadow */
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(0,0,0,0.05);
            margin-top: 20px;
        }

        /* HEADER */
        .chat-page-header {
            padding: 20px 30px;
            background: var(--white);
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
            box-sizing: border-box;
        }

        .chat-header-user {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .chat-avatar-large {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--light-pink);
            color: var(--dark-pink);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
        }

        .chat-info h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark) !important;
            margin: 0;
            text-align: left;
        }

        .chat-info p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 2px 0 0 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ACTIONS */
        .chat-actions button {
            background: #fff0eb;
            color: #ef4444; /* Red for delete */
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .chat-actions button:hover { background: #fee2e2; }

        /* CHAT AREA */
        .chat-messages-area {
            flex: 1;
            padding: 30px 40px;
            background-color: var(--white);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* BUBBLES */
        .message {
            max-width: 65%;
            padding: 16px 24px;
            font-size: 0.95rem;
            line-height: 1.6;
            position: relative;
        }

        .message.admin {
            background: var(--light-gray);
            color: var(--text-dark);
            align-self: flex-start;
            border-radius: 20px 20px 20px 4px;
        }

        .message.user {
            background: var(--user-bubble); /* PINK */
            color: white;
            align-self: flex-end;
            border-radius: 20px 20px 4px 20px;
            box-shadow: 0 4px 12px rgba(234, 54, 113, 0.2);
        }

        /* INPUT AREA */
        .chat-input-wrapper {
            padding: 20px 30px;
            background: var(--white);
            border-top: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 12px;
            box-sizing: border-box;
        }

        .chat-input-field {
            flex: 1;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 14px 24px;
            border-radius: 30px;
            font-size: 0.95rem;
            color: var(--text-dark);
            outline: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .chat-input-field:focus {
            background: var(--white);
            border-color: var(--light-pink);
            box-shadow: 0 2px 10px rgba(234, 54, 113, 0.1);
        }

        .chat-send-btn {
            width: 48px;
            height: 48px;
            background: var(--primary-pink); /* PINK BUTTON */
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            box-shadow: 0 4px 10px rgba(234, 54, 113, 0.3);
        }

        .chat-send-btn:hover {
            background: var(--dark-pink);
            transform: translateY(-2px);
        }

        /* ATTACHMENT BUTTON */
        .chat-attach-btn {
            width: 48px;
            height: 48px;
            background: #f3f4f6;
            color: var(--text-muted);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .chat-attach-btn:hover {
            background: #e5e7eb;
            color: var(--text-dark);
        }
        .chat-attach-btn.has-file {
            background: var(--light-pink);
            color: var(--primary-pink);
        }

        /* IMAGE IN BUBBLE */
        .message img {
            max-width: 100%;
            border-radius: 12px;
            margin-bottom: 8px;
            cursor: pointer;
            display: block;
        }
        .message.user img {
            border: 2px solid rgba(255,255,255,0.2);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .chat-page-container {
                height: 100vh;
                max-width: 100%;
                border-radius: 0;
                box-shadow: none;
                margin-top: 0;
                border: none;
            }
            .chat-messages-area { padding: 20px; }
            .navbar { display: none; }
        }
    </style>
</head>
<body>
    
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <a href="../index.php">Neydream<span style="color: #ea3671;">.</span></a>
        </div>
        <div class="nav-links">
            <a href="../index.php">Beranda</a>
            <a href="reservasi.php">Reservasi</a>
            <a href="javascript:void(0);" onclick="openLogoutModal()" style="color: #ea3671;">Logout</a>
        </div>
    </nav>

    <!-- Chat Container -->
    <div class="chat-page-container">
        
        <!-- Header -->
        <div class="chat-page-header">
            <div class="chat-header-user">
                <div class="chat-avatar-large">
                    ND
                </div>
                <div class="chat-info">
                    <h2>Neydream Assistant</h2>
                    <p><span style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; display: inline-block;"></span> Online</p>
                </div>
            </div>
            <div class="chat-actions">
                <button onclick="clearChat()">
                    Hapus Chat 🗑️
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages-area" id="chatBox">
            <div class="message admin">
                Halo Kak <b><?= htmlspecialchars($username) ?></b>! ✨<br>
                Selamat datang di layanan chat Neydream. Ada yang bisa saya bantu hari ini?
            </div>
        </div>

        <!-- Input Area -->
        <div class="chat-input-wrapper">
            <input type="file" id="imageInput" accept="image/*,video/*" style="display: none;" onchange="handleFileSelect(this)">
            <button class="chat-attach-btn" id="attachBtn" onclick="document.getElementById('imageInput').click()">
                📎
            </button>
            <input type="text" id="userInput" class="chat-input-field" placeholder="Ketik pesan Anda disini..." onkeypress="handleKeyPress(event)">
            <button class="chat-send-btn" onclick="sendMessage()">
                ➤
            </button>
        </div>

    </div>

    <!-- Hidden Modals -->
    <!-- Delete Chat Confirmation Modal -->
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

    <!-- Logout Confirmation Modal -->
    <div id="logoutConfirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; width: 90%; max-width: 350px;">
            <h3 style="color: #1f2937; margin-bottom: 10px;">Keluar dari Akun?</h3>
            <p style="color: #666; margin: 15px 0; font-size: 14px;">Apakah Anda yakin ingin keluar?</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button onclick="closeLogoutModal()" style="padding: 10px 20px; border: none; background: #eee; border-radius: 8px; cursor: pointer; font-weight: 500;">Tidak</button>
                <button onclick="executeLogout()" style="padding: 10px 20px; border: none; background: #ea3671; color: white; border-radius: 8px; cursor: pointer; font-weight: 500;">Logout</button>
            </div>
        </div>
    </div>

    <!-- INTERNAL JAVASCRIPT (FIXED SEND BUTTON) -->
    <script>
        // ================================================
        // ULTRA SMART AI CHATBOT v3.0 (INLINED)
        // ================================================

        let chatHistory = [];
        let userContext = { hasAskedPrice: false, wantsToBook: false };
        let currentConversationId = null;
        let isEscalated = false;
        let unknownCount = 0;
        let botResponseCount = 0; // TRACK TOTAL BOT RESPONSES

        // Knowledge Base
        const knowledgeBase = {
            'cara_booking': {
                triggers: ['cara pesan', 'cara booking', 'gimana pesan', 'gimana booking', 'mau pesan', 'mau booking'],
                response: `📝 <b>Cara Booking:</b>\n1. Isi form di atas\n2. Pilih jadwal\n3. Bayar DP 20rb ke BCA: 123-456-7890\n4. Upload bukti.`
            },
            'harga': {
                triggers: ['harga', 'biaya', 'price', 'berapa'],
                response: `💰 <b>Harga:</b>\n💅 Gel: 50rb\n✨ French: 75rb\n💎 Ext: 150rb\n🎨 Custom: 200rb.`
            }
        };

        const greetings = ["Halo Kak! ✨ Ada yang bisa Admin Neydream bantu?", "Hi! 💅 Mau tanya-tanya seputar nail art?"];

        // Initialize
        document.addEventListener('DOMContentLoaded', async () => {
            console.log("Chatbot Initialized");
            await checkStatus();
            if (currentConversationId) {
                await loadHistory();
                setInterval(syncMessages, 4000);
            }
        });

        async function checkStatus() {
            try {
                const res = await fetch('../api/chat/get_status.php');
                const data = await res.json();
                if (data.success) {
                    currentConversationId = data.conversation_id;
                    isEscalated = data.is_escalated;
                }
            } catch (e) { console.error("Status check failed", e); }
        }

        async function loadHistory() {
            if (!currentConversationId) return;
            try {
                const res = await fetch(`../api/chat/messages.php?conversation_id=${currentConversationId}`);
                const data = await res.json();
                if (data.success) {
                    const chatBox = document.getElementById('chatBox');
                    chatBox.innerHTML = '';
                    
                    // Count bot responses while loading history
                    botResponseCount = 0;
                    
                    data.messages.forEach(msg => {
                        const sender = msg.sender_type === 'customer' ? 'user' : 'admin';
                        addMessageToBox(sender, msg.message, { 
                            scroll: false, 
                            image_path: msg.image_path 
                        });
                        
                        // Count bot/admin responses
                        if (msg.sender_type === 'bot' || msg.sender_type === 'admin') {
                            botResponseCount++;
                        }
                    });
                    
                    chatBox.scrollTop = chatBox.scrollHeight;
                    
                    // If bot already responded 2+ times, set escalated flag
                    if (botResponseCount >= 2) {
                        isEscalated = true;
                    }
                }
            } catch (e) { console.error("History load failed", e); }
        }

        async function syncMessages() {
            if (!currentConversationId) return;
            try {
                const res = await fetch(`../api/chat/messages.php?conversation_id=${currentConversationId}`);
                const data = await res.json();
                if (data.success) {
                    const currentCount = document.querySelectorAll('.message:not(.typing)').length;
                    if (data.messages.length > currentCount) {
                        loadHistory();
                    }
                    // Check escalation status
                    const statusRes = await fetch('../api/chat/get_status.php');
                    const statusData = await statusRes.json();
                    if (statusData.success) isEscalated = statusData.is_escalated;
                }
            } catch (e) { }
        }

        async function sendMessage() {
            const input = document.getElementById('userInput');
            const fileInput = document.getElementById('imageInput');
            const userText = input.value.trim();
            const hasFile = fileInput.files && fileInput.files[0];

            if (!userText && !hasFile) return;
            
            // Temporary storage for file info if any
            const selectedFile = hasFile ? fileInput.files[0] : null;

            input.value = "";
            fileInput.value = "";
            document.getElementById('attachBtn').classList.remove('has-file');

            // Display user message
            addMessageToBox('user', userText, { image_file: selectedFile });

            // Save user message to DB
            try {
                const data = await saveMessageToDB(userText, 'customer', selectedFile);
                if (data && data.success) {
                    if (!currentConversationId) {
                        currentConversationId = data.conversation_id;
                        setInterval(syncMessages, 4000);
                    }
                } else {
                    alert("Gagal mengirim pesan: " + (data.error || "Unknown error"));
                }
            } catch (err) {
                console.error(err);
                alert("Gagal mengirim pesan. Silakan cek koneksi atau ukuran file.");
            }

            // ========================================
            // STRICT BOT RESPONSE LIMIT: MAX 2 TIMES
            // ========================================
            
            // Check if bot should respond
            if (isEscalated || botResponseCount >= 2) {
                // Bot is SILENT - do not respond at all
                console.log("Bot is silent (escalated or reached 2 response limit)");
                return;
            }

            // Bot can still respond (haven't hit limit yet)
            showTypingIndicator();
            setTimeout(async () => {
                hideTypingIndicator();
                
                // Get bot response
                const response = getIntelligentResponse(userText);
                
                // Increment response counter
                botResponseCount++;
                
                // Display bot message
                addMessageToBox('admin', response);
                await saveMessageToDB(response, 'bot');
                
                // Check if this was the final response (2nd response)
                if (botResponseCount >= 2) {
                    // Escalate to admin after 2nd response
                    isEscalated = true;
                    await escalateToAdmin();
                    console.log("Bot has responded 2 times. Now permanently silent.");
                }

            }, 1000);
        }

        async function saveMessageToDB(message, senderType, file = null) {
            const formData = new FormData();
            formData.append('message', message);
            if (currentConversationId) formData.append('conversation_id', currentConversationId);
            if (senderType === 'bot') formData.append('sender_type', 'bot');
            if (file) formData.append('image', file);

            try {
                const res = await fetch('../api/chat/send.php', { method: 'POST', body: formData });
                
                // Debug response status
                if (!res.ok) {
                    throw new Error(`HTTP Error ${res.status}: ${res.statusText}`);
                }

                const text = await res.text(); // Get text first to check for PHP errors
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("Server Response (Not JSON):", text);
                    throw new Error("Server Error (Lihat Console): " + text.substring(0, 50));
                }
            } catch (e) {
                throw e; // Re-throw to be caught by sendMessage
            }
        }

        async function escalateToAdmin() {
            isEscalated = true;
            const formData = new FormData();
            formData.append('conversation_id', currentConversationId);
            try {
                const res = await fetch('../api/chat/escalate.php', { method: 'POST', body: formData });
                if (!res.ok) {
                    alert(`Gagal eskalasi (HTTP Error ${res.status}): ${res.statusText}`);
                }
            } catch (e) {
                alert("Gagal eskalasi (Jaringan/File): " + e.message);
                console.error("Escalate failed", e);
            }
        }

        function addMessageToBox(sender, text, options = {}) {
            const chatBox = document.getElementById('chatBox');
            if (!chatBox) return;
            
            const isScroll = options.scroll !== undefined ? options.scroll : true;
            const imagePath = options.image_path || null;
            const imageFile = options.image_file || null;

            const msg = document.createElement('div');
            msg.className = `message ${sender}`;

            // Handle Media (Image/Video)
            if (imageFile || imagePath) {
                const fileSource = imageFile ? URL.createObjectURL(imageFile) : `../${imagePath}`;
                const fileName = imageFile ? imageFile.name : imagePath;
                const isVideo = fileName.toLowerCase().match(/\.(mp4|webm|mov|avi)$/);

                if (isVideo) {
                    const video = document.createElement('video');
                    video.src = fileSource;
                    video.controls = true;
                    video.style.maxWidth = '100%';
                    video.style.borderRadius = '12px';
                    video.style.marginTop = '8px';
                    msg.appendChild(video);
                } else {
                    const img = document.createElement('img');
                    img.src = fileSource;
                    img.style.maxWidth = '100%';
                    img.style.borderRadius = '12px';
                    img.style.cursor = 'pointer';
                    img.style.marginTop = '8px';
                    img.onclick = () => window.open(img.src);
                    msg.appendChild(img);
                }
            }

            if (text) {
                const textNode = document.createElement('div');
                textNode.innerHTML = text.replace(/\n/g, '<br>');
                msg.appendChild(textNode);
            }

            chatBox.appendChild(msg);
            if (isScroll) chatBox.scrollTop = chatBox.scrollHeight;
        }

        function handleFileSelect(input) {
            const btn = document.getElementById('attachBtn');
            if (input.files && input.files[0]) {
                btn.classList.add('has-file');
            } else {
                btn.classList.remove('has-file');
            }
        }

        function handleKeyPress(e) { if (e.key === "Enter") sendMessage(); }

        function showTypingIndicator() {
            const chatBox = document.getElementById('chatBox');
            const typing = document.createElement('div');
            typing.id = 'typing-indicator';
            typing.className = 'message admin typing';
            typing.innerHTML = '<span></span><span></span><span></span>';
            chatBox.appendChild(typing);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function hideTypingIndicator() {
            const indicator = document.getElementById('typing-indicator');
            if (indicator) indicator.remove();
        }


        function getIntelligentResponse(userInput) {
            const text = userInput.toLowerCase();
            
            // Greetings
            if (text.match(/halo|hai|hi|hello/)) {
                return "Halo Kak! ✨ Ada yang bisa Admin Neydream bantu?";
            }
            
            // Price questions
            if (text.match(/harga|biaya|price|berapa/)) {
                return knowledgeBase.harga.response;
            }
            
            // Booking questions
            if (text.match(/cara|pesan|booking/)) {
                return knowledgeBase.cara_booking.response;
            }

            // Default response for unknown questions
            return "Maaf Kak, untuk pertanyaan ini akan dijawab langsung oleh admin kami ya. Mohon ditunggu sebentar! 😊";
        }


        async function clearChat() {
            const modal = document.getElementById('deleteConfirmModal');
            if (modal) modal.style.display = 'flex';
            else if (confirm('Hapus riwayat chat?')) executeClearChat();
        }

        async function executeClearChat() {
            try {
                const res = await fetch('../api/chat/clear.php', { method: 'POST' });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('chatBox').innerHTML = '';
                    currentConversationId = null;
                    isEscalated = false;
                    addMessageToBox('admin', 'Riwayat dihapus. Ada lagi yang bisa dibantu?');
                }
                closeDeleteModal();
            } catch (e) { alert('Gagal menghapus'); }
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteConfirmModal');
            if (modal) modal.style.display = 'none';
        }

        // Logout Modal Functions
        function openLogoutModal() {
            const modal = document.getElementById('logoutConfirmModal');
            if (modal) modal.style.display = 'flex';
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutConfirmModal');
            if (modal) modal.style.display = 'none';
        }

        function executeLogout() {
            window.location.href = '../auth/logout.php';
        }
    </script>
</body>
</html>
