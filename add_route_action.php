<?php
// add_route_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

include 'db.php';

// Lấy dữ liệu từ form
$route_id = $_POST['route_id'];
$route_name = $_POST['route_name'];
$ticket_price = $_POST['ticket_price']; // Lấy thêm giá vé

// --- BƯỚC KIỂM TRA TRÙNG LẶP (giữ nguyên) ---
$sql_check = "SELECT route_id FROM routes WHERE route_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $route_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $_SESSION['flash_message'] = "Error: Route ID '" . htmlspecialchars($route_id) . "' already exists.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: admin_routes.php");
    exit();
}
$stmt_check->close();
// --- KẾT THÚC BƯỚC KIỂM TRA ---


// Nếu không trùng, tiếp tục thêm mới (cập nhật câu lệnh INSERT)
$sql_insert = "INSERT INTO routes (route_id, route_name, ticket_price) VALUES (?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
// "ssd" -> s: string, s: string, d: double/decimal (cho giá vé)
$stmt_insert->bind_param("ssd", $route_id, $route_name, $ticket_price);

if ($stmt_insert->execute()) {
    $_SESSION['flash_message'] = "Successfully added new route: " . htmlspecialchars($route_name);
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Database error: " . $stmt_insert->error;
    $_SESSION['flash_message_type'] = "danger";
}

$stmt_insert->close();
$conn->close();

header("Location: admin_routes.php");
exit();
?>