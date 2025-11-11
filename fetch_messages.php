<?php
// fetch_messages.php: File để lấy lịch sử tin nhắn
session_start();
if (!isset($_SESSION['user_id'])) { die("Access Denied"); }
include 'db.php';

$current_user_id = $_SESSION['user_id'];
$chat_partner_id = $_GET['partner_id'] ?? null;

$messages = [];
if ($chat_partner_id) {
    $sql = "SELECT m.sender_id, m.content, u.username AS sender_name, m.created_at 
            FROM messages m
            JOIN users u ON m.sender_id = u.user_id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $current_user_id, $chat_partner_id, $chat_partner_id, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode(['messages' => $messages, 'current_user_id' => $current_user_id]);
?>