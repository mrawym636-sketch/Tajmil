<!-- ========== BOUTON CHATBOT FLOTTANT ========== -->
<div class="chatbot-toggle" id="chatbotToggle" onclick="toggleChatbot()">
    <i class="fas fa-comments" id="chatbotIcon"></i>
    <span class="chatbot-badge" id="chatbotBadge">1</span>
</div>

<!-- ========== FENÊTRE CHATBOT ========== -->
<div class="chatbot-window" id="chatbotWindow">
    <div class="chatbot-header">
        <div class="chatbot-header-info">
            <div class="chatbot-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div>
                <h4>Assistante Tajmil</h4>
                <span class="chatbot-status"><span class="online-dot"></span> En ligne</span>
            </div>
        </div>
        <button class="chatbot-close" onclick="toggleChatbot()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="chatbot-messages" id="chatbotMessages">
        <div class="chat-message bot-message">
            <div class="message-avatar"><i class="fas fa-robot"></i></div>
            <div class="message-content">
                <p>Bonjour ! 🌸 Je suis l'assistante Tajmil Lwajh.</p>
                <p>Salam ! Kifach n9dar n3awnek ?</p>
                <div class="quick-replies">
                    <button onclick="sendQuickReply('produits')">💄 Produits</button>
                    <button onclick="sendQuickReply('livraison')">🚚 Livraison</button>
                    <button onclick="sendQuickReply('prix')">💰 Prix</button>
                    <button onclick="sendQuickReply('contact')">📞 Contact</button>
                    <button onclick="sendQuickReply('promo')">🎁 Promo</button>
                    <button onclick="sendQuickReply('aide')">🤝 Aide</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="chatbot-input-area">
        <input type="text" id="chatbotInput" placeholder="Écrivez votre message..." 
               onkeypress="if(event.key==='Enter') sendChatMessage()">
        <button class="chatbot-send" onclick="sendChatMessage()">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<style>
.chatbot-toggle {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 65px;
    height: 65px;
    background: linear-gradient(135deg, #e84393, #c2185b);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 26px;
    cursor: pointer;
    box-shadow: 0 8px 30px rgba(232, 67, 147, 0.4);
    z-index: 9998;
    transition: all 0.3s ease;
    animation: chatbotPulse 2s infinite;
}
.chatbot-toggle:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 12px 40px rgba(232, 67, 147, 0.5);
}
.chatbot-toggle.active {
    background: #1a1a2e;
    animation: none;
}
@keyframes chatbotPulse {
    0%, 100% { box-shadow: 0 8px 30px rgba(232, 67, 147, 0.4); }
    50% { box-shadow: 0 8px 40px rgba(232, 67, 147, 0.7); }
}
.chatbot-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ffc107;
    color: #1a1a2e;
    font-size: 12px;
    font-weight: 700;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: badgeBounce 1.5s infinite;
}
@keyframes badgeBounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}
.chatbot-window {
    position: fixed;
    bottom: 105px;
    right: 30px;
    width: 380px;
    max-width: calc(100vw - 40px);
    height: 550px;
    max-height: calc(100vh - 150px);
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    z-index: 9999;
    overflow: hidden;
    transform: scale(0);
    transform-origin: bottom right;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}
.chatbot-window.show {
    transform: scale(1);
    opacity: 1;
    visibility: visible;
}
.chatbot-header {
    background: linear-gradient(135deg, #1a1a2e, #2d1b36);
    color: white;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 3px solid #e84393;
}
.chatbot-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.chatbot-avatar {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, #e84393, #ff6b9d);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.chatbot-header h4 {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 2px;
}
.chatbot-status {
    font-size: 11px;
    color: #4ade80;
    display: flex;
    align-items: center;
    gap: 5px;
}
.online-dot {
    width: 8px;
    height: 8px;
    background: #4ade80;
    border-radius: 50%;
    display: inline-block;
    animation: onlinePulse 2s infinite;
}
@keyframes onlinePulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}
.chatbot-close {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.chatbot-close:hover {
    background: #e84393;
    transform: rotate(90deg);
}
.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.chatbot-messages::-webkit-scrollbar { width: 6px; }
.chatbot-messages::-webkit-scrollbar-thumb { background: #e84393; border-radius: 3px; }
.chat-message {
    display: flex;
    gap: 10px;
    animation: messageIn 0.3s ease;
}
@keyframes messageIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.user-message { flex-direction: row-reverse; }
.message-avatar {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #e84393, #ff6b9d);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    flex-shrink: 0;
}
.user-message .message-avatar { background: #1a1a2e; }
.message-content {
    max-width: 75%;
    background: white;
    padding: 12px 16px;
    border-radius: 16px;
    border-top-left-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    font-size: 14px;
    line-height: 1.5;
    color: #4a4a6a;
}
.user-message .message-content {
    background: linear-gradient(135deg, #e84393, #c2185b);
    color: white;
    border-radius: 16px;
    border-top-right-radius: 4px;
    border-top-left-radius: 16px;
}
.message-content p { margin-bottom: 8px; font-size: 14px; }
.message-content p:last-child { margin-bottom: 0; }
.message-content strong { color: #e84393; }
.user-message .message-content strong { color: #fff; }
.quick-replies {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}
.quick-replies button {
    background: #fff5f8;
    border: 1px solid #e84393;
    color: #e84393;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}
.quick-replies button:hover {
    background: #e84393;
    color: white;
    transform: translateY(-2px);
}
.chatbot-input-area {
    padding: 15px;
    background: white;
    border-top: 1px solid #e8e8f0;
    display: flex;
    gap: 10px;
}
.chatbot-input-area input {
    flex: 1;
    padding: 12px 18px;
    border: 2px solid #e8e8f0;
    border-radius: 50px;
    outline: none;
    font-size: 14px;
    font-family: inherit;
    transition: 0.3s;
}
.chatbot-input-area input:focus { border-color: #e84393; }
.chatbot-send {
    width: 46px;
    height: 46px;
    background: linear-gradient(135deg, #e84393, #c2185b);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    transition: 0.3s;
    flex-shrink: 0;
}
.chatbot-send:hover {
    transform: scale(1.1) rotate(15deg);
    box-shadow: 0 5px 15px rgba(232, 67, 147, 0.4);
}
.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 12px 16px;
    background: white;
    border-radius: 16px;
    border-top-left-radius: 4px;
    width: fit-content;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
.typing-indicator span {
    width: 8px;
    height: 8px;
    background: #e84393;
    border-radius: 50%;
    animation: typingBounce 1.4s infinite;
}
.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typingBounce {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30% { transform: translateY(-8px); opacity: 1; }
}
@media (max-width: 768px) {
    .chatbot-toggle {
        bottom: 20px;
        right: 20px;
        width: 55px;
        height: 55px;
        font-size: 22px;
    }
    .chatbot-window {
        bottom: 85px;
        right: 10px;
        left: 10px;
        width: auto;
        max-width: none;
        height: 60vh;
        max-height: calc(100vh - 120px);
    }
    .message-content { max-width: 85%; font-size: 13px; }
}
</style>

<script>
let chatbotOpen = false;
let isTyping = false;

function toggleChatbot() {
    chatbotOpen = !chatbotOpen;
    const windowEl = document.getElementById('chatbotWindow');
    const iconEl = document.getElementById('chatbotIcon');
    const toggleEl = document.getElementById('chatbotToggle');
    const badgeEl = document.getElementById('chatbotBadge');
    
    windowEl.classList.toggle('show', chatbotOpen);
    toggleEl.classList.toggle('active', chatbotOpen);
    
    if (chatbotOpen) {
        iconEl.className = 'fas fa-times';
        badgeEl.style.display = 'none';
        setTimeout(() => document.getElementById('chatbotInput').focus(), 300);
        scrollToBottom();
    } else {
        iconEl.className = 'fas fa-comments';
    }
}

function sendQuickReply(message) {
    document.getElementById('chatbotInput').value = message;
    sendChatMessage();
}

function sendChatMessage() {
    if (isTyping) return;
    
    const input = document.getElementById('chatbotInput');
    const message = input.value.trim();
    if (!message) return;
    
    addMessage(message, 'user');
    input.value = '';
    showTyping();
    
    fetch('../php_files/chatbot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: message })
    })
    .then(r => r.json())
    .then(data => {
        removeTyping();
        setTimeout(() => {
            addMessage(data.reply, 'bot');
        }, 300);
    })
    .catch(err => {
        removeTyping();
        addMessage("Désolée, problème technique. 😅 Contactez-nous au +212 723-699031.", 'bot');
    });
}

function addMessage(text, type) {
    const messagesEl = document.getElementById('chatbotMessages');
    const msgDiv = document.createElement('div');
    msgDiv.className = `chat-message ${type === 'user' ? 'user-message' : 'bot-message'}`;
    
    const avatarIcon = type === 'user' ? 'fa-user' : 'fa-robot';
    const formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
    
    msgDiv.innerHTML = `
        <div class="message-avatar"><i class="fas ${avatarIcon}"></i></div>
        <div class="message-content">${formattedText}</div>
    `;
    
    messagesEl.appendChild(msgDiv);
    scrollToBottom();
}

function showTyping() {
    isTyping = true;
    const messagesEl = document.getElementById('chatbotMessages');
    const typingDiv = document.createElement('div');
    typingDiv.className = 'chat-message bot-message';
    typingDiv.id = 'typingIndicator';
    typingDiv.innerHTML = `
        <div class="message-avatar"><i class="fas fa-robot"></i></div>
        <div class="typing-indicator">
            <span></span><span></span><span></span>
        </div>
    `;
    messagesEl.appendChild(typingDiv);
    scrollToBottom();
}

function removeTyping() {
    isTyping = false;
    const typing = document.getElementById('typingIndicator');
    if (typing) typing.remove();
}

function scrollToBottom() {
    const messagesEl = document.getElementById('chatbotMessages');
    messagesEl.scrollTop = messagesEl.scrollHeight;
}

document.addEventListener('click', function(e) {
    const chatbotWindow = document.getElementById('chatbotWindow');
    const chatbotToggle = document.getElementById('chatbotToggle');
    if (chatbotOpen && chatbotWindow && !chatbotWindow.contains(e.target) && !chatbotToggle.contains(e.target)) {
        toggleChatbot();
    }
});
</script>