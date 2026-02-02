let currentConversationId = null;
let conversations = [];
let messagesInterval = null;

async function loadConversations() {
    try {
        const response = await fetch('../api/chat/conversations.php');
        const data = await response.json();

        if (data.success) {
            conversations = data.conversations;
            renderConversationList(conversations);
            updateUnreadCount();
        }
    } catch (error) {
        console.error('Failed to load conversations:', error);
    }
}

function renderConversationList(convos) {
    const list = document.getElementById('conversationList');
    list.innerHTML = '';

    if (convos.length === 0) {
        list.innerHTML = '<div class="empty-list">No conversations yet</div>';
        return;
    }

    convos.forEach(conv => {
        const div = document.createElement('div');
        div.className = 'conversation-item';
        if (conv.id == currentConversationId) div.classList.add('active');
        if (conv.unread_count > 0) div.classList.add('unread');

        div.onclick = () => selectConversation(conv.id);

        const statusBadge = conv.status === 'escalated' ? '<span class="status-badge escalated">Escalated</span>' : '';

        div.innerHTML = `
            <div class="conv-avatar">${conv.username ? conv.username.charAt(0).toUpperCase() : 'U'}</div>
            <div class="conv-details">
                <div class="conv-name">${conv.username || 'Guest'} ${statusBadge}</div>
                <div class="conv-preview">${conv.last_message || 'No messages'}</div>
            </div>
            ${conv.unread_count > 0 ? `<div class="unread-badge">${conv.unread_count}</div>` : ''}
        `;

        list.appendChild(div);
    });
}

function updateUnreadCount() {
    const total = conversations.reduce((sum, conv) => sum + parseInt(conv.unread_count || 0), 0);
    const badge = document.getElementById('totalUnread');
    if (badge) {
        badge.textContent = total;
        badge.style.display = total > 0 ? 'inline-block' : 'none';
    }
}

async function selectConversation(convId) {
    currentConversationId = convId;

    const conv = conversations.find(c => c.id == convId);
    if (!conv) return;

    document.getElementById('chatHeader').innerHTML = `
        <div class="chat-info">
            <h4>${conv.username || 'Guest'}</h4>
            <span class="status-text">${conv.email || ''}</span>
        </div>
    `;

    document.getElementById('chatInputContainer').style.display = 'flex';

    renderConversationList(conversations);

    await loadMessages();

    if (messagesInterval) clearInterval(messagesInterval);
    messagesInterval = setInterval(loadMessages, 3000);

    fetch('../api/chat/mark_read.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `conversation_id=${convId}`
    }).then(() => loadConversations());
}

async function loadMessages() {
    if (!currentConversationId) return;

    const response = await fetch(`../api/chat/messages.php?conversation_id=${currentConversationId}`);
    const data = await response.json();

    if (data.success) {
        renderMessages(data.messages);
    }
}

function renderMessages(messages) {
    const container = document.getElementById('chatMessages');
    const shouldScroll = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;

    container.innerHTML = '';

    messages.forEach(msg => {
        const div = document.createElement('div');
        div.className = `message ${msg.sender_type}`;

        const content = document.createElement('div');
        content.className = 'message-content';

        if (msg.image_path) {
            const img = document.createElement('img');
            img.src = `../${msg.image_path}`;
            img.alt = 'Attachment';
            img.style.maxWidth = '250px';
            img.style.borderRadius = '8px';
            content.appendChild(img);
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

    if (shouldScroll) {
        container.scrollTop = container.scrollHeight;
    }
}

async function sendAdminMessage() {
    const input = document.getElementById('messageInput');
    const fileInput = document.getElementById('imageInput');
    const message = input.value.trim();

    if (!message && !fileInput.files.length) return;

    const formData = new FormData();
    formData.append('conversation_id', currentConversationId);
    formData.append('message', message);

    if (fileInput.files.length > 0) {
        formData.append('image', fileInput.files[0]);
    }

    try {
        const response = await fetch('../api/chat/send.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            input.value = '';
            fileInput.value = '';
            loadMessages();
            loadConversations();
        }
    } catch (error) {
        console.error('Failed to send message:', error);
    }
}

function handleKeyPress(e) {
    if (e.key === 'Enter') {
        sendAdminMessage();
    }
}

loadConversations();
setInterval(loadConversations, 5000);
