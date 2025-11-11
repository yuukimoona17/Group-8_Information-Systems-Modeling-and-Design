<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$vehicle_id = $_POST['vehicle_id'];
$license_plate = $_POST['license_plate'];
$model = $_POST['model'];
$capacity = $_POST['capacity'];
$status = $_POST['status'];

$sql = "UPDATE vehicles SET license_plate = ?, model = ?, capacity = ?, status = ? WHERE vehicle_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisi", $license_plate, $model, $capacity, $status, $vehicle_id);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Vehicle '" . htmlspecialchars($license_plate) . "' updated successfully.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error updating vehicle: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: admin_vehicles.php");
exit();
?>