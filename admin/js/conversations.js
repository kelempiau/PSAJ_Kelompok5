let currentConversationId = null;
let currentUserId = null;
let conversations = [];
let messagesInterval = null;

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    loadConversations();
    setInterval(loadConversations, 5000);
});

async function loadConversations() {
    try {
        const response = await fetch('../api/chat/conversations.php');
        const text = await response.text(); // Read as text first to debug

        try {
            const data = JSON.parse(text);
            if (data.success) {
                conversations = data.conversations;
                renderConversationList(conversations);
            } else {
                console.error('API Logic Error:', data.error);
                document.getElementById('conversationList').innerHTML = `<div class="empty-list">API Error: ${data.error}</div>`;
            }
        } catch (e) {
            console.error('JSON Parse Error:', e);
            console.log('Raw Output:', text);
            document.getElementById('conversationList').innerHTML = `<div class="empty-list">
                <strong>Error Parse JSON</strong><br>
                <small style="font-size:10px; color:red;">${text.substring(0, 100)}...</small>
            </div>`;
        }
    } catch (error) {
        console.error('Network Error:', error);
        document.getElementById('conversationList').innerHTML = '<div class="empty-list">Kesalahan Jaringan (Fetch)</div>';
    }
}

function renderConversationList(convos) {
    const list = document.getElementById('conversationList');
    list.innerHTML = '';

    if (convos.length === 0) {
        list.innerHTML = '<div class="empty-list">Tidak ada pengguna terdaftar</div>';
        return;
    }

    convos.forEach(conv => {
        const div = document.createElement('div');
        div.className = 'conversation-item';

        // Active item check by User ID or Conversation ID
        if (conv.user_id == currentUserId) {
            div.classList.add('active');
        }
        if (conv.unread_count > 0) div.classList.add('unread');

        div.onclick = () => selectUser(conv);

        const statusBadge = conv.conv_status === 'escalated' ? '<span class="status-badge escalated">Butuh Admin</span>' : '';
        // remove onlineDot logic

        div.innerHTML = `
            <div class="conv-avatar">
                ${conv.username.charAt(0).toUpperCase()}
            </div>
            <div class="conv-details">
                <div class="conv-name">
                    ${conv.username} 
                    ${statusBadge}
                </div>
                <div class="conv-preview">${conv.last_message}</div>
            </div>
            ${conv.unread_count > 0 ? `<div class="unread-badge">${conv.unread_count}</div>` : ''}
        `;

        list.appendChild(div);
    });
}

function selectUser(user) {
    currentConversationId = user.conversation_id;
    currentUserId = user.user_id;

    // Reset UI
    document.getElementById('chatHeader').innerHTML = `
        <div class="chat-info">
            <h4 style="margin: 0; line-height: 1.2;">${user.username}</h4>
            <span style="font-size: 11px; color: var(--text-muted); display: block; line-height: 1;">${user.email || ''}</span>
        </div>
        <div class="chat-actions">
            <button class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; color: #ef4444; border-color: var(--primary-light);" onclick="openAdminDeleteModal()">Hapus Riwayat</button>
        </div>
    `;

    document.getElementById('chatInputContainer').style.display = 'flex';
    document.getElementById('messageInput').focus();

    // Re-render list to update active state
    renderConversationList(conversations);

    if (currentConversationId) {
        loadMessages();
        if (messagesInterval) clearInterval(messagesInterval);
        messagesInterval = setInterval(loadMessages, 3000);

        // Mark as read
        fetch('../api/chat/mark_read.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `conversation_id=${currentConversationId}`
        });
    } else {
        // New conversation
        document.getElementById('chatMessages').innerHTML = `
            <div class="empty-state">
                <p>Mulai percakapan baru dengan <b>${user.username}</b></p>
                <p style="font-size: 12px; margin-top: 10px;">Ketikan pesan di bawah untuk memulai obrolan.</p>
            </div>
        `;
        if (messagesInterval) clearInterval(messagesInterval);
    }
}

async function loadMessages() {
    if (!currentConversationId) return;

    try {
        const response = await fetch(`../api/chat/messages.php?conversation_id=${currentConversationId}`);
        const data = await response.json();
        if (data.success) {
            renderMessages(data.messages);
        }
    } catch (e) {
        console.error("Failed to load messages:", e);
    }
}

function renderMessages(messages) {
    const container = document.getElementById('chatMessages');
    const shouldScroll = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;

    container.innerHTML = '';

    if (messages.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>Belum ada pesan dalam percakapan ini.</p></div>';
        return;
    }

    messages.forEach(msg => {
        const div = document.createElement('div');
        div.className = `message ${msg.sender_type}`;

        const content = document.createElement('div');
        content.className = 'message-content';

        if (msg.image_path) {
            const isVideo = msg.image_path.toLowerCase().match(/\.(mp4|webm|mov|avi)$/);
            if (isVideo) {
                const video = document.createElement('video');
                video.src = `../${msg.image_path}`;
                video.controls = true;
                video.style.maxWidth = '250px';
                video.style.borderRadius = '8px';
                content.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = `../${msg.image_path}`;
                img.style.maxWidth = '250px';
                img.style.borderRadius = '8px';
                img.style.cursor = 'pointer';
                img.onclick = () => window.open(img.src);
                content.appendChild(img);
            }
        }

        if (msg.message) {
            const text = document.createElement('p');
            text.textContent = msg.message;
            content.appendChild(text);
        }

        const time = document.createElement('div');
        time.className = 'message-time';
        time.textContent = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        div.appendChild(content);
        div.appendChild(time);
        container.appendChild(div);
    });

    if (shouldScroll) container.scrollTop = container.scrollHeight;
}

async function sendAdminMessage() {
    const input = document.getElementById('messageInput');
    const fileInput = document.getElementById('imageInput');
    const message = input.value.trim();

    if (!message && !fileInput.files.length) return;

    const formData = new FormData();
    if (currentConversationId) formData.append('conversation_id', currentConversationId);
    formData.append('target_user_id', currentUserId);
    formData.append('message', message);

    if (fileInput.files.length > 0) {
        formData.append('image', fileInput.files[0]);
    }

    // Disable input while sending
    input.disabled = true;

    try {
        const response = await fetch('../api/chat/send.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            input.value = '';
            fileInput.value = '';
            handleAdminFileSelect(fileInput); // Reset icon color
            if (data.conversation_id && !currentConversationId) {
                currentConversationId = data.conversation_id;
                // Start polling
                loadMessages();
                messagesInterval = setInterval(loadMessages, 3000);
            }
            loadMessages();
            loadConversations();
        } else {
            alert('Gagal mengirim: ' + data.error);
        }
    } catch (error) {
        console.error('Failed to send message:', error);
        alert('Kesalahan jaringan. Gagal mengirim pesan.');
    } finally {
        input.disabled = false;
        input.focus();
    }
}

function handleAdminFileSelect(input) {
    const btn = document.getElementById('adminAttachBtn');
    if (!btn) return;
    if (input.files && input.files[0]) {
        btn.classList.add('has-file');
        btn.style.background = '#e0e7ff';
        btn.style.color = 'var(--primary)';
    } else {
        btn.classList.remove('has-file');
        btn.style.background = '#f1f5f9';
        btn.style.color = 'var(--text-secondary)';
    }
}

function handleKeyPress(e) {
    if (e.key === 'Enter') sendAdminMessage();
}

// Global modal handlers
function openAdminDeleteModal() {
    if (!currentConversationId) return;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeAdminDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

async function executeAdminDeleteChat() {
    if (!currentConversationId) return;

    try {
        const formData = new FormData();
        formData.append('conversation_id', currentConversationId);

        const response = await fetch('../api/chat/clear.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            document.getElementById('chatMessages').innerHTML = '<div class="empty-state"><p>Riwayat chat telah dihapus.</p></div>';
            closeAdminDeleteModal();
            loadConversations();
        } else {
            alert('Gagal: ' + data.error);
        }
    } catch (error) {
        alert('Terjadi kesalahan koneksi.');
    }
}
