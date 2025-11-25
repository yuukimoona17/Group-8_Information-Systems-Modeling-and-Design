<?php
// register_action.php - Phiên bản nâng cấp Avatar
session_start();
include 'db.php';

// Lấy dữ liệu
$username = $_POST['username'];
$password = $_POST['password'];
$full_name = $_POST['full_name']; 
$email = $_POST['email']; 
$phone_number = $_POST['phone_number']; 

// Xử lý upload ảnh đại diện (Avatar)
$profile_picture_path = 'uploads/default_avatar.png'; // Mặc định
$upload_dir = 'uploads/'; // Thư mục đã tạo ở Bước 2

// Kiểm tra xem file có được upload và không bị lỗi
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
    // Tạo tên file duy nhất để tránh bị ghi đè
    $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid('avatar_') . '.' . $file_extension;
    $target_file_path = $upload_dir . $unique_filename;

    // Di chuyển file từ tmp vào thư mục uploads
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file_path)) {
        $profile_picture_path = $target_file_path; // Cập nhật đường dẫn nếu thành công
    } else {
        // Nếu di chuyển file lỗi, vẫn dùng ảnh mặc định nhưng báo lỗi (hoặc không)
        // Bỏ qua lỗi, dùng ảnh mặc định
    }
}

// Kiểm tra username (giữ nguyên)
$sql_check = "SELECT user_id FROM users WHERE username = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $_SESSION['flash_message'] = "Error: Username already exists.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: register.php");
    exit();
}
$stmt_check->close();

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = 'user'; 

// Cập nhật câu SQL INSERT (thêm 4 cột mới)
$sql_insert = "INSERT INTO users (username, full_name, email, phone_number, profile_picture_path, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
// Cập nhật bind_param (sssssss)
$stmt_insert->bind_param("sssssss", $username, $full_name, $email, $phone_number, $profile_picture_path, $hashed_password, $role);

if ($stmt_insert->execute()) {
    $_SESSION['flash_message'] = "Registration successful! You can now login.";
    $_SESSION['flash_message_type'] = "success";
    header("Location: login.php");
    exit();
} else {
    $_SESSION['flash_message'] = "Error: Could not register. " . $stmt_insert->error;
    $_SESSION['flash_message_type'] = "danger";
    header("Location: register.php");
    exit();
}
?>