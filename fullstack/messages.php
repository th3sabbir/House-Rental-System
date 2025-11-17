<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$current_user_id = $_SESSION['user_id'];

// Get current user info
$user_query = "SELECT user_id, full_name, profile_image, user_type FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param('i', $current_user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$current_user = $user_result->fetch_assoc();

if (!$current_user) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - AmarThikana</title>
    <meta name="current-user-id" content="<?php echo $current_user_id; ?>">
    
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

html {
    height: 100%;
}

body {
    font-family: var(--font-family-body);
    color: var(--text-dark);
    background-color: var(--background-light);
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Chat Page Specific Styles */
.chat-container {
    flex: 1;
    padding: 0;
    margin: 0;
    height: 100%;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    flex-direction: column;
}

.chat-wrapper {
    flex: 1;
    max-width: 100%;
    margin: 0;
    padding: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.chat-layout {
    display: grid;
    grid-template-columns: 320px 1fr;
    flex: 1;
    height: 100%;
    background: var(--background-white);
    border-radius: 0;
    overflow: hidden;
    box-shadow: none;
}

/* Sidebar Styles */
.chat-sidebar {
    background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
    border-right: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.chat-sidebar-header {
    padding: 20px 20px;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    flex-shrink: 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.chat-sidebar-header h2 {
    font-size: 1.5rem;
    font-family: var(--font-family-heading);
    margin-bottom: 4px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 0.3px;
}

.chat-sidebar-header p {
    font-size: 0.85rem;
    opacity: 0.9;
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
    flex-shrink: 0;
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

.chat-header-info > div {
    display: flex;
    align-items: center;
    gap: 8px;
}

.chat-header-info h3 {
    font-size: 1.2rem;
    font-weight: 600;
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

/* Dropdown Styles */
.dropdown {
    position: relative;
}

.dropdown-toggle {
    background: transparent;
    border: none;
    color: var(--text-medium);
    font-size: 1.3rem;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.dropdown-toggle:hover {
    background: #f0f4f8;
    color: var(--secondary-color);
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    display: none;
    z-index: 1000;
    min-width: 150px;
}

.dropdown-menu button {
    display: block;
    width: 100%;
    padding: 10px 15px;
    background: none;
    border: none;
    text-align: left;
    cursor: pointer;
    font-size: 0.9rem;
    color: var(--text-dark);
    transition: background 0.3s ease;
}

.dropdown-menu button:hover {
    background: #f8f9fa;
    color: var(--secondary-color);
}

.dropdown-menu.show {
    display: block;
}

.message-area {
    flex: 1 1 0;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 16px;
    background: #fafbfc;
    min-height: 0;
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
    max-width: 75%;
    padding: 12px 16px;
    border-radius: 16px;
    line-height: 1.5;
    font-size: 0.93rem;
    position: relative;
    animation: fadeIn 0.3s ease;
    word-wrap: break-word;
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
    margin-top: 8px;
    display: block;
    text-align: right;
}

.sent {
    background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
    box-shadow: 0 2px 6px rgba(26, 188, 156, 0.25);
}

.sent .timestamp {
    color: rgba(255,255,255,0.8);
    font-size: 0.7rem;
}

.received {
    background: var(--background-white);
    color: var(--text-dark);
    align-self: flex-start;
    border: 1px solid #e0e6ed;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}

.received .timestamp {
    color: #aaa;
    font-size: 0.7rem;
}

.message-input-wrapper {
    background: var(--background-white);
    border-top: 1px solid var(--border-color);
    padding: 16px 20px;
    flex-shrink: 0;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.04);
}

.message-input-form {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8f9fa;
    border-radius: 25px;
    padding: 8px 18px;
    border: 1px solid #e0e6ed;
    transition: all 0.3s ease;
    box-shadow: none;
}

.message-input-form:focus-within {
    border-color: var(--secondary-color);
    background: var(--background-white);
    box-shadow: 0 0 0 2px rgba(26, 188, 156, 0.08);
}

.message-input-form input {
    flex-grow: 1;
    border: none;
    background: transparent;
    font-size: 0.93rem;
    padding: 10px 4px;
    outline: none;
    color: var(--text-dark);
    font-family: var(--font-family-body);
}

.message-input-form input::placeholder {
    color: #aaa;
}

.message-input-form .send-btn {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #16a085 100%);
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(26, 188, 156, 0.25);
    flex-shrink: 0;
}

.message-input-form .send-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 3px 10px rgba(26, 188, 156, 0.35);
}

.message-input-form .send-btn:active {
    transform: scale(0.95);
}

.message-input-form .send-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Empty State */
#chatEmptyState {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--text-medium);
    text-align: center;
}

#chatEmptyState i {
    font-size: 4rem;
    color: var(--border-color);
    margin-bottom: 16px;
}

#chatEmptyState h3 {
    font-size: 1.3rem;
    margin-bottom: 8px;
    color: var(--text-dark);
}

#chatEmptyState p {
    font-size: 0.95rem;
}

/* Responsive Design */
@media (max-width: 992px) {
    .chat-layout {
        grid-template-columns: 280px 1fr;
    }
}

@media (max-width: 768px) {
    .chat-wrapper {
        padding: 0;
    }
    
    .chat-layout {
        grid-template-columns: 1fr;
        position: relative;
    }
    
    .chat-sidebar {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
        transition: transform 0.3s ease;
        background: white;
        box-shadow: none;
    }
    
    .chat-sidebar.mobile-hide {
        transform: translateX(-100%);
    }
    
    .chat-header {
        padding: 12px 16px;
    }
    
    .chat-header-info {
        flex: 1;
        min-width: 0;
    }
    
    .chat-header-info img {
        width: 40px;
        height: 40px;
    }
    
    .chat-header-info h3 {
        font-size: 1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .message-area {
        padding: 16px;
        gap: 12px;
    }
    
    .message-bubble {
        max-width: 85%;
        font-size: 0.9rem;
        padding: 10px 14px;
    }
    
    .message-input-wrapper {
        padding: 12px 16px;
    }
    
    .message-input-form {
        padding: 6px 14px;
    }
    
    .message-input-form .send-btn {
        width: 38px;
        height: 38px;
        font-size: 0.9rem;
    }
    
    .mobile-menu-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        color: var(--secondary-color);
        font-size: 1.3rem;
        cursor: pointer;
        padding: 8px;
        margin-right: 8px;
        flex-shrink: 0;
    }
    
    .chat-window {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 999;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }
    
    .chat-window.mobile-show {
        transform: translateX(0);
    }
}

@media (min-width: 769px) {
    .mobile-menu-btn {
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
    flex-shrink: 0;
}

#chatEmptyState {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--text-medium);
    text-align: center;
    gap: 12px;
}

#chatEmptyState i {
    font-size: 3.5rem;
    color: #d0d0d0;
}

#chatEmptyState h3 {
    font-size: 1.2rem;
    color: var(--text-dark);
    font-weight: 600;
}

#chatEmptyState p {
    font-size: 0.9rem;
    color: var(--text-medium);
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
                    
                    <div class="chat-list" id="chatList">
                        <!-- Conversations will be loaded dynamically -->
                    </div>
                </aside>

                <main class="chat-window">
                    <div id="chatEmptyState">
                        <i class="fas fa-comments"></i>
                        <h3>Welcome to Messages</h3>
                        <p>Select a conversation to start chatting</p>
                    </div>

                    <div id="chatActive" style="display: none; height: 100%; flex-direction: column;">
                        <div class="chat-header">
                            <button class="mobile-menu-btn" onclick="toggleSidebar()">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <div class="chat-header-info">
                                <img id="chatHeaderImg" src="" alt="">
                                <div>
                                    <h3 id="chatHeaderName"></h3>
                                    <span class="status" id="chatHeaderStatus"></span>
                                </div>
                            </div>
                            <div class="chat-header-actions">
                                <div class="dropdown">
                                    <button title="More Options" class="dropdown-toggle" onclick="toggleDropdown(event)">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu" id="dropdownMenu">
                                        <button onclick="blockUser()">Block User</button>
                                        <button onclick="reportUser()">Report User</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="message-area" id="messageArea">
                            <!-- Messages will be loaded dynamically -->
                        </div>
                        
                        <div class="message-input-wrapper">
                            <form class="message-input-form" id="messageForm">
                                <input type="hidden" id="receiverIdInput">
                                <input type="text" id="messageInput" placeholder="Type a message..." autocomplete="off">
                                <button type="submit" class="send-btn" title="Send message">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/script.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentUserId = parseInt(document.querySelector('meta[name="current-user-id"]').content);
        let activeConversationUserId = null;
        let pollInterval = null;

        const chatList = document.getElementById('chatList');
        const messageArea = document.getElementById('messageArea');
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const receiverIdInput = document.getElementById('receiverIdInput');
        
        const chatEmptyState = document.getElementById('chatEmptyState');
        const chatActive = document.getElementById('chatActive');

        // Format time nicely, treating input as UTC
        function formatTime(dateTimeString) {
            // Append 'Z' to treat as UTC ISO string
            const date = new Date(dateTimeString + 'Z');
            const now = new Date();
            const diff = now - date;

            const seconds = Math.floor(diff / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);

            if (days > 1) {
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', timeZone: 'UTC' });
            } else if (days === 1) {
                return 'Yesterday';
            } else if (hours > 0) {
                return `${hours}h ago`;
            } else if (minutes > 0) {
                return `${minutes}m ago`;
            } else {
                return 'Just now';
            }
        }

        // Load all conversations
        async function loadConversations() {
            try {
                const response = await fetch('api/get_conversations.php');
                const data = await response.json();

                if (data.success && data.conversations.length > 0) {
                    chatList.innerHTML = '';
                    data.conversations.forEach(conv => {
                        const chatItem = document.createElement('div');
                        chatItem.className = 'chat-item';
                        chatItem.dataset.userId = conv.user_id;
                        chatItem.dataset.userName = conv.full_name;
                        chatItem.dataset.userImage = conv.profile_image || 'img/default-avatar.svg';

                        const lastMsg = conv.last_message.substring(0, 40) + (conv.last_message.length > 40 ? '...' : '');
                        const msgPrefix = conv.sender_id == currentUserId ? 'You: ' : '';

                        chatItem.innerHTML = `
                            <div class="chat-avatar">
                                <img src="${conv.profile_image || 'img/default-avatar.svg'}" alt="${conv.full_name}" onerror="this.src='img/default-avatar.svg'">
                            </div>
                            <div class="chat-info">
                                <h4>${conv.full_name}</h4>
                                <p>${msgPrefix}${lastMsg}</p>
                            </div>
                            <div class="chat-meta">
                                <span class="chat-time">${formatTime(conv.last_message_time)}</span>
                                ${conv.unread_count > 0 ? `<span class="unread-badge">${conv.unread_count}</span>` : ''}
                            </div>
                        `;
                        chatList.appendChild(chatItem);
                    });
                } else if (!data.success) {
                    console.error('Failed to load conversations:', data.message);
                    chatList.innerHTML = '<p style="text-align: center; padding: 20px; color: var(--text-medium);">Error loading conversations</p>';
                } else {
                    chatList.innerHTML = '<p style="text-align: center; padding: 20px; color: var(--text-medium);">No conversations yet. Start a new message!</p>';
                }
            } catch (error) {
                console.error('Error fetching conversations:', error);
                chatList.innerHTML = '<p style="text-align: center; padding: 20px; color: #c00;">Connection error. Please refresh the page.</p>';
            }
        }

        // Load messages for a specific user
        async function loadMessages(userId, userName, userImage) {
            activeConversationUserId = userId;
            chatEmptyState.style.display = 'none';
            chatActive.style.display = 'flex';
            
            // Mobile: show chat window and hide sidebar
            if (window.innerWidth <= 768) {
                document.querySelector('.chat-sidebar').classList.add('mobile-hide');
                document.querySelector('.chat-window').classList.add('mobile-show');
            }

            // Update header
            document.getElementById('chatHeaderName').textContent = userName;
            document.getElementById('chatHeaderImg').src = userImage;
            document.getElementById('chatHeaderImg').onerror = function() { this.src = 'img/default-avatar.svg'; };
            receiverIdInput.value = userId;

            // Mark active in sidebar
            document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
            document.querySelector(`.chat-item[data-user-id='${userId}']`)?.classList.add('active');

            try {
                const response = await fetch(`api/get_messages.php?user_id=${userId}`);
                const data = await response.json();

                messageArea.innerHTML = '';
                if (data.success) {
                    if (data.messages.length === 0) {
                        messageArea.innerHTML = '<p style="text-align: center; padding: 20px; color: var(--text-medium);">No messages yet. Start the conversation!</p>';
                    } else {
                        data.messages.forEach(msg => {
                            appendMessage(msg);
                        });
                    }
                    scrollToBottom();
                } else {
                    console.error('Failed to load messages:', data.message);
                    messageArea.innerHTML = '<p style="text-align: center; padding: 20px; color: #c00;">Error loading messages</p>';
                }
            } catch (error) {
                console.error('Error fetching messages:', error);
                messageArea.innerHTML = '<p style="text-align: center; padding: 20px; color: #c00;">Connection error</p>';
            }
        }

        function appendMessage(msg) {
            const messageType = msg.sender_id == currentUserId ? 'sent' : 'received';
            const messageBubble = document.createElement('div');
            messageBubble.className = `message-bubble ${messageType}`;
            messageBubble.innerHTML = `
                ${msg.message.replace(/</g, '&lt;').replace(/>/g, '&gt;')}
                <span class="timestamp">${formatTime(msg.timestamp)}</span>
            `;
            messageArea.appendChild(messageBubble);
        }

        function scrollToBottom() {
            messageArea.scrollTop = messageArea.scrollHeight;
        }

        // Send message
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const messageText = messageInput.value.trim();
            const receiverId = parseInt(receiverIdInput.value);

            if (!messageText || !receiverId) {
                alert('Please select a recipient and enter a message');
                return;
            }

            // Disable send button
            const sendBtn = messageForm.querySelector('.send-btn');
            sendBtn.disabled = true;
            const originalHTML = sendBtn.innerHTML;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            const messageData = {
                receiver_id: receiverId,
                message: messageText
            };

            try {
                const response = await fetch('api/send_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(messageData)
                });
                const data = await response.json();

                if (data.success) {
                    appendMessage(data.message);
                    scrollToBottom();
                    messageInput.value = '';
                    // Refresh conversation list
                    loadConversations();
                } else {
                    alert('Failed to send message: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Error sending message. Please check your connection.');
            } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = originalHTML;
            }
        });

        // Event delegation for chat list items
        chatList.addEventListener('click', function(e) {
            const chatItem = e.target.closest('.chat-item');
            if (chatItem) {
                const userId = parseInt(chatItem.dataset.userId);
                const userName = chatItem.dataset.userName;
                const userImage = chatItem.dataset.userImage;
                loadMessages(userId, userName, userImage);
            }
        });

        // Initial load
        loadConversations();
        
        // Poll for new messages every 5 seconds
        pollInterval = setInterval(() => {
            loadConversations();
            if (activeConversationUserId) {
                const chatItem = document.querySelector(`.chat-item[data-user-id='${activeConversationUserId}']`);
                if (chatItem) {
                    loadMessages(activeConversationUserId, chatItem.dataset.userName, chatItem.dataset.userImage);
                }
            }
        }, 5000);

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (pollInterval) {
                clearInterval(pollInterval);
            }
        });
    });

    // Mobile sidebar toggle
    function toggleSidebar() {
        const sidebar = document.querySelector('.chat-sidebar');
        const chatWindow = document.querySelector('.chat-window');
        
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('mobile-hide');
            chatWindow.classList.toggle('mobile-show');
        }
    }

    // Dropdown toggle
    function toggleDropdown(event) {
        event.stopPropagation();
        const dropdown = event.target.closest('.dropdown');
        const menu = dropdown.querySelector('.dropdown-menu');
        menu.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('show'));
        }
    });

    // Block user function
    function blockUser() {
        if (confirm('Are you sure you want to block this user? You will no longer receive messages from them.')) {
            // TODO: Implement block functionality
            alert('Block functionality not yet implemented.');
        }
    }

    // Report user function
    function reportUser() {
        if (confirm('Are you sure you want to report this user?')) {
            // TODO: Implement report functionality
            alert('Report functionality not yet implemented.');
        }
    }
    </script>
</body>
</html>




