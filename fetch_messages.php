<?php
// fetch_messages.php - Phiên bản Chat "User vs Team Admin"
session_start();
if (!isset($_SESSION['user_id'])) { die("Access Denied"); }
include 'db.php';

$current_user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$messages = [];

if ($role === 'admin') {
    // --- LOGIC CHO ADMIN ---
    // Admin đang xem tin nhắn với một User cụ thể (partner_id)
    $chat_partner_id = $_GET['partner_id'] ?? null;
    
    if ($chat_partner_id) {
        // Lấy tin nhắn giữa (User này) VÀ (Bất kỳ ai là Admin)
        // Logic: 
        // 1. Tin từ User này gửi đến bất kỳ Admin nào
        // 2. Tin từ bất kỳ Admin nào gửi đến User này
        $sql = "SELECT 
                    m.sender_id, 
                    m.content, 
                    m.created_at,
                    u_sender.username AS sender_name,
                    u_sender.role AS sender_role
                FROM messages m
                JOIN users u_sender ON m.sender_id = u_sender.user_id
                JOIN users u_receiver ON m.receiver_id = u_receiver.user_id
                WHERE 
                    (m.sender_id = ? AND u_receiver.role = 'admin') -- User gửi cho Admin
                    OR 
                    (u_sender.role = 'admin' AND m.receiver_id = ?) -- Admin gửi cho User
                ORDER BY m.created_at ASC";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $chat_partner_id, $chat_partner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
} else {
    // --- LOGIC CHO USER / DRIVER ---
    // User muốn xem tin nhắn của mình với Admin
    // Logic: Lấy tin của mình gửi cho bất kỳ Admin nào HOẶC bất kỳ Admin nào gửi cho mình
    $sql = "SELECT 
                m.sender_id, 
                m.content, 
                m.created_at,
                u_sender.username AS sender_name,
                u_sender.role AS sender_role
            FROM messages m
            JOIN users u_sender ON m.sender_id = u_sender.user_id
            JOIN users u_receiver ON m.receiver_id = u_receiver.user_id
            WHERE 
                (m.sender_id = ? AND u_receiver.role = 'admin') -- Mình gửi cho Admin
                OR 
                (u_sender.role = 'admin' AND m.receiver_id = ?) -- Admin gửi cho Mình
            ORDER BY m.created_at ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $current_user_id, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

header('Content-Type: application/json');
// Trả về thêm thông tin role của người đang xem để JS xử lý màu sắc
echo json_encode([
    'messages' => $messages, 
    'current_user_id' => $current_user_id,
    'current_user_role' => $role
]);
?>