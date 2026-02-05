/**
 * SIMPLE AUTO-RESPONDER (Shopee/Tokopedia Style)
 * Replies once, then hands off to admin.
 */

const GREETING_MESSAGE = "Halo Kak! 👋\nPesan Kakak sudah kami terima. Admin akan segera membalas chat Kakak sebentar lagi ya. Mohon ditunggu! 😊";

// Track if we have already greeted the user in this session
let hasGreeted = false;

function sendMessage(override = null) {
    const input = document.getElementById('userInput');
    const chatBox = document.getElementById('chatBox');
    const text = (override || input.value).trim();

    if (!text || !chatBox) return;

    // 1. Render User Message
    const userDiv = document.createElement('div');
    userDiv.className = 'message user';
    userDiv.textContent = text;
    chatBox.appendChild(userDiv);

    if (!override) input.value = "";
    chatBox.scrollTop = chatBox.scrollHeight;

    // 2. Escalate to Admin IMMEDIATELY (Save to DB)
    if (typeof window.escalateToAdmin === 'function') {
        window.escalateToAdmin(text);
    }

    // 3. Auto-Reply ONLY if this is the first message
    if (!hasGreeted) {
        hasGreeted = true; // Mark as greeted

        // Show typing indicator nicely
        const typingId = "ai-typing-" + Date.now();
        const typeDiv = document.createElement('div');
        typeDiv.className = 'message admin typing';
        typeDiv.id = typingId;
        typeDiv.innerHTML = '<span></span><span></span><span></span>';
        chatBox.appendChild(typeDiv);
        chatBox.scrollTop = chatBox.scrollHeight;

        setTimeout(() => {
            const el = document.getElementById(typingId);
            if (el) el.remove();

            renderBotResponse(GREETING_MESSAGE);
        }, 1000);
    }
}

function renderBotResponse(html) {
    const chatBox = document.getElementById('chatBox');
    const botDiv = document.createElement('div');
    botDiv.className = 'message admin';

    const content = document.createElement('div');
    content.innerHTML = html.replace(/\n/g, '<br>');
    botDiv.appendChild(content);

    chatBox.appendChild(botDiv);
    chatBox.scrollTop = chatBox.scrollHeight;
}


function handleKeyPress(e) {
    if (e.key === 'Enter') sendMessage();
}

console.log('🤖 Simple Auto-Responder Active');
