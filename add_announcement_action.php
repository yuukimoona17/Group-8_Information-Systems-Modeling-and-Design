<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$title = $_POST['title'];
$content = $_POST['content'];

$sql = "INSERT INTO announcements (title, content) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $title, $content);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Announcement posted successfully.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}
$stmt->close();
$conn->close();
header("Location: admin_announcements.php");
exit();
?>