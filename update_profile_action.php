<?php
// update_profile_action.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$user_id = $_SESSION['user_id'];

// Lấy dữ liệu POST
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone_number = $_POST['phone_number'];

// Chuẩn bị câu lệnh SQL
$sql = "UPDATE users SET full_name = ?, email = ?, phone_number = ?";
$types = "sss"; // 3 strings
$params = [$full_name, $email, $phone_number];

// Xử lý upload avatar (NẾU CÓ)
$upload_dir = 'uploads/';
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
    
    // (Nên xóa avatar cũ ở đây - Tạm thời bỏ qua để đơn giản)

    // Tạo tên file mới
    $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid('avatar_') . '.' . $file_extension;
    $target_file_path = $upload_dir . $unique_filename;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file_path)) {
        // Nếu upload thành công, thêm vào câu SQL
        $sql .= ", profile_picture_path = ?";
        $types .= "s"; // Thêm 1 string
        $params[] = $target_file_path; // Thêm vào mảng params
    }
}

// Hoàn thành câu SQL
$sql .= " WHERE user_id = ?";
$types .= "i"; // Thêm 1 integer (user_id)
$params[] = $user_id;

// Thực thi
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params); // Dùng ... để unpack mảng params

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Profile updated successfully.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error updating profile: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: profile.php");
exit();
?>