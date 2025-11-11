<?php
// delete_route_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

include 'db.php';

$route_id = $_GET['id'];

// Trước khi xóa route, cần xóa các itineraries liên quan để tránh lỗi khóa ngoại
$sql_delete_itineraries = "DELETE FROM itineraries WHERE route_id = ?";
$stmt_itineraries = $conn->prepare($sql_delete_itineraries);
$stmt_itineraries->bind_param("s", $route_id);
$stmt_itineraries->execute();
$stmt_itineraries->close();

// Sau đó mới xóa route
$sql_delete_route = "DELETE FROM routes WHERE route_id = ?";
$stmt_route = $conn->prepare($sql_delete_route);
$stmt_route->bind_param("s", $route_id);

if ($stmt_route->execute()) {
    header("Location: admin_routes.php");
    exit();
} else {
    echo "Lỗi khi xóa: " . $stmt_route->error;
}

$stmt_route->close();
$conn->close();
?>