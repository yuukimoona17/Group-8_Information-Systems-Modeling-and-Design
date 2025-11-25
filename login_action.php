<?php
session_start();
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT user_id, username, password, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Chấp nhận cả pass đã mã hóa VÀ pass thường (để fix lỗi dữ liệu cũ)
    if (password_verify($password, $user['password']) || $password === $user['password']) {
        
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Phân quyền chuyển hướng
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($user['role'] === 'driver') {
            header("Location: driver_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }
}

$_SESSION['flash_message'] = "Invalid username or password.";
$_SESSION['flash_message_type'] = "danger";
header("Location: login.php");
exit();
?>