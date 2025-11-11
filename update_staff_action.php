<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$staff_id = $_POST['staff_id'];
$full_name = $_POST['full_name'];
$license_number = $_POST['license_number'];
$contact_info = $_POST['contact_info'];
$staff_role = $_POST['staff_role'];

$sql = "UPDATE staff SET full_name = ?, license_number = ?, contact_info = ?, staff_role = ? WHERE staff_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $full_name, $license_number, $contact_info, $staff_role, $staff_id);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Staff '" . htmlspecialchars($full_name) . "' updated successfully.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error updating staff: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: admin_staff.php");
exit();
?>