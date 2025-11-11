<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

include 'db.php';

$license_plate = $_POST['license_plate'];
$model = $_POST['model'];
$capacity = $_POST['capacity'];
$status = $_POST['status'];

$sql_check = "SELECT vehicle_id FROM vehicles WHERE license_plate = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $license_plate);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $_SESSION['flash_message'] = "Error: License plate '" . htmlspecialchars($license_plate) . "' already exists.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: admin_vehicles.php");
    exit();
}
$stmt_check->close();

$sql_insert = "INSERT INTO vehicles (license_plate, model, capacity, status) VALUES (?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("ssis", $license_plate, $model, $capacity, $status);

if ($stmt_insert->execute()) {
    $_SESSION['flash_message'] = "Successfully added new vehicle: " . htmlspecialchars($license_plate);
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Database error: " . $stmt_insert->error;
    $_SESSION['flash_message_type'] = "danger";
}

$stmt_insert->close();
$conn->close();

header("Location: admin_vehicles.php");
exit();
?>