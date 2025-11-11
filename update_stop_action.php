<?php
// update_stop_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$stop_id = $_POST['stop_id'];
$stop_name = $_POST['stop_name'];
$street = $_POST['street'];

$sql = "UPDATE bus_stops SET stop_name = ?, street = ? WHERE stop_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $stop_name, $street, $stop_id);

if ($stmt->execute()) {
    header("Location: admin_bus_stops.php");
    exit();
} else {
    echo "Error updating record: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>