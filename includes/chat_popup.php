    <?php 
    if(!isset($isLoggedIn)) $isLoggedIn = isset($_SESSION['user_id']);
    if(!isset($isAdmin)) $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    if ($isLoggedIn && !$isAdmin): 
    ?>
    <style>
        .floating-chat-bubble {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ea3671, #be123c);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(234, 54, 113, 0.4);
            z-index: 9999999;
            transition: 0.3s;
        }
        .floating-chat-bubble:hover { transform: scale(1.1) rotate(-5deg); }
        .floating-chat-bubble img { width: 30px; height: 30px; filter: brightness(0) invert(1); }

        .chat-popup-container {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 380px;
            height: 550px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            display: none;
            flex-direction: column;
            z-index: 9999999;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            animation: popupSlideIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes popupSlideIn {
            from { opacity: 0; transform: translateY(20px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        /* Popup Header */
        .cp-header {
            padding: 15px 20px;
            background: white;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .cp-user { display: flex; align-items: center; gap: 12px; }
        .cp-avatar { 
            width: 32px; height: 32px; border-radius: 50%; 
            background: #ea3671; display: flex; align-items: center; justify-content: center;
            overflow: hidden; color: white; font-size: 14px;
        }
        .cp-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .cp-info h4 { margin: 0; font-size: 14px; font-weight: 700; color: #1e293b; }
        .cp-info p { margin: 0; font-size: 11px; color: #10b981; font-weight: 600; }
        .cp-close { cursor: pointer; color: #64748b; font-size: 20px; transition: 0.2s; }
        .cp-close:hover { color: #ef4444; }

        /* Popup Body */
        .cp-body {
            flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px;
            background: rgba(255, 241, 242, 0.3);
            scrollbar-width: thin;
        }
        .cp-msg { max-width: 80%; padding: 10px 15px; border-radius: 18px; font-size: 13px; line-height: 1.5; }
        .cp-msg.admin { background: white; align-self: flex-start; border-radius: 18px 18px 18px 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .cp-msg.user { background: #ea3671; color: white; align-self: flex-end; border-radius: 18px 18px 4px 18px; box-shadow: 0 4px 10px rgba(234, 54, 113, 0.2); }
        .cp-msg img, .cp-msg video { max-width: 100%; border-radius: 12px; display: block; margin-bottom: 5px; cursor: pointer; }

        /* Popup Footer */
        .cp-footer { padding: 12px 15px; background: white; border-top: 1px solid #f1f5f9; display: flex; gap: 10px; align-items: center; }
        .cp-input { flex: 1; border: none; background: #f8fafc; padding: 10px 15px; border-radius: 12px; font-size: 13px; outline: none; }
        .cp-send { width: 40px; height: 40px; background: #ea3671; border: none; border-radius: 10px; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
        .cp-send:hover { transform: scale(1.05); background: #be123c; }

        @media (max-width: 480px) {
            .chat-popup-container {
                width: 100%; height: 100%; bottom: 0; right: 0; border-radius: 0;
            }
        }
    </style>

    <div class="floating-chat-bubble" id="chatBubble" onclick="toggleChatPopup()">
        <img src="https://cdn-icons-png.flaticon.com/512/5968/5968841.png" alt="Chat">
    </div>

    <div class="chat-popup-container" id="chatPopup">
        <div class="cp-header">
            <div class="cp-user">
                    <div class="cp-avatar" id="cpAdminAvatar">
                        <?php 
                        $adminPic = null;
                        if (isset($conn)) {
                            $stCheck = $conn->query("SHOW COLUMNS FROM studio_settings LIKE 'admin_profile_pic'");
                            if ($stCheck && $stCheck->num_rows > 0) {
                                $stRes = $conn->query("SELECT admin_profile_pic FROM studio_settings WHERE id = 1");
                                if ($stRes) {
                                    $stData = $stRes->fetch_assoc();
                                    $adminPic = $stData['admin_profile_pic'] ?? null;
                                }
                            }
                        }
                        
                        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                        $adminPicUrl = $adminPic ? (strpos($adminPic, 'http') === 0 ? $adminPic : $base_url . '/' . ltrim($adminPic, '/')) : 'https://cdn-icons-png.flaticon.com/512/5968/5968841.png';
                        
                        if(strpos($adminPicUrl, 'assets/') !== false && !file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $adminPic) && !file_exists('../' . $adminPic)) {
                            $adminPicUrl = 'https://cdn-icons-png.flaticon.com/512/5968/5968841.png';
                        }
                        ?>
                        <img src="<?= $adminPicUrl ?>" alt="Admin Profile" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/5968/5968841.png';">
                    </div>
                <div class="cp-info">
                    <h4>Neydream Assistant</h4>
                    <p>● Online</p>
                </div>
            </div>
            <div class="cp-close" onclick="toggleChatPopup()">&times;</div>
        </div>
        <div class="cp-body" id="cpBody">
            <div class="cp-msg admin">
                Halo Kak <b><?= htmlspecialchars($user_name ?? $username ?? '') ?></b>! ✨ Mau tanya-tanya soal nail art atau booking? Aku siap bantu ya!
            </div>
        </div>
        <div class="cp-footer">
            <input type="text" class="cp-input" id="cpInput" placeholder="Tulis pesan..." onkeypress="if(event.key==='Enter') sendPopupMsg()">
            <button class="cp-send" onclick="sendPopupMsg()">➤</button>
        </div>
    </div>

    <script>
        var currentConvId = null;
        var isPopupOpen = false;
        var isEscalated = false;

        function toggleChatPopup() {
            const popup = document.getElementById('chatPopup');
            isPopupOpen = !isPopupOpen;
            popup.style.display = isPopupOpen ? 'flex' : 'none';
            if (isPopupOpen) {
                loadPopupHistory();
                if (!window.chatInterval) {
                    window.chatInterval = setInterval(loadPopupHistory, 4000);
                }
            } else {
                if (window.chatInterval) {
                    clearInterval(window.chatInterval);
                    window.chatInterval = null;
                }
            }
        }
        // Expose ke window agar bisa dipanggil dari mana saja
        window.toggleChatPopup = toggleChatPopup;
        const CHAT_VERSION = "4.5.0";
        console.log("Chat Engine Loaded. Version:", CHAT_VERSION);
        function parseSafeJSON(text) {
            try {
                const START = "!!!JSON_START!!!";
                const END = "!!!JSON_END!!!";
                const sIdx = text.indexOf(START);
                const eIdx = text.lastIndexOf(END);

                if (sIdx !== -1 && eIdx !== -1) {
                    return JSON.parse(text.substring(sIdx + START.length, eIdx).trim());
                }
                const match = text.match(/\{[\s\S]*\}/);
                if (match) {
                    try {
                        return JSON.parse(match[0]);
                    } catch (inner) {
                        let cleaned = match[0].replace(/<\/?[^>]+(>|$)/g, "");
                        return JSON.parse(cleaned);
                    }
                }
                return JSON.parse(text.trim());
            } catch (e) {
                console.error("JSON Parse Error:", e, "Raw Content:", text);
                throw new Error("Gagal memproses data server. Silakan coba lagi.");
            }
        }
        <?php
            // Deteksi apakah file yang meng-include kita ada di root atau subfolder
            // Bandingkan DOCUMENT_ROOT dengan direktori file yang sedang diinclude
            $callerDir = dirname($_SERVER['SCRIPT_FILENAME']);
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            $callerDir = rtrim($callerDir, '/\\');
            // Hitung kedalaman relatif callerDir dari docRoot
            $chatApiPrefix = ($callerDir === $docRoot) ? '' : '../';
        ?>
        function getAPIUrl(base) { return '<?= $chatApiPrefix ?>' + base; }

        async function loadPopupHistory() {
            try {
                const statusRes = await fetch(getAPIUrl('api/chat/get_status.php'));
                const statusRaw = await statusRes.text();
                const statusData = parseSafeJSON(statusRaw);
                
                if (statusData.success && statusData.conversation_id) {
                    currentConvId = statusData.conversation_id;
                    isEscalated = statusData.is_escalated;
                    const msgRes = await fetch(getAPIUrl(`api/chat/messages.php?conversation_id=${currentConvId}`));
                    const msgRaw = await msgRes.text();
                    const msgData = parseSafeJSON(msgRaw);
                    if (msgData.success) {
                        const body = document.getElementById('cpBody');
                        body.innerHTML = '';
                        const welcome = document.createElement('div');
                        welcome.className = 'cp-msg admin';
                        welcome.innerHTML = 'Halo Kak <b><?= htmlspecialchars($user_name ?? $username ?? '') ?></b>! ✨ Ada yang bisa Ney bantu?';
                        body.appendChild(welcome);

                        msgData.messages.forEach(m => {
                            const div = document.createElement('div');
                            div.className = `cp-msg ${m.sender_type === 'customer' ? 'user' : 'admin'}`;
                            
                            let html = '';
                            if (m.image_path) {
                                const isVideo = m.image_path.toLowerCase().match(/\.(mp4|webm|mov|avi)$/);
                                if (isVideo) {
                                    html += `<video src="${m.image_path}" controls style="max-width:100%; border-radius:8px;"></video>`;
                                } else {
                                    html += `<img src="${m.image_path}" onclick="window.open(this.src)" style="max-width:100%; border-radius:8px; cursor:pointer;">`;
                                }
                            }
                            if (m.message) {
                                html += `<span>${m.message}</span>`;
                            }
                            
                            div.innerHTML = html;
                            body.appendChild(div);
                        });
                        body.scrollTop = body.scrollHeight;
                    }
                }
            } catch (e) {
                console.warn("History Load Error:", e.message);
            }
        }

        async function sendPopupMsg() {
            const input = document.getElementById('cpInput');
            const text = input.value.trim();
            if (!text) return;
            input.value = '';

            const body = document.getElementById('cpBody');
            const userMsg = document.createElement('div');
            userMsg.className = 'cp-msg user';
            userMsg.innerText = text;
            body.appendChild(userMsg);
            body.scrollTop = body.scrollHeight;

            const formData = new FormData();
            formData.append('message', text);
            if (currentConvId) formData.append('conversation_id', currentConvId);

            try {
                const res = await fetch(getAPIUrl('api/chat/send.php'), { method: 'POST', body: formData });
                const resRaw = await res.text();
                const data = parseSafeJSON(resRaw);
                
                if (data.success && !currentConvId) currentConvId = data.conversation_id;
                const triggerAdmin = /admin|operator|manusia|beralih|cs|bantuan|tanya admin/i.test(text);
                
                if (triggerAdmin && !isEscalated) {
                    isEscalated = true;
                    const escForm = new FormData();
                    escForm.append('conversation_id', currentConvId);
                    await fetch(getAPIUrl('api/chat/escalate.php'), { method: 'POST', body: escForm });
                    
                    const botReply = "Baik Kak, aku sambungkan ke Admin ya. Mohon tunggu sebentar... ✨";
                    await saveBotMessage(botReply);
                    loadPopupHistory();
                    return;
                }

                if (!isEscalated) {
                    const typing = document.createElement('div');
                    typing.className = 'cp-msg admin';
                    typing.innerHTML = '<span style="font-style:italic; opacity:0.7;">Ney sedang membalas...</span>';
                    body.appendChild(typing);
                    body.scrollTop = body.scrollHeight;

                    await callNovaAI(text);
                    if (typing && typing.parentNode === body) {
                        body.removeChild(typing);
                    }
                    setTimeout(loadPopupHistory, 300);
                }
            } catch (e) {
                console.error("Send Error:", e);
                const errDiv = document.createElement('div');
                errDiv.className = 'cp-msg admin';
                errDiv.style.background = '#fee2e2';
                errDiv.style.color = '#b91c1c';
                errDiv.innerHTML = `⚠️ <b>Sistem Gangguan</b><br><small>${e.message}</small>`;
                body.appendChild(errDiv);
                body.scrollTop = body.scrollHeight;
            }
        }
        async function fetchWithTimeout(resource, options = {}) {
            const { timeout = 5000 } = options;
            const controller = new AbortController();
            const id = setTimeout(() => controller.abort(), timeout);
            const response = await fetch(resource, { ...options, signal: controller.signal });
            clearTimeout(id);
            return response;
        }

        async function callNovaAI(userText) {
            let serverRaw = "";
            try {
                const res = await fetchWithTimeout(getAPIUrl('api/chat_ai.php'), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: userText }),
                    timeout: 5000 
                });
                
                serverRaw = await res.text();
                let data;
                try {
                    data = parseSafeJSON(serverRaw);
                } catch (parseErr) {
                    data = { success: false };
                }
                
                if (data.success && data.reply) {
                    await saveBotMessage(data.reply);
                    return;
                }
            } catch (e) {
                console.warn("Server AI took too long or failed, switching to fast fallback...");
            }
            try {
                const API_KEY = "AIzaSyCvJPGGc0yq6Vl_Z_kMIT59ioU_cXRFHjA";
                const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=${API_KEY}`;
                
                <?php
                    $studio_wa = "Belum diatur";
                    $studio_email = "Belum diatur";
                    $studio_dp = "50";
                    if (isset($conn)) {
                        $setRes = $conn->query("SELECT whatsapp, email, min_dp_percent FROM studio_settings WHERE id = 1");
                        if ($setRes && $setRes->num_rows > 0) {
                            $setData = $setRes->fetch_assoc();
                            $studio_wa = $setData['whatsapp'] ?? "Belum diatur";
                            $studio_email = $setData['email'] ?? "Belum diatur";
                            $studio_dp = $setData['min_dp_percent'] ?? "50";
                        }
                    }
                ?>
                const sysWa = "<?= htmlspecialchars($studio_wa) ?>";
                const sysEmail = "<?= htmlspecialchars($studio_email) ?>";
                const sysDp = "<?= htmlspecialchars($studio_dp) ?>";

                const systemInstruction = `Identitas: Kamu adalah Ney, asisten virtual dari Neydream Studio. KAMI ADALAH STUDIO NAIL ART (KECANTIKAN KUKU), BUKAN AGENSI KREATIF/DESAIN/MEDIA SOSIAL. Jawab sangat singkat (max 2 kalimat) dengan gaya ramah & bahasa Indonesia santai. Fokus hanya pada kuku (Nail Art, Extension, Menicure, Pedicure). Jika ditanya di luar urusan kuku (seperti coding, pemrograman, tugas sekolah, dll), TOLAK DENGAN TEGAS DAN SOPAN karena itu bukan bidang kita. Jika pelanggan menanyakan nomor WhatsApp studio, berikan nomor ini: ${sysWa}. Jika pelanggan menanyakan email studio, berikan email ini: ${sysEmail}. Jika pelanggan bertanya tentang DP (Down Payment), beritahu bahwa minimal DP adalah ${sysDp}%. ✨.`;
                
                const clientRes = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        system_instruction: { parts: [{ text: systemInstruction }] },
                        contents: [{ parts: [{ text: userText }] }]
                    })
                });
                
                const clientData = await clientRes.json();
                if (clientData.candidates && clientData.candidates[0].content.parts[0].text) {
                    await saveBotMessage(clientData.candidates[0].content.parts[0].text);
                } else {
                    throw new Error("AI Limit reached.");
                }
            } catch (fallbackErr) {
                throw new Error("Koneksi sangat lambat. Silakan hubungi via WhatsApp.");
            }
        }

        async function saveBotMessage(reply) {
            try {
                const fm = new FormData();
                fm.append('message', reply);
                fm.append('conversation_id', currentConvId);
                fm.append('sender_type', 'bot');
                
                const saveRes = await fetch(getAPIUrl('api/chat/send.php'), { method: 'POST', body: fm });
                const saveText = await saveRes.text();
                const saveData = parseSafeJSON(saveText);
                if (!saveData.success) console.warn("Save bot message failed:", saveData.error);
            } catch (e) {
                console.warn("Save Error:", e.message);
            }
        }
    </script>
    <?php endif; ?>
