<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - AmarThikana</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
:root {
    --primary-color: #2c3e50;
    --secondary-color: #1abc9c;
    --background-light: #f8f9fa;
    --background-white: #ffffff;
    --text-dark: #34495e;
    --text-medium: #7f8c8d;
    --border-color: #e0e6ed;
    --font-family-body: 'Lato', sans-serif;
    --font-family-heading: 'Poppins', sans-serif;
    --shadow-soft: 0 2px 8px rgba(0,0,0,0.08);
    --shadow-medium: 0 4px 15px rgba(0,0,0,0.1);
    --border-radius: 12px;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: var(--font-family-body);
    color: var(--text-dark);
    background-color: var(--background-light);
    overflow-x: hidden;
}

/* Chat Page Specific Styles */
.chat-container {
    padding-top: 0;
    min-height: 100vh;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.chat-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px;
}

.chat-layout {
    display: grid;
    grid-template-columns: 380px 1fr;
    height: calc(100vh - 48px);
    background: var(--background-white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

/* Sidebar Styles */
.chat-sidebar {
    background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
    border-right: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
}

.chat-sidebar-header {
    padding: 28px 24px;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.chat-sidebar-header h2 {
    font-size: 1.75rem;
    font-family: var(--font-family-heading);
    margin-bottom: 6px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 0.3px;
}

.chat-sidebar-header p {
    font-size: 0.95rem;
    opacity: 0.95;
    margin: 0;
    color: #ecf0f1;
    font-weight: 400;
}

.chat-list {
    flex-grow: 1;
    overflow-y: auto;
    padding: 8px 0;
}

.chat-list::-webkit-scrollbar {
    width: 6px;
}

.chat-list::-webkit-scrollbar-track {
    background: transparent;
}

.chat-list::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 10px;
}

.chat-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    position: relative;
}

.chat-item:hover {
    background-color: #f0f4f8;
}

.chat-item.active {
    background-color: #e8f8f5;
    border-left-color: var(--secondary-color);
}

.chat-item.active::after {
    content: '';
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: var(--secondary-color);
}

.chat-avatar {
    position: relative;
    flex-shrink: 0;
}

.chat-avatar img {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border-color);
    transition: all 0.3s ease;
}

.chat-item.active .chat-avatar img {
    border-color: var(--secondary-color);
}

.chat-avatar .status-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    background: #10b981;
    border: 2px solid white;
    border-radius: 50%;
}

.chat-avatar .status-indicator.offline {
    background: #94a3b8;
}

.chat-info {
    flex-grow: 1;
    min-width: 0;
}

.chat-info h4 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 4px;
    font-family: var(--font-family-heading);
    color: var(--text-dark);
}

.chat-info p {
    font-size: 0.88rem;
    color: var(--text-medium);
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
}

.chat-time {
    font-size: 0.8rem;
    color: var(--text-medium);
}

.unread-badge {
    background: var(--secondary-color);
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 6px;
}

/* Chat Window Styles */
.chat-window {
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
    height: 100%;
}

.chat-header {
    padding: 20px 28px;
    background: var(--background-white);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.chat-header-info {
    display: flex;
    align-items: center;
    gap: 14px;
}

.chat-header-info img {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    border: 2px solid var(--secondary-color);
}

.chat-header-info h3 {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 2px;
}

.chat-header-info .status {
    font-size: 0.85rem;
    color: #10b981;
    display: flex;
    align-items: center;
    gap: 6px;
}

.chat-header-info .status::before {
    content: '';
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
}

.chat-header-info .status.offline {
    color: #94a3b8;
}

.chat-header-info .status.offline::before {
    background: #94a3b8;
}

.chat-header-actions {
    display: flex;
    gap: 12px;
}

.chat-header-actions button {
    background: transparent;
    border: none;
    color: var(--text-medium);
    font-size: 1.3rem;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.chat-header-actions button:hover {
    background: #f0f4f8;
    color: var(--secondary-color);
}

.message-area {
    flex-grow: 1;
    padding: 28px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 20px;
    height: 0;
}

.message-area::-webkit-scrollbar {
    width: 8px;
}

.message-area::-webkit-scrollbar-track {
    background: transparent;
}

.message-area::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

.message-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.message-bubble {
    max-width: 70%;
    padding: 14px 18px;
    border-radius: 18px;
    line-height: 1.6;
    font-size: 0.95rem;
    position: relative;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-bubble .timestamp {
    font-size: 0.75rem;
    color: var(--text-medium);
    margin-bottom: 6px;
    display: block;
    font-weight: 500;
}

.sent {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #16a085 100%);
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
    box-shadow: 0 2px 8px rgba(26, 188, 156, 0.3);
}

.sent .timestamp {
    color: rgba(255,255,255,0.85);
}

.received {
    background: var(--background-white);
    color: var(--text-dark);
    align-self: flex-start;
    border: 1px solid var(--border-color);
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

.message-input-wrapper {
    background: var(--background-white);
    border-top: 1px solid var(--border-color);
    padding: 20px 24px;
    position: sticky;
    bottom: 0;
    z-index: 10;
}

.message-input-form {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #ffffff;
    border-radius: 30px;
    padding: 8px 20px;
    border: 2px solid var(--border-color);
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.message-input-form:focus-within {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.1), 0 2px 8px rgba(0,0,0,0.08);
}

.message-input-form input {
    flex-grow: 1;
    border: none;
    background: transparent;
    font-size: 0.95rem;
    padding: 10px 4px;
    outline: none;
    color: var(--text-dark);
    font-family: var(--font-family-body);
}

.message-input-form input::placeholder {
    color: var(--text-medium);
}

.message-input-form .send-btn {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #16a085 100%);
    color: white;
    border: none;
    border-radius: 50%;
    width: 46px;
    height: 46px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(26, 188, 156, 0.3);
    flex-shrink: 0;
}

.message-input-form .send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(26, 188, 156, 0.4);
}

.message-input-form .send-btn:active {
    transform: scale(0.95);
}

/* Empty State */
.chat-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--text-medium);
}

.chat-empty i {
    font-size: 4rem;
    color: var(--border-color);
    margin-bottom: 16px;
}

.chat-empty h3 {
    font-size: 1.3rem;
    margin-bottom: 8px;
    color: var(--text-dark);
}

.chat-empty p {
    font-size: 0.95rem;
}

/* Responsive Design */
@media (max-width: 992px) {
    .chat-layout {
        grid-template-columns: 320px 1fr;
    }
}

@media (max-width: 768px) {
    .chat-wrapper {
        padding: 12px;
    }
    
    .chat-layout {
        grid-template-columns: 1fr;
        height: calc(100vh - 100px);
    }
    
    .chat-sidebar {
        display: none;
    }
    
    .chat-sidebar.mobile-show {
        display: flex;
        position: fixed;
        top: 80px;
        left: 0;
        width: 100%;
        height: calc(100vh - 80px);
        z-index: 1000;
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(-100%);
        }
        to {
            transform: translateX(0);
        }
    }
    
    .chat-header {
        padding: 16px 20px;
    }
    
    .chat-header-info h3 {
        font-size: 1.05rem;
    }
    
    .message-area {
        padding: 20px 16px;
    }
    
    .message-bubble {
        max-width: 85%;
        font-size: 0.9rem;
    }
    
    .message-input-wrapper {
        padding: 16px;
    }
    
    .message-input-form {
        padding: 6px 16px;
    }
    
    .message-input-form .send-btn {
        width: 42px;
        height: 42px;
    }
    
    .mobile-back-btn {
        display: flex;
        background: transparent;
        border: none;
        color: var(--secondary-color);
        font-size: 1.3rem;
        cursor: pointer;
        padding: 8px;
        margin-right: 8px;
    }
    
    .chat-sidebar-header h2 {
        font-size: 1.5rem;
    }
}

@media (min-width: 769px) {
    .mobile-back-btn {
        display: none;
    }
}

/* Typing Indicator */
.typing-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: var(--background-white);
    border-radius: 18px;
    border-bottom-left-radius: 4px;
    width: fit-content;
    border: 1px solid var(--border-color);
}

.typing-indicator span {
    width: 8px;
    height: 8px;
    background: var(--text-medium);
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
        opacity: 0.6;
    }
    30% {
        transform: translateY(-8px);
        opacity: 1;
    }
}
.main-header {
    background-color: #2c3e50 !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06) !important;
}
    </style>
</head>
<body>
    
    <div class="chat-container">
        <div class="chat-wrapper">
            <div class="chat-layout">
                <aside class="chat-sidebar">
                    <div class="chat-sidebar-header">
                        <h2>Messages</h2>
                        <p>Your conversations</p>
                    </div>
                    
                    <div class="chat-list">
                        <div class="chat-item active" data-user-id="clara">
                            <div class="chat-avatar">
                                <img src="https://images.pexels.com/photos/1036623/pexels-photo-1036623.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Clara Bennett">
                                <span class="status-indicator"></span>
                            </div>
                            <div class="chat-info">
                                <h4>Clara Bennett</h4>
                                <p>Tomorrow at 3 PM would be great!</p>
                            </div>
                            <div class="chat-meta">
                                <span class="chat-time">2:55 PM</span>
                                <span class="unread-badge">3</span>
                            </div>
                        </div>
                        
                        <div class="chat-item" data-user-id="owen">
                            <div class="chat-avatar">
                                <img src="https://images.pexels.com/photos/91227/pexels-photo-91227.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Owen Carter">
                                <span class="status-indicator"></span>
                            </div>
                            <div class="chat-info">
                                <h4>Owen Carter</h4>
                                <p>Can I see it tomorrow?</p>
                            </div>
                            <div class="chat-meta">
                                <span class="chat-time">Yesterday</span>
                            </div>
                        </div>
                        
                        <div class="chat-item" data-user-id="emma">
                            <div class="chat-avatar">
                                <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?auto=compress&cs=tinysrgb&w=400" alt="Emma Wilson">
                                <span class="status-indicator offline"></span>
                            </div>
                            <div class="chat-info">
                                <h4>Emma Wilson</h4>
                                <p>Thank you for the information</p>
                            </div>
                            <div class="chat-meta">
                                <span class="chat-time">2 days ago</span>
                            </div>
                        </div>
                        
                        <div class="chat-item" data-user-id="james">
                            <div class="chat-avatar">
                                <img src="https://images.pexels.com/photos/1065084/pexels-photo-1065084.jpeg?auto=compress&cs=tinysrgb&w=400" alt="James Miller">
                                <span class="status-indicator"></span>
                            </div>
                            <div class="chat-info">
                                <h4>James Miller</h4>
                                <p>Is parking included?</p>
                            </div>
                            <div class="chat-meta">
                                <span class="chat-time">3 days ago</span>
                                <span class="unread-badge">1</span>
                            </div>
                        </div>
                    </div>
                </aside>

                <main class="chat-window">
                    <div class="chat-header">
                        <button class="mobile-back-btn" onclick="toggleSidebar()">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="chat-header-info">
                            <img id="chatHeaderImg" src="https://images.pexels.com/photos/1036623/pexels-photo-1036623.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Clara Bennett">
                            <div>
                                <h3 id="chatHeaderName">Clara Bennett</h3>
                                <span class="status" id="chatHeaderStatus">Active now</span>
                            </div>
                        </div>
                        <div class="chat-header-actions">
                            <button title="Voice Call"><i class="fas fa-phone"></i></button>
                            <button title="More Options"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>
                    
                    <div class="message-area" id="messageArea">
                        <!-- Messages will be loaded dynamically -->
                    </div>
                    
                    <div class="message-input-wrapper">
                        <form class="message-input-form" onsubmit="sendMessage(event)">
                            <input type="text" id="messageInput" placeholder="Type a message..." autocomplete="off">
                            <button type="submit" class="send-btn" title="Send message">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/script.js"></script>
    
    <script>
        // All conversations data
        const conversationsData = {
            clara: {
                name: 'Clara Bennett',
                image: 'https://images.pexels.com/photos/1036623/pexels-photo-1036623.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                status: 'Active now',
                isOnline: true,
                messages: [
                    { type: 'received', text: 'Hi there, I\'m interested in renting your apartment. Is it available from July 15th to July 20th?', time: 'July 10, 2:30 PM' },
                    { type: 'sent', text: 'Hello Clara, thank you for your interest! Yes, the apartment is available during those dates.', time: 'July 10, 2:45 PM' },
                    { type: 'sent', text: 'Would you like to schedule a viewing?', time: 'July 10, 2:46 PM' },
                    { type: 'received', text: 'That would be perfect! What times are available?', time: 'July 10, 2:50 PM' },
                    { type: 'sent', text: 'I\'m available tomorrow between 2-5 PM or Friday morning. Which works better for you?', time: 'July 10, 2:52 PM' },
                    { type: 'received', text: 'Tomorrow at 3 PM would be great! See you then 😊', time: 'July 10, 2:55 PM' }
                ]
            },
            owen: {
                name: 'Owen Carter',
                image: 'https://images.pexels.com/photos/91227/pexels-photo-91227.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                status: 'Active now',
                isOnline: true,
                messages: [
                    { type: 'received', text: 'Hello! I saw your property listing. Is it still available?', time: 'July 9, 10:15 AM' },
                    { type: 'sent', text: 'Hi Owen! Yes, it\'s still available. When would you like to view it?', time: 'July 9, 10:30 AM' },
                    { type: 'received', text: 'Can I see it tomorrow?', time: 'July 9, 10:35 AM' },
                    { type: 'sent', text: 'Sure! What time works best for you?', time: 'July 9, 10:40 AM' },
                    { type: 'received', text: 'How about 4 PM?', time: 'July 9, 10:42 AM' },
                    { type: 'sent', text: 'Perfect! I\'ll send you the address. See you tomorrow at 4 PM.', time: 'July 9, 10:45 AM' }
                ]
            },
            emma: {
                name: 'Emma Wilson',
                image: 'https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?auto=compress&cs=tinysrgb&w=400',
                status: 'Last seen 2 days ago',
                isOnline: false,
                messages: [
                    { type: 'received', text: 'Hi, I\'m looking for a 2-bedroom apartment. Do you have any available?', time: 'July 8, 3:20 PM' },
                    { type: 'sent', text: 'Hello Emma! Yes, I have a beautiful 2-bedroom apartment available. Would you like more details?', time: 'July 8, 3:25 PM' },
                    { type: 'received', text: 'Yes please! What\'s the monthly rent?', time: 'July 8, 3:27 PM' },
                    { type: 'sent', text: 'The rent is $1,200 per month, utilities included. It has a modern kitchen, parking space, and is close to public transport.', time: 'July 8, 3:30 PM' },
                    { type: 'received', text: 'Thank you for the information. I\'ll discuss with my partner and get back to you.', time: 'July 8, 3:35 PM' },
                    { type: 'sent', text: 'No problem! Feel free to reach out if you have any questions.', time: 'July 8, 3:36 PM' }
                ]
            },
            james: {
                name: 'James Miller',
                image: 'https://images.pexels.com/photos/1065084/pexels-photo-1065084.jpeg?auto=compress&cs=tinysrgb&w=400',
                status: 'Active now',
                isOnline: true,
                messages: [
                    { type: 'received', text: 'Good afternoon! I\'m interested in your studio apartment listing.', time: 'July 7, 1:10 PM' },
                    { type: 'sent', text: 'Hi James! Great to hear from you. The studio is perfect for a single person or couple.', time: 'July 7, 1:15 PM' },
                    { type: 'received', text: 'Is parking included?', time: 'July 7, 1:18 PM' },
                    { type: 'sent', text: 'Yes, one parking spot is included with the rent. There\'s also street parking available.', time: 'July 7, 1:20 PM' },
                    { type: 'received', text: 'That\'s great! Can I schedule a viewing for this weekend?', time: 'July 7, 1:22 PM' },
                    { type: 'sent', text: 'Absolutely! Saturday or Sunday? And what time works best for you?', time: 'July 7, 1:25 PM' },
                    { type: 'received', text: 'Saturday afternoon around 2 PM would be ideal.', time: 'July 7, 1:27 PM' }
                ]
            }
        };

        let currentUserId = 'clara';

        // Function to load messages for a specific user
        function loadMessages(userId) {
            currentUserId = userId;
            const conversation = conversationsData[userId];
            const messageArea = document.getElementById('messageArea');
            
            // Clear existing messages
            messageArea.innerHTML = '';
            
            // Update header
            document.getElementById('chatHeaderName').textContent = conversation.name;
            document.getElementById('chatHeaderImg').src = conversation.image;
            document.getElementById('chatHeaderImg').alt = conversation.name;
            
            const statusElement = document.getElementById('chatHeaderStatus');
            statusElement.textContent = conversation.status;
            if (conversation.isOnline) {
                statusElement.classList.remove('offline');
            } else {
                statusElement.classList.add('offline');
            }
            
            // Load messages
            conversation.messages.forEach(msg => {
                const messageGroup = document.createElement('div');
                messageGroup.className = 'message-group';
                
                const messageBubble = document.createElement('div');
                messageBubble.className = `message-bubble ${msg.type}`;
                messageBubble.innerHTML = `
                    <span class="timestamp">${msg.time}</span>
                    ${msg.text}
                `;
                
                messageGroup.appendChild(messageBubble);
                messageArea.appendChild(messageGroup);
            });
            
            // Scroll to bottom
            setTimeout(() => {
                messageArea.scrollTop = messageArea.scrollHeight;
            }, 100);
        }

        // Send message function
        function sendMessage(event) {
            event.preventDefault();
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (message) {
                const messageArea = document.getElementById('messageArea');
                const now = new Date();
                const timeString = now.toLocaleString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    hour: 'numeric', 
                    minute: '2-digit', 
                    hour12: true 
                });
                
                const messageGroup = document.createElement('div');
                messageGroup.className = 'message-group';
                
                const messageBubble = document.createElement('div');
                messageBubble.className = 'message-bubble sent';
                messageBubble.innerHTML = `
                    <span class="timestamp">${timeString}</span>
                    ${message}
                `;
                
                messageGroup.appendChild(messageBubble);
                messageArea.appendChild(messageGroup);
                
                // Save to conversations data
                conversationsData[currentUserId].messages.push({
                    type: 'sent',
                    text: message,
                    time: timeString
                });
                
                // Scroll to bottom
                messageArea.scrollTop = messageArea.scrollHeight;
                
                // Clear input
                input.value = '';
            }
        }
        
        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.chat-sidebar');
            sidebar.classList.toggle('mobile-show');
        }
        
        // Chat item click handler
        document.querySelectorAll('.chat-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all items
                document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
                
                // Add active class to clicked item
                this.classList.add('active');
                
                // Remove unread badge
                const badge = this.querySelector('.unread-badge');
                if (badge) {
                    badge.remove();
                }
                
                // Load messages for selected user
                const userId = this.getAttribute('data-user-id');
                loadMessages(userId);
                
                // On mobile, hide sidebar after selection
                if (window.innerWidth <= 768) {
                    const sidebar = document.querySelector('.chat-sidebar');
                    sidebar.classList.remove('mobile-show');
                }
            });
        });
        
        // Load initial conversation on page load
        window.addEventListener('load', function() {
            loadMessages('clara');
        });
    </script>
</body>
</html>




