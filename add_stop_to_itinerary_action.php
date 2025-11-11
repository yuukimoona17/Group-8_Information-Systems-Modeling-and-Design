<?php
// add_stop_to_itinerary_action.php - Phiên bản tự động sắp xếp lại

session_start();
// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

include 'db.php';

// Lấy dữ liệu từ form
$route_id = $_POST['route_id'];
$stop_id = $_POST['stop_id'];
$stop_order = $_POST['stop_order'];
$direction = $_POST['direction'];

// Ghi chú: Trong một hệ thống lớn, 2 câu lệnh SQL dưới đây sẽ được bọc trong một "transaction"
// để đảm bảo nếu một trong hai lệnh bị lỗi, lệnh kia sẽ được hoàn tác.

// --- BƯỚC 1: "DỌN CHỖ" ---
// Tăng stop_order của tất cả các điểm dừng đứng sau vị trí cần chèn lên 1.
$sql_update = "UPDATE itineraries 
                SET stop_order = stop_order + 1 
                WHERE route_id = ? AND direction = ? AND stop_order >= ?";

$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("sii", $route_id, $direction, $stop_order);
$stmt_update->execute();
$stmt_update->close();


// --- BƯỚC 2: "CHÈN VÀO" ---
// Thêm điểm dừng mới vào vị trí đã được dọn trống.
$sql_insert = "INSERT INTO itineraries (route_id, stop_id, stop_order, direction) 
                VALUES (?, ?, ?, ?)";

$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("siii", $route_id, $stop_id, $stop_order, $direction);

// Thực thi và chuyển hướng
if ($stmt_insert->execute()) {
    header("Location: manage_itinerary_detail.php?route_id=" . urlencode($route_id));
    exit();
} else {
    echo "Lỗi khi thêm điểm dừng vào lộ trình: " . $stmt_insert->error;
}

$stmt_insert->close();
$conn->close();
?>