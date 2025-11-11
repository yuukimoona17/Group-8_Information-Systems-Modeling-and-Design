<?php
// feedback_action.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$title = $_POST['title'];
$feedback_type = $_POST['feedback_type'];
$content = $_POST['content'];

// (Nâng cao: Bạn có thể thêm trường trip_id (mã chuyến đi) ở đây nếu muốn)

$sql = "INSERT INTO feedback (user_id, title, feedback_type, content) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $user_id, $title, $feedback_type, $content);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Your feedback has been sent successfully. Thank you!";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error: Could not send feedback.";
    $_SESSION['flash_message_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: feedback.php");
exit();
?>