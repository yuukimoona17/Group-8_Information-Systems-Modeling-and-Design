<?php
// update_user_role_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

$user_id_to_update = $_POST['user_id'];
$new_role = $_POST['role'];

// Đảm bảo vai trò hợp lệ
if ($new_role !== 'user' && $new_role !== 'admin' && $new_role !== 'driver') {
    die("Invalid role specified.");
}

// --- CODE MỚI THÊM VÀO ---
// BƯỚC 1: Lấy vai trò CŨ của user
$sql_get_old_role = "SELECT role FROM users WHERE user_id = ?";
$stmt_get = $conn->prepare($sql_get_old_role);
$stmt_get->bind_param("i", $user_id_to_update);
$stmt_get->execute();
$old_role = $stmt_get->get_result()->fetch_assoc()['role'];
$stmt_get->close();

// BƯỚC 2: Kiểm tra logic
// Nếu vai trò CŨ là 'driver' VÀ vai trò MỚI KHÁC 'driver'
if ($old_role === 'driver' && $new_role !== 'driver') {
    // Xóa hồ sơ staff liên quan
    $sql_delete_staff = "DELETE FROM staff WHERE user_id = ?";
    $stmt_staff = $conn->prepare($sql_delete_staff);
    $stmt_staff->bind_param("i", $user_id_to_update);
    $stmt_staff->execute();
    $stmt_staff->close();
    
    $_SESSION['flash_message'] = "User role updated. Staff profile was automatically deleted.";
    $_SESSION['flash_message_type'] = "warning"; // Dùng màu vàng để admin biết
}
// --- KẾT THÚC CODE MỚI ---


// BƯỚC 3: Cập nhật vai trò mới (như cũ)
$sql = "UPDATE users SET role = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_role, $user_id_to_update);

if ($stmt->execute()) {
    // Chỉ set message thành công nếu chưa có message warning ở trên
    if (!isset($_SESSION['flash_message'])) {
        $_SESSION['flash_message'] = "User role updated successfully.";
        $_SESSION['flash_message_type'] = "success";
    }
} else {
    $_SESSION['flash_message'] = "Error updating user role.";
    $_SESSION['flash_message_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: admin_users.php");
exit();
?>