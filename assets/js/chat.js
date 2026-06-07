// assets/js/chat.js

document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    if (!chatForm || !chatInput || !chatMessages) return;

    // Scroll chat to bottom initially
    scrollToBottom();

    // Form submit listener
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const messageText = chatInput.value.trim();
        if (!messageText) return;
        
        // Append user bubble
        appendMessage(messageText, 'sent');
        chatInput.value = '';
        
        // Show typing indicator
        const typingId = showTypingIndicator();
        
        // Send request to API
        const formData = new FormData();
        formData.append('message', messageText);
        
        fetch('api/chat.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            removeTypingIndicator(typingId);
            
            if (data.success) {
                appendMessage(data.reply, 'received');
            } else {
                appendMessage("🤖 Navré, une erreur est survenue lors de l'analyse du message.", 'received');
            }
        })
        .catch(err => {
            removeTypingIndicator(typingId);
            appendMessage("🤖 Une erreur réseau a empêché de contacter le serveur HydroBot.", 'received');
        });
    });
});

// Helper function to scroll to bottom of chat
function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

// Function to append a message bubble
function appendMessage(htmlContent, type) {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;
    
    const bubbleClass = type === 'sent' ? 'bubble-sent' : 'bubble-received';
    const alignClass = type === 'sent' ? 'justify-content-end' : 'justify-content-start';
    
    const bubbleHtml = `
        <div class="d-flex ${alignClass} mb-3">
            <div class="chat-bubble ${bubbleClass}">
                ${htmlContent}
            </div>
        </div>
    `;
    
    chatMessages.insertAdjacentHTML('beforeend', bubbleHtml);
    scrollToBottom();
}

// Function to show a pulsing typing indicator
function showTypingIndicator() {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return null;
    
    const typingId = 'typing-' + Date.now();
    const indicatorHtml = `
        <div class="d-flex justify-content-start mb-3" id="${typingId}">
            <div class="chat-bubble bubble-received py-2 px-3 d-flex align-items-center">
                <span class="text-muted small me-2">HydroBot réfléchit</span>
                <div class="spinner-grow spinner-grow-sm text-info" role="status" style="width: 6px; height: 6px; animation-duration: 0.8s;"></div>
                <div class="spinner-grow spinner-grow-sm text-info mx-1" role="status" style="width: 6px; height: 6px; animation-duration: 0.8s; animation-delay: 0.2s;"></div>
                <div class="spinner-grow spinner-grow-sm text-info" role="status" style="width: 6px; height: 6px; animation-duration: 0.8s; animation-delay: 0.4s;"></div>
            </div>
        </div>
    `;
    
    chatMessages.insertAdjacentHTML('beforeend', indicatorHtml);
    scrollToBottom();
    return typingId;
}

// Function to remove typing indicator
function removeTypingIndicator(id) {
    if (!id) return;
    const indicator = document.getElementById(id);
    if (indicator) {
        indicator.remove();
    }
}

// Function triggered when user clicks a technical computation template button
function loadTemplateQuery(queryText) {
    const chatInput = document.getElementById('chatInput');
    const chatForm = document.getElementById('chatForm');
    
    if (chatInput && chatForm) {
        chatInput.value = queryText;
        chatInput.focus();
        // Submit the form automatically after a brief delay
        setTimeout(() => {
            chatForm.dispatchEvent(new Event('submit'));
        }, 150);
    }
}
