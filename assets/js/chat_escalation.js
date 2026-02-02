let currentConversationId = null;
let messageCheckInterval = null;

window.escalateToAdmin = async function (userMessage) {
    if (!currentConversationId) {
        const formData = new FormData();
        formData.append('message', userMessage);

        const response = await fetch('../api/chat/send.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            currentConversationId = data.conversation_id;

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
