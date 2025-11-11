<?php
// delete_stop_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$stop_id = $_GET['id'];

// Xóa các lộ trình liên quan trước để không bị lỗi CSDL
$sql_delete_itineraries = "DELETE FROM itineraries WHERE stop_id = ?";
$stmt_itineraries = $conn->prepare($sql_delete_itineraries);
$stmt_itineraries->bind_param("i", $stop_id);
$stmt_itineraries->execute();
$stmt_itineraries->close();

// Sau đó mới xóa điểm dừng
$sql_delete_stop = "DELETE FROM bus_stops WHERE stop_id = ?";
$stmt_stop = $conn->prepare($sql_delete_stop);
$stmt_stop->bind_param("i", $stop_id);

if ($stmt_stop->execute()) {
    header("Location: admin_bus_stops.php");
    exit();
} else {
    echo "Error deleting record: " . $stmt_stop->error;
}

$stmt_stop->close();
$conn->close();
?>