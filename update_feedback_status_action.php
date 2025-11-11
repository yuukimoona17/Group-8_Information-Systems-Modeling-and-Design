<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$feedback_id = $_GET['id'];
$status = $_GET['status'];

// Đảm bảo status hợp lệ (CHỈ CHO PHÉP 'processing')
if ($status !== 'processing') {
    die("Invalid status action.");
}

$sql = "UPDATE feedback SET status = ? WHERE feedback_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $feedback_id);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Feedback status updated to 'Processing'.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error updating status: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: admin_feedback.php"); // Quay về trang list
exit();
?>