<div style="max-width: 900px; height: 800px; margin: 0 auto; border: 2px solid #333; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0,0,0,0.2);">
    
    <iframe 
        src="pdfreader/web/viewer.html?file=../../uploads/ksayLabo.pdf" 
        width="100%" 
        height="100%" 
        style="border: none; border-radius: 8px;">
    </iframe>

</div>
<button id="ai-chat-btn" style="position: fixed; bottom: 20px; right: 20px; background-color: #0056b3; color: white; border: none; border-radius: 50px; padding: 15px 20px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0px 4px 10px rgba(0,0,0,0.3); z-index: 1000;">
    💬 Ask the AI Guide
</button>

<div id="ai-chat-window" style="display: none; position: fixed; bottom: 80px; right: 20px; width: 300px; height: 400px; background-color: white; border: 2px solid #ccc; border-radius: 10px; box-shadow: 0px 5px 15px rgba(0,0,0,0.2); flex-direction: column; z-index: 1000;">
    
    <div style="background-color: #0056b3; color: white; padding: 10px; border-top-left-radius: 8px; border-top-right-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
        <strong style="margin: 0;">🏛️ Museo AI Guide</strong>
        <button id="close-chat" style="background: none; border: none; color: white; font-size: 16px; cursor: pointer;">✖</button>
    </div>

    <div id="chat-messages" style="flex: 1; padding: 10px; overflow-y: auto; background-color: #f9f9f9; font-family: sans-serif; font-size: 14px;">
        <div style="margin-bottom: 10px; color: #333;"><strong>AI:</strong> Hello! I am your virtual guide to the Museo de Labo. What part of the book would you like me to explain?</div>
    </div>

    <div style="padding: 10px; border-top: 1px solid #ddd; display: flex;">
        <input type="text" id="user-input" placeholder="Type a question..." style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        <button id="send-btn" style="margin-left: 5px; padding: 8px 12px; background-color: #0056b3; color: white; border: none; border-radius: 4px; cursor: pointer;">Send</button>
    </div>
</div>

<script>
    const chatBtn = document.getElementById('ai-chat-btn');
    const chatWindow = document.getElementById('ai-chat-window');
    const closeBtn = document.getElementById('close-chat');
    const sendBtn = document.getElementById('send-btn');
    const userInput = document.getElementById('user-input');
    const messagesArea = document.getElementById('chat-messages');

    // Open/Close Chat
    chatBtn.addEventListener('click', () => { chatWindow.style.display = 'flex'; chatBtn.style.display = 'none'; });
    closeBtn.addEventListener('click', () => { chatWindow.style.display = 'none'; chatBtn.style.display = 'block'; });

    // Function to send message to the AI Brain
    async function sendMessage() {
        const text = userInput.value.trim();
        if(!text) return; // Don't send empty messages

        // 1. Show User Message and clear box
        messagesArea.innerHTML += `<div style="margin-bottom: 10px; color: #0056b3; text-align: right;"><strong>You:</strong> ${text}</div>`;
        userInput.value = ''; 
        messagesArea.scrollTop = messagesArea.scrollHeight; 

        // 2. Show Typing Indicator
        const typingId = "typing-" + Date.now();
        messagesArea.innerHTML += `<div id="${typingId}" style="margin-bottom: 10px; color: #888;"><em>AI is thinking...</em></div>`;
        messagesArea.scrollTop = messagesArea.scrollHeight;

        try {
            // 3. Talk to PHP Brain
            const response = await fetch('ai_brain.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `message=${encodeURIComponent(text)}`
            });
            
            const reply = await response.text();

            // 4. Clean up Google's markdown (bolding) to HTML
            const formattedReply = reply.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            document.getElementById(typingId).remove();
            messagesArea.innerHTML += `<div style="margin-bottom: 10px; color: #333;"><strong>AI:</strong> ${formattedReply}</div>`;
            messagesArea.scrollTop = messagesArea.scrollHeight;

        } catch (error) {
            document.getElementById(typingId).remove();
            messagesArea.innerHTML += `<div style="margin-bottom: 10px; color: red;"><strong>System:</strong> Connection error! Check internet.</div>`;
        }
    }

    // Connect the buttons to the function
    sendBtn.addEventListener('click', sendMessage);
    userInput.addEventListener('keypress', (e) => { if(e.key === 'Enter') sendMessage(); });
</script>