<?php
// update_user_role_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$user_id_to_update = $_POST['user_id'];
$new_role = $_POST['role'];

// Đảm bảo vai trò hợp lệ (THÊM 'driver')
if ($new_role !== 'user' && $new_role !== 'admin' && $new_role !== 'driver') {
    die("Invalid role specified.");
}

$sql = "UPDATE users SET role = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_role, $user_id_to_update);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "User role updated successfully.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error updating user role.";
    $_SESSION['flash_message_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: admin_users.php");
exit();
?>