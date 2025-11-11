<?php
// admin_resolve_feedback_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$feedback_id = $_POST['feedback_id'];
$admin_response = $_POST['admin_response'];

if (empty($admin_response)) {
    $_SESSION['flash_message'] = "Error: Response content cannot be empty.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: admin_view_feedback.php?id=" . $feedback_id);
    exit();
}

$sql = "UPDATE feedback SET status = 'resolved', admin_response = ? WHERE feedback_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $admin_response, $feedback_id);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Feedback resolved and response sent.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error updating feedback: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: admin_feedback.php"); // Quay về trang list
exit();
?>