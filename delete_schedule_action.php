<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$trip_id = $_GET['id'];

$sql = "DELETE FROM schedules WHERE trip_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trip_id);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Schedule deleted successfully.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error deleting schedule: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}
$stmt->close();
$conn->close();
header("Location: admin_schedules.php");
exit();
?>