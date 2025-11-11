<?php
// Thêm session_start() ở đầu
session_start();
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Kiểm tra username
$sql_check = "SELECT user_id FROM users WHERE username = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // --- SỬA CHỖ NÀY ---
    // die("Error: Username already exists. <a href='register.php'>Go back</a>");
    // Thay bằng:
    $_SESSION['flash_message'] = "Error: Username already exists. Please choose another.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: register.php");
    exit();
    // --- KẾT THÚC SỬA ---
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = 'user'; // Mặc định là user

$sql_insert = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("sss", $username, $hashed_password, $role);

if ($stmt_insert->execute()) {
    // --- SỬA CHỖ NÀY ---
    // echo "Registration successful! <a href='login.php'>You can now login.</a>";
    // Thay bằng: (chuyển về login.php để báo thành công)
    $_SESSION['flash_message'] = "Registration successful! You can now login.";
    $_SESSION['flash_message_type'] = "success";
    header("Location: login.php");
    exit();
    // --- KẾT THÚC SỬA ---
} else {
    // --- SỬA CHỖ NÀY ---
    // echo "Error: " . $stmt_insert->error;
    // Thay bằng:
    $_SESSION['flash_message'] = "Error: Could not register. " . $stmt_insert->error;
    $_SESSION['flash_message_type'] = "danger";
    header("Location: register.php");
    exit();
    // --- KẾT THÚC SỬA ---
}
?>