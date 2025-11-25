<?php
// chat.php - New Pro Interface
include 'db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = "Please login to access chat.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$current_role = $_SESSION['role'];
$is_admin = ($current_role === 'admin');

// 2. Load Header tương ứng
if ($is_admin) include 'admin_header.php';
elseif ($current_role === 'driver') include 'driver_header.php';
else include 'user_header.php';

// 3. Lấy danh sách User để chat (Chỉ dành cho Admin)
$users_list = [];
if ($is_admin) {
    // Lấy những user đã từng nhắn tin hoặc tất cả user (ở đây lấy tất cả user trừ admin)
    $sql_users = "SELECT user_id, username, role, full_name, profile_picture_path 
                  FROM users 
                  WHERE user_id != ? 
                  ORDER BY role ASC, username ASC";
    $stmt = $conn->prepare($sql_users);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $users_list[] = $row;
    }
}
?>

<div class="chat-wrapper">
    <div class="chat-app-container">
        
        <?php if ($is_admin): ?>
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <h5 class="text-white mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i>Contacts</h5>
            </div>
            <div class="user-list">
                <?php foreach ($users_list as $u): ?>
                    <div class="user-item" onclick="selectUser(<?php echo $u['user_id']; ?>, '<?php echo htmlspecialchars($u['full_name'] ?: $u['username']); ?>')">
                        <img src="<?php echo htmlspecialchars($u['profile_picture_path'] ?? 'uploads/default_avatar.png'); ?>" class="user-avatar-small object-fit-cover">
                        <div>
                            <div class="fw-bold text-white" style="font-size: 0.9rem;">
                                <?php echo htmlspecialchars($u['full_name'] ?: $u['username']); ?>
                            </div>
                            <div class="small text-white-50 text-uppercase" style="font-size: 0.7rem;">
                                <?php echo $u['role']; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="chat-main">
            <div class="chat-main-header">
                <div class="d-flex align-items-center">
                    <div class="user-avatar-small bg-primary" id="chat-header-icon">
                        <i class="bi bi-chat-dots-fill"></i>
                    </div>
                    <div>
                        <h5 class="text-white mb-0 fw-bold" id="chat-with-name">
                            <?php echo $is_admin ? 'Select a user to chat' : 'Support Team (Admin)'; ?>
                        </h5>
                        <small class="text-success" id="chat-status">
                            <?php echo $is_admin ? '' : 'Online'; ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="chat-messages-area" id="msg-area">
                <div class="text-center text-white-50 mt-5">
                    <i class="bi bi-chat-square-text fs-1 opacity-50"></i>
                    <p class="mt-2">Start a conversation</p>
                </div>
            </div>

            <div class="chat-input-area">
                <div class="input-group-glass">
                    <input type="text" id="msg-input" class="chat-input-field" placeholder="Type your message..." autocomplete="off" <?php echo $is_admin ? 'disabled' : ''; ?>>
                    <button class="btn-send" id="btn-send" <?php echo $is_admin ? 'disabled' : ''; ?>>
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<?php 
if ($is_admin) include 'admin_footer.php'; 
elseif ($current_role === 'driver') include 'driver_footer.php';
else include 'user_footer.php'; 
?>

<script>
    const currentUserId = <?php echo $current_user_id; ?>;
    const isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;
    
    let partnerId = null;       // ID người mình đang chat cùng
    let chatInterval = null;    // Biến lưu vòng lặp cập nhật tin nhắn
    
    const msgArea = document.getElementById('msg-area');
    const msgInput = document.getElementById('msg-input');
    const btnSend = document.getElementById('btn-send');
    const chatNameDisplay = document.getElementById('chat-with-name');
    const chatStatus = document.getElementById('chat-status');

    // Nếu là User thường: Tự động lấy ID Admin để chat luôn
    if (!isAdmin) {
        fetchAdminId();
    }

    // Hàm lấy ID Admin (cho User)
    async function fetchAdminId() {
        try {
            const res = await fetch('get_admin_id.php');
            const data = await res.json();
            if (data.admin_id) {
                partnerId = data.admin_id;
                startChat(); // Bắt đầu chat luôn
            } else {
                msgArea.innerHTML = '<div class="text-center text-danger mt-5">Admin not found.</div>';
            }
        } catch (e) { console.error(e); }
    }

    // Hàm chọn User (cho Admin) - Được gọi khi bấm vào Sidebar
    function selectUser(userId, userName) {
        partnerId = userId;
        chatNameDisplay.innerText = userName;
        chatStatus.innerText = 'Chatting...';
        
        // Kích hoạt input
        msgInput.disabled = false;
        btnSend.disabled = false;
        msgInput.focus();

        // Highlight user đang chọn
        document.querySelectorAll('.user-item').forEach(el => el.classList.remove('active'));
        event.currentTarget.classList.add('active');

        // Bắt đầu load tin nhắn
        startChat();
    }

    // Bắt đầu vòng lặp load tin nhắn
    function startChat() {
        if (chatInterval) clearInterval(chatInterval); // Xóa vòng lặp cũ nếu có
        loadMessages(true); // Load ngay lập tức và cuộn xuống đáy
        chatInterval = setInterval(() => loadMessages(false), 3000); // Cập nhật mỗi 3s
    }

    // Hàm tải tin nhắn từ Server
    async function loadMessages(forceScroll = false) {
        if (!partnerId) return;

        try {
            const res = await fetch(`fetch_messages.php?partner_id=${partnerId}`);
            const data = await res.json();
            
            // Kiểm tra xem người dùng có đang cuộn lên xem tin cũ không
            // Nếu đang ở đáy (cách đáy < 50px) thì mới tự cuộn xuống khi có tin mới
            const isAtBottom = (msgArea.scrollHeight - msgArea.scrollTop <= msgArea.clientHeight + 50);

            if (!data.messages || data.messages.length === 0) {
                msgArea.innerHTML = '<div class="text-center text-white-50 mt-5">No messages yet. Say "Hi"!</div>';
                return;
            }

            // Render tin nhắn HTML
            let html = '';
            data.messages.forEach(msg => {
                // Logic phân biệt Mình (Me) và Họ (Other)
                // Nếu tôi là Admin: Tin của Admin gửi là Me.
                // Nếu tôi là User: Tin của User (tôi) gửi là Me.
                const isMe = (msg.sender_id == currentUserId) || (isAdmin && msg.sender_role == 'admin' && msg.sender_id == currentUserId);
                
                const alignClass = isMe ? 'msg-me' : 'msg-other';
                const senderName = isMe ? 'Me' : msg.sender_name;
                
                // Format thời gian
                const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                html += `
                <div class="msg-row ${alignClass}">
                    <div class="msg-bubble">
                        <div class="msg-sender-name">${senderName}</div>
                        <div>${msg.content}</div>
                        <div class="msg-time">${time}</div>
                    </div>
                </div>`;
            });

            msgArea.innerHTML = html;

            // Tự động cuộn xuống nếu đang ở đáy hoặc forceScroll=true
            if (isAtBottom || forceScroll) {
                msgArea.scrollTop = msgArea.scrollHeight;
            }

        } catch (e) { console.error("Chat error:", e); }
    }

    // Hàm gửi tin nhắn
    async function sendMessage() {
        const content = msgInput.value.trim();
        if (!content || !partnerId) return;

        try {
            await fetch('send_message_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    content: content,
                    receiver_id: partnerId
                })
            });
            
            msgInput.value = ''; // Xóa ô nhập
            loadMessages(true);  // Load lại và cuộn xuống ngay
        } catch (e) { alert('Error sending message'); }
    }

    // Sự kiện bấm nút Gửi
    btnSend.addEventListener('click', sendMessage);
    
    // Sự kiện ấn Enter
    msgInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Chặn xuống dòng
            sendMessage();
        }
    });

</script>