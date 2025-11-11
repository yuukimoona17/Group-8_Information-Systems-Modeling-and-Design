<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$route_id = $_POST['route_id'];
$vehicle_id = $_POST['vehicle_id'];
$driver_id = $_POST['driver_id'];
$departure_time = $_POST['departure_time'];

if (empty($route_id) || empty($vehicle_id) || empty($driver_id) || empty($departure_time)) {
     $_SESSION['flash_message'] = "Error: All fields are required.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: admin_schedules.php");
    exit();
}

$sql = "INSERT INTO schedules (route_id, vehicle_id, driver_id, departure_time) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siis", $route_id, $vehicle_id, $driver_id, $departure_time);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Successfully added new schedule.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Database error: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}
$stmt->close();
$conn->close();
header("Location: admin_schedules.php");
exit();
?>
