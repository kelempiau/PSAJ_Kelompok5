let currentConversationId = null;
let messageCheckInterval = null;

// Heartbeat for user online status
async function sendHeartbeat() {
    try {
        await fetch('../api/user/heartbeat.php');
    } catch (e) { }
}
setInterval(sendHeartbeat, 30000);
sendHeartbeat();

// Check Admin Status
async function checkAdminStatus() {
    try {
        const response = await fetch('../api/chat/conversations.php?check_admin=1');
        const data = await response.json();
        const statusEl = document.querySelector('.chat-header-info .status');
        if (statusEl && data.admin_online) {
            statusEl.textContent = 'Online';
            statusEl.style.color = '#34D399';
        } else if (statusEl) {
            statusEl.textContent = 'Offline';
            statusEl.style.color = '#9CA3AF';
        }
    } catch (e) { }
}
setInterval(checkAdminStatus, 60000);
checkAdminStatus();

window.escalateToAdmin = async function (userMessage) {
    // Don't send the message again - AI already showed it
    // Just create conversation if needed and mark as escalated
    if (!currentConversationId) {
        // Create conversation silently without sending escalation message again
        const formData = new FormData();
        formData.append('message', '__ESCALATION_FLAG__'); // Internal marker

        const response = await fetch('../api/chat/send.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            currentConversationId = data.conversation_id;

            // Mark as escalated
            await fetch('../api/chat/escalate.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `conversation_id=${currentConversationId}`
            });

            startMessagePolling();
        }
    }
};

function startMessagePolling() {
    if (messageCheckInterval) return;

    messageCheckInterval = setInterval(async () => {
        if (!currentConversationId) return;

        const response = await fetch(`../api/chat/messages.php?conversation_id=${currentConversationId}`);
        const data = await response.json();

        if (data.success) {
            const chatBox = document.getElementById('chatBox');
            data.messages.forEach(msg => {
                if (msg.sender_type === 'admin' && !document.getElementById(`msg-${msg.id}`)) {
                    const div = document.createElement('div');
                    div.id = `msg-${msg.id}`;
                    div.className = 'message admin';
                    div.innerHTML = msg.message.replace(/\n/g, '<br>');
                    chatBox.appendChild(div);
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            });

            fetch('../api/chat/mark_read.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `conversation_id=${currentConversationId}`
            });
        }
    }, 3000);
}

window.addEventListener('beforeunload', () => {
    if (messageCheckInterval) clearInterval(messageCheckInterval);
});
