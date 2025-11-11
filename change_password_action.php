<?php
// change_password_action.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];
$confirm_new_password = $_POST['confirm_new_password'];
$user_id = $_SESSION['user_id'];

// 1. Kiểm tra mật khẩu mới có khớp không
if ($new_password !== $confirm_new_password) {
    $_SESSION['flash_message'] = "New passwords do not match.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: profile.php");
    exit();
}

// 2. Lấy mật khẩu cũ đã mã hóa từ CSDL
$sql_get_pass = "SELECT password FROM users WHERE user_id = ?";
$stmt_get_pass = $conn->prepare($sql_get_pass);
$stmt_get_pass->bind_param("i", $user_id);
$stmt_get_pass->execute();
$result = $stmt_get_pass->get_result();
$user = $result->fetch_assoc();
$hashed_password_from_db = $user['password'];
$stmt_get_pass->close();

// 3. So sánh mật khẩu cũ người dùng nhập với mật khẩu trong CSDL
if (password_verify($old_password, $hashed_password_from_db)) {
    // Nếu đúng, mã hóa mật khẩu mới và cập nhật
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql_update_pass = "UPDATE users SET password = ? WHERE user_id = ?";
    $stmt_update_pass = $conn->prepare($sql_update_pass);
    $stmt_update_pass->bind_param("si", $new_hashed_password, $user_id);
    
    if ($stmt_update_pass->execute()) {
        $_SESSION['flash_message'] = "Password changed successfully.";
        $_SESSION['flash_message_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Error updating password.";
        $_SESSION['flash_message_type'] = "danger";
    }
    $stmt_update_pass->close();
} else {
    // Nếu sai mật khẩu cũ
    $_SESSION['flash_message'] = "Incorrect old password.";
    $_SESSION['flash_message_type'] = "danger";
}

$conn->close();
header("Location: profile.php");
exit();
?>