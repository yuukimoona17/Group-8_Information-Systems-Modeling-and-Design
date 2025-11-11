<?php
// delete_user_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$user_id_to_delete = $_GET['id'];

// Không cho phép admin tự xóa chính mình
if ($user_id_to_delete == $_SESSION['user_id']) {
    $_SESSION['flash_message'] = "Error: You cannot delete your own account.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: admin_users.php");
    exit();
}

// --- CODE MỚI THÊM VÀO ---
// BƯỚC 1: Xóa hồ sơ staff liên quan (nếu có)
// (Chúng ta cũng nên xóa các schedules, feedback, messages... của họ)

// Xóa staff profile
$sql_delete_staff = "DELETE FROM staff WHERE user_id = ?";
$stmt_staff = $conn->prepare($sql_delete_staff);
$stmt_staff->bind_param("i", $user_id_to_delete);
$stmt_staff->execute();
$stmt_staff->close();

// Xóa feedback của họ
$sql_delete_feedback = "DELETE FROM feedback WHERE user_id = ?";
$stmt_feedback = $conn->prepare($sql_delete_feedback);
$stmt_feedback->bind_param("i", $user_id_to_delete);
$stmt_feedback->execute();
$stmt_feedback->close();

// Xóa messages của họ (cả gửi và nhận)
$sql_delete_msg = "DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?";
$stmt_msg = $conn->prepare($sql_delete_msg);
$stmt_msg->bind_param("ii", $user_id_to_delete, $user_id_to_delete);
$stmt_msg->execute();
$stmt_msg->close();
// --- KẾT THÚC CODE MỚI ---


// BƯỚC 2: Xóa tài khoản user
$sql = "DELETE FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id_to_delete);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "User and all related data deleted successfully.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error deleting user.";
    $_SESSION['flash_message_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: admin_users.php");
exit();
?>