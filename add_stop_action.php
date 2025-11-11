<?php
// add_stop_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$stop_name = $_POST['stop_name'];
$street = $_POST['street'];

// Dùng prepared statements để thêm dữ liệu an toàn
$sql = "INSERT INTO bus_stops (stop_name, street) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $stop_name, $street);

if ($stmt->execute()) {
    header("Location: admin_bus_stops.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>