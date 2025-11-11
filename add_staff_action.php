<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

include 'db.php';

$user_id = $_POST['user_id'];
$full_name = $_POST['full_name'];
$license_number = $_POST['license_number'];
$staff_role = 'driver'; // Tạm thời mặc định là driver

if (empty($user_id) || empty($full_name)) {
    $_SESSION['flash_message'] = "Error: User and Full Name are required.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: admin_staff.php");
    exit();
}

// user_id trong CSDL là unique nên không cần check
$sql_insert = "INSERT INTO staff (user_id, full_name, staff_role, license_number) VALUES (?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("isss", $user_id, $full_name, $staff_role, $license_number);

if ($stmt_insert->execute()) {
    $_SESSION['flash_message'] = "Successfully added new staff: " . htmlspecialchars($full_name);
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Database error: " . $stmt_insert->error;
    $_SESSION['flash_message_type'] = "danger";
}

$stmt_insert->close();
$conn->close();

header("Location: admin_staff.php");
exit();
?>