<?php
// send_message_action.php: File để xử lý gửi tin nhắn mới
session_start();
if (!isset($_SESSION['user_id'])) { die("Access Denied"); }
include 'db.php';

$sender_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$content = $data['content'] ?? '';
$receiver_id = $data['receiver_id'] ?? null;

if ($receiver_id && !empty($content)) {
    $sql = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $content);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>