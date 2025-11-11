<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$staff_id = $_GET['id'];

// Cảnh báo: Nên xóa các schedules liên quan trước
$sql_delete_schedules = "DELETE FROM schedules WHERE driver_id = ?";
$stmt_sched = $conn->prepare($sql_delete_schedules);
$stmt_sched->bind_param("i", $staff_id);
$stmt_sched->execute();
$stmt_sched->close();


$sql = "DELETE FROM staff WHERE staff_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Staff deleted successfully (and related schedules).";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error deleting staff: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}
$stmt->close();
$conn->close();
header("Location: admin_staff.php");
exit();
?>