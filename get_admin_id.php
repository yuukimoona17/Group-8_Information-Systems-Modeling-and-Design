<?php
// get_admin_id.php: File nhỏ để user tìm ra ID của admin
include 'db.php';
header('Content-Type: application/json');

$sql_get_admin = "SELECT user_id FROM users WHERE role = 'admin' LIMIT 1";
$admin_result = $conn->query($sql_get_admin);
$admin_id = null;
if ($admin_result && $admin_result->num_rows > 0) {
    $admin_id = $admin_result->fetch_assoc()['user_id'];
}
echo json_encode(['admin_id' => $admin_id]);
?>