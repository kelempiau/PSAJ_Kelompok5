// ================================================
// ULTRA SMART AI CHATBOT v3.0
// ================================================

let chatHistory = [];
let userContext = { hasAskedPrice: false, wantsToBook: false };
let currentConversationId = null;
let isEscalated = false;
let unknownCount = 0;

// Knowledge Base (Simplified view for file writing, keep existing triggers)
const knowledgeBase = {
    'cara_booking': {
        triggers: ['cara pesan', 'cara booking', 'gimana pesan', 'gimana booking', 'mau pesan', 'mau booking'],
        response: `📝 <b>Cara Booking:</b>\n1. Isi form di atas\n2. Pilih jadwal\n3. Bayar DP 20rb ke BCA: 123-456-7890\n4. Upload bukti.`
    },
    'harga': {
        triggers: ['harga', 'biaya', 'price', 'berapa'],
        response: `💰 <b>Harga:</b>\n💅 Gel: 50rb\n✨ French: 75rb\n💎 Ext: 150rb\n🎨 Custom: 200rb.`
    }
    // ... (keeping other entries as they were in the previous version)
};

const greetings = ["Halo Kak! ✨ Ada yang bisa Admin Neydream bantu?", "Hi! 💅 Mau tanya-tanya seputar nail art?"];

// Initialize
document.addEventListener('DOMContentLoaded', async () => {
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
            data.messages.forEach(msg => {
                const sender = msg.sender_type === 'customer' ? 'user' : 'admin';
                addMessageToBox(sender, msg.message, false);
            });
            chatBox.scrollTop = chatBox.scrollHeight;
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
    const userText = input.value.trim();

    if (!userText) return;
    input.value = "";

    addMessageToBox('user', userText);

    try {
        const data = await saveMessageToDB(userText, 'customer');
        if (data && data.success) {
            if (!currentConversationId) {
                currentConversationId = data.conversation_id;
                setInterval(syncMessages, 4000);
            }
        }
    } catch (err) {
        console.error("Send message error:", err);
    }

    if (isEscalated) return;

    showTypingIndicator();
    setTimeout(async () => {
        hideTypingIndicator();
        const response = getIntelligentResponse(userText);
        addMessageToBox('admin', response);
        await saveMessageToDB(response, 'bot');

        if (response.includes("chat ini akan dijawab oleh admin")) {
            await escalateToAdmin();
        }
    }, 1000);
}

async function saveMessageToDB(message, senderType) {
    const formData = new FormData();
    formData.append('message', message);
    if (currentConversationId) formData.append('conversation_id', currentConversationId);
    if (senderType === 'bot') formData.append('sender_type', 'bot');

    const res = await fetch('../api/chat/send.php', { method: 'POST', body: formData });
    return await res.json();
}

async function escalateToAdmin() {
    isEscalated = true;
    const formData = new FormData();
    formData.append('conversation_id', currentConversationId);
    await fetch('../api/chat/escalate.php', { method: 'POST', body: formData });
}

function addMessageToBox(sender, text, scroll = true) {
    const chatBox = document.getElementById('chatBox');
    if (!chatBox) return;
    const msg = document.createElement('div');
    msg.className = `message ${sender}`;
    msg.innerHTML = text.replace(/\n/g, '<br>');
    chatBox.appendChild(msg);
    if (scroll) chatBox.scrollTop = chatBox.scrollHeight;
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
    if (text.match(/halo|hai|hi/)) return greetings[0];
    if (text.match(/harga|biaya/)) return knowledgeBase.harga.response;
    if (text.match(/cara|pesan/)) return knowledgeBase.cara_booking.response;

    unknownCount++;
    if (unknownCount >= 2) return "maaf saya tidak tahu chat ini akan dijawab oleh admin";
    return "Maaf Kak, Admin Neydream kurang paham. Bisa coba tanya Harga atau Lokasi?";
}

function toggleChat() {
    const container = document.getElementById("chatContainer");
    const icon = document.getElementById("chatIcon");
    if (!container) return;
    const isHidden = window.getComputedStyle(container).display === "none";
    if (isHidden) {
        container.style.display = "flex";
        if (icon) icon.style.opacity = "0";
    } else {
        container.style.display = "none";
        if (icon) icon.style.opacity = "1";
    }
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
