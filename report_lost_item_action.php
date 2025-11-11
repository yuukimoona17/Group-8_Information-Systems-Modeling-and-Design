<?php
// report_lost_item_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    die("Access Denied.");
}
include 'db.php';

$item_name = $_POST['item_name'];
$description = $_POST['description'];
$route_id = !empty($_POST['route_id']) ? $_POST['route_id'] : NULL;
$reported_by_user_id = $_SESSION['user_id'];
$status = 'reported_by_user'; // User báo mất

$sql = "INSERT INTO lost_and_found (item_name, description, status, route_id, reported_by_user_id) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $item_name, $description, $status, $route_id, $reported_by_user_id);

if ($stmt->execute()) {
    $_SESSION['flash_message'] = "Your lost item report has been submitted.";
    $_SESSION['flash_message_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Error: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
}
$stmt->close();
$conn->close();
header("Location: lost_and_found.php");
exit();
?>