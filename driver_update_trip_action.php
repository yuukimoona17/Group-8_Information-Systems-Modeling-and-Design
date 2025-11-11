<?php
// driver_update_trip_action.php
session_start();
// Bảo vệ: Chỉ cho 'driver'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    die("Access Denied.");
}

include 'db.php';

$trip_id = $_GET['trip_id'];
$new_status = $_GET['status'];
$driver_staff_id = $_SESSION['staff_id']; // Lấy từ session

// Đảm bảo status hợp lệ
$allowed_statuses = ['running', 'completed', 'cancelled'];
if (empty($trip_id) || empty($new_status) || !in_array($new_status, $allowed_statuses)) {
    $_SESSION['flash_message'] = "Invalid action.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: driver_dashboard.php");
    exit();
}

// --- SỬA CODE TỪ ĐÂY ---
// Chuẩn bị câu SQL cơ bản
$sql = "UPDATE schedules SET status = ? ";
$params = [$new_status]; // Mảng chứa các tham số cho bind_param
$types = "s";           // Kiểu dữ liệu ban đầu là 's' (string cho status)

// Nếu trạng thái mới là 'completed', thêm cập nhật completion_time
if ($new_status === 'completed') {
    $sql .= ", actual_completion_time = NOW() "; // NOW() lấy giờ hiện tại của server CSDL
}

// Thêm điều kiện WHERE (chỉ cập nhật chuyến của tài xế này)
$sql .= " WHERE trip_id = ? AND driver_id = ?";
$params[] = $trip_id;      // Thêm trip_id vào mảng params
$params[] = $driver_staff_id; // Thêm driver_id vào mảng params
$types .= "ii";             // Thêm 'i' (integer) cho trip_id và driver_id

// Chuẩn bị và thực thi câu lệnh
$stmt = $conn->prepare($sql);
// Sử dụng ...$params để unpack mảng thành các tham số riêng lẻ
$stmt->bind_param($types, ...$params); 
// --- KẾT THÚC SỬA CODE ---

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['flash_message'] = "Trip status updated successfully.";
        $_SESSION['flash_message_type'] = "success";
    } else {
        // Lỗi này xảy ra nếu trip_id không tồn tại, hoặc nó không thuộc về tài xế này
        $_SESSION['flash_message'] = "Error: You do not have permission to update this trip or trip not found.";
        $_SESSION['flash_message_type'] = "danger";
    }
} else {
    $_SESSION['flash_message'] = "Database error: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: driver_dashboard.php");
exit();
?>