<?php
// remove_stop_from_itinerary_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$itinerary_id = $_GET['id'];
$route_id = $_GET['route_id']; // Lấy route_id để chuyển hướng lại

$sql = "DELETE FROM itineraries WHERE itinerary_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $itinerary_id);

if ($stmt->execute()) {
    header("Location: manage_itinerary_detail.php?route_id=" . urlencode($route_id));
    exit();
} else {
    echo "Error deleting record: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>