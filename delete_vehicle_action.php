<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$vehicle_id = $_GET['id'];

// Cảnh báo: Nên xóa các schedules liên quan trước
// Tạm thời chỉ xóa vehicle
$sql_delete_schedules = "DELETE FROM schedules WHERE vehicle_id = ?";
$stmt_sched = $conn->prepare($sql_delete_schedules);
$stmt_sched->bind_param("i", $vehicle_id);
$stmt_sched->execute();
$stmt_sched->close();


$sql = "DELETE FROM vehicles WHERE vehicle_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vehicle_id);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Vehicle deleted successfully (and related schedules).";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error deleting vehicle: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}
$stmt->close();
$conn->close();
header("Location: admin_vehicles.php");
exit();
?>