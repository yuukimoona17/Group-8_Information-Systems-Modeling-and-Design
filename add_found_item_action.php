<?php
// add_found_item_action.php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'driver')) {
    die("Access Denied.");
}
include 'db.php';

$item_name = $_POST['item_name'];
$description = $_POST['description'];
$route_id = !empty($_POST['route_id']) ? $_POST['route_id'] : NULL;
$vehicle_id = !empty($_POST['vehicle_id']) ? $_POST['vehicle_id'] : NULL; // Driver có thể gửi
$reported_by_user_id = $_SESSION['user_id'];
$status = 'found_by_staff'; // Admin hoặc Driver tìm thấy

$sql = "INSERT INTO lost_and_found (item_name, description, status, route_id, vehicle_id, reported_by_user_id) 
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $item_name, $description, $status, $route_id, $vehicle_id, $reported_by_user_id);

$redirect_page = "admin_lost_and_found.php"; // Mặc định cho admin
if ($_SESSION['role'] == 'driver') {
    $redirect_page = "driver_report_found_item.php"; // Trang của driver
}

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Item reported successfully.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}
$stmt->close();
$conn->close();
header("Location: " . $redirect_page);
exit();
?>