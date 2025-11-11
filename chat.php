<?php
// chat.php - Trang nhắn tin chuyên nghiệp
include 'db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// Lấy danh sách user nếu là admin
$users_list = [];
if ($is_admin) {
    include 'admin_header.php';
    $sql_users = "SELECT user_id, username FROM users WHERE role = 'user' ORDER BY username ASC";
    $result_users = $conn->query($sql_users);
    if ($result_users) {
        while ($row = $result_users->fetch_assoc()) { $users_list[] = $row; }
    }
} else {
    include 'user_header.php';
}
?>

<style>
    .chat-wrapper { display: flex; height: 75vh; gap: 20px; }
    .user-list-panel { flex: 0 0 300px; overflow-y: auto; }
    .chat-container { flex-grow: 1; display: flex; flex-direction: column; }
    .chat-body { flex-grow: 1; overflow-y: auto; padding: 15px; border-radius: 8px; background-color: <?php echo $is_admin ? '#fff' : '#212529'; ?>; border: 1px solid #dee2e6;}
    .message-row { display: flex; align-items: flex-end; gap: 10px; margin-bottom: 15px; }
    .message-row.sent { justify-content: flex-end; }
    .message-row.received { justify-content: flex-start; }
    .message-bubble { max-width: 70%; padding: 10px 15px; border-radius: 18px; word-wrap: break-word; }
    .message-row.sent .message-bubble { background-color: #0d6efd; color: white; }
    .message-row.received .message-bubble { background-color: <?php echo $is_admin ? '#e9ecef' : '#495057'; ?>; color: <?php echo $is_admin ? '#212529' : '#e0e0e0'; ?>; }
    .sender-name { font-size: 0.75rem; font-weight: bold; color: #6c757d; margin-bottom: 4px; }
    .timestamp { font-size: 0.7rem; color: #adb5bd; margin-top: 5px; text-align: right; }
</style>

<div class="card <?php echo $is_admin ? '' : 'bg-dark text-white'; ?>">
    <div class="card-header"><h3><i class="bi bi-chat-dots-fill me-2"></i>Chat System</h3></div>
    <div class="card-body">
        <div class="chat-wrapper">
            <?php if ($is_admin): ?>
                <div class="user-list-panel">
                    <div class="list-group">
                        <div class="list-group-item list-group-item-secondary fw-bold">Select a User to Chat</div>
                        <?php foreach ($users_list as $user): ?>
                            <a href="#" class="list-group-item list-group-item-action" data-user-id="<?php echo $user['user_id']; ?>" data-username="<?php echo htmlspecialchars($user['username']); ?>">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="chat-container">
                <h4 id="chat-with-user" class="mb-3"><?php echo $is_admin ? 'Select a conversation' : 'Chat with Admin'; ?></h4>
                <div class="chat-body" id="chat-body">
                    <div class="text-center text-muted p-3"><em><?php echo $is_admin ? 'Please select a user to start chatting.' : 'Loading messages...'; ?></em></div>
                </div>
                <div class="chat-footer mt-3 input-group">
                    <input type="text" id="chat-input" class="form-control" placeholder="Type a message..." disabled>
                    <button class="btn btn-primary" id="send-chat-btn" disabled>Send</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if ($is_admin) { include 'admin_footer.php'; } 
else { include 'user_footer.php'; }
?>

<script>
const chatBody = document.getElementById('chat-body');
const chatInput = document.getElementById('chat-input');
const sendChatBtn = document.getElementById('send-chat-btn');
const chatWithUser = document.getElementById('chat-with-user');
let currentPartnerId = null; 
const isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;
let chatInterval;

async function loadMessages() {
    if (!currentPartnerId) return;
    try {
        const response = await fetch(`fetch_messages.php?partner_id=${currentPartnerId}`);
        const data = await response.json();
        chatBody.innerHTML = '';
        if (!data.messages || data.messages.length === 0) { chatBody.innerHTML = '<div class="text-center text-muted p-3"><em>No messages yet.</em></div>'; return; }
        data.messages.forEach(msg => {
            const row = document.createElement('div'); row.className = 'message-row';
            const bubble = document.createElement('div'); bubble.className = 'message-bubble';
            const sender = document.createElement('div'); sender.className = 'sender-name'; sender.textContent = msg.sender_name;
            const content = document.createElement('div'); content.textContent = msg.content;
            const time = document.createElement('div'); time.className = 'timestamp'; time.textContent = new Date(msg.created_at.replace(' ', 'T')).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            bubble.append(sender, content, time);
            row.appendChild(bubble);
            row.className += msg.sender_id == data.current_user_id ? ' sent' : ' received';
            chatBody.appendChild(row);
        });
        chatBody.scrollTop = chatBody.scrollHeight;
    } catch (error) { chatBody.innerHTML = '<div class="text-center text-danger p-3"><em>Error loading messages.</em></div>'; }
}
async function sendMessage() {
    const content = chatInput.value.trim();
    if (content === '' || !currentPartnerId) return;
    chatInput.value = '';
    try {
        await fetch('send_message_action.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ content: content, receiver_id: currentPartnerId })
        });
        loadMessages();
    } catch (error) { alert('Error sending message.'); }
}
if (isAdmin) {
    document.querySelectorAll('.user-list-panel a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            currentPartnerId = this.getAttribute('data-user-id');
            chatWithUser.textContent = `Chat with ${this.getAttribute('data-username')}`;
            chatInput.disabled = false; sendChatBtn.disabled = false;
            document.querySelectorAll('.user-list-panel a').forEach(a => a.classList.remove('active'));
            this.classList.add('active');
            if (chatInterval) clearInterval(chatInterval);
            loadMessages();
            chatInterval = setInterval(loadMessages, 5000);
        });
    });
} else {
    (async function() {
        const response = await fetch('get_admin_id.php');
        const data = await response.json();
        if (data.admin_id) {
            currentPartnerId = data.admin_id;
            chatInput.disabled = false; sendChatBtn.disabled = false;
            loadMessages();
            if (chatInterval) clearInterval(chatInterval);
            chatInterval = setInterval(loadMessages, 5000);
        } else {
            chatBody.innerHTML = '<div class="text-center text-danger p-3"><em>Admin not found.</em></div>';
        }
    })();
}
sendChatBtn.addEventListener('click', sendMessage);
chatInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') { e.preventDefault(); sendMessage(); }});
</script>