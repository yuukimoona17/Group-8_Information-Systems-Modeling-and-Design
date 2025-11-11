<?php
// admin_update_lost_item_action.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

// Xử lý Xóa
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $item_id = $_GET['id'];
    $sql = "DELETE FROM lost_and_found WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Item deleted.";
        $_SESSION['flash_message_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Error: " . $stmt->error;
        $_SESSION['flash_message_type'] = "danger";
    }
    $stmt->close();
}

// Xử lý "Mark Claimed"
if (isset($_POST['action']) && $_POST['action'] == 'claim' && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    $claimed_by_user_id = $_POST['claimed_by_user_id'];
    
    $sql = "UPDATE lost_and_found SET status = 'claimed', claimed_by_user_id = ? WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $claimed_by_user_id, $item_id);
     if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Item marked as claimed.";
        $_SESSION['flash_message_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Error: " . $stmt->error;
        $_SESSION['flash_message_type'] = "danger";
    }
    $stmt->close();
}

$conn->close();
header("Location: admin_lost_and_found.php");
exit();
?>