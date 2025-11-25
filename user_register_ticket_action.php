<?php
// user_register_ticket_action.php - Fixed ArgumentCountError
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { die("Access Denied"); }

$user_id = $_SESSION['user_id'];
$priority_type = $_POST['priority_type'];
$ticket_scope = $_POST['ticket_scope'];
// Nếu là vé liên tuyến thì route_id là NULL
$route_id = ($ticket_scope == 'single_route') ? $_POST['route_id'] : NULL;
$price = $_POST['final_price']; 
$dob = $_POST['dob']; 

// Config Upload
$upload_dir = "uploads/";
if (!file_exists($upload_dir)) { mkdir($upload_dir, 0777, true); }

// 1. Upload Face Image (Bắt buộc)
$face_img_path = "";
if (isset($_FILES['face_image']) && $_FILES['face_image']['error'] == 0) {
    $ext = pathinfo($_FILES['face_image']['name'], PATHINFO_EXTENSION);
    $filename = "face_" . $user_id . "_" . time() . "." . $ext;
    $target = $upload_dir . $filename;
    if (move_uploaded_file($_FILES['face_image']['tmp_name'], $target)) {
        $face_img_path = $target;
    }
} else {
    $_SESSION['flash_message'] = "Face image is required.";
    $_SESSION['flash_message_type'] = "danger";
    header("Location: user_register_ticket.php");
    exit();
}

// 2. Upload Evidence Image (Tùy chọn)
$evidence_img_path = NULL;
if ($priority_type !== 'normal') {
    if (isset($_FILES['evidence_image']) && $_FILES['evidence_image']['error'] == 0) {
        $ext = pathinfo($_FILES['evidence_image']['name'], PATHINFO_EXTENSION);
        $filename = "evidence_" . $user_id . "_" . time() . "." . $ext;
        $target = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['evidence_image']['tmp_name'], $target)) {
            $evidence_img_path = $target;
        }
    } else {
        $_SESSION['flash_message'] = "Evidence document is required for priority tickets.";
        $_SESSION['flash_message_type'] = "danger";
        header("Location: user_register_ticket.php");
        exit();
    }
}

// 3. INSERT TICKET
$sql = "INSERT INTO monthly_tickets (user_id, dob, route_id, ticket_scope, priority_type, price, face_image_path, evidence_image_path, status, registered_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

$stmt = $conn->prepare($sql);

// SỬA LỖI Ở ĐÂY: "issssdss" (8 ký tự cho 8 biến)
// i: user_id
// s: dob
// s: route_id
// s: ticket_scope
// s: priority_type
// d: price
// s: face
// s: evidence
$stmt->bind_param("issssdss", $user_id, $dob, $route_id, $ticket_scope, $priority_type, $price, $face_img_path, $evidence_img_path);

if ($stmt->execute()) {
    $new_ticket_id = $stmt->insert_id; // Lấy ID vé vừa tạo
    
    // 4. GENERATE INVOICE (Nếu có tiền)
    if ($price > 0) {
        $transaction_code = "TRX" . strtoupper(uniqid()) . rand(100, 999); 
        $sql_inv = "INSERT INTO payment_invoices (ticket_id, transaction_code, amount, payment_method, payment_time) VALUES (?, ?, ?, 'VNPay QR', NOW())";
        $stmt_inv = $conn->prepare($sql_inv);
        $stmt_inv->bind_param("isi", $new_ticket_id, $transaction_code, $price);
        $stmt_inv->execute();
        $stmt_inv->close();
        
        $_SESSION['flash_message'] = "Payment Successful! Ticket Registered.";
        $_SESSION['flash_message_type'] = "success";
        
        // Chuyển hướng đến trang xem Hóa Đơn
        header("Location: view_invoice.php?ticket_id=" . $new_ticket_id);
    } else {
        // Nếu miễn phí
        $_SESSION['flash_message'] = "Free Ticket Registered! Waiting for approval.";
        $_SESSION['flash_message_type'] = "success";
        header("Location: user_view_tickets.php");
    }

} else {
    $_SESSION['flash_message'] = "Error: " . $stmt->error;
    $_SESSION['flash_message_type'] = "danger";
    header("Location: user_register_ticket.php");
}

$stmt->close();
$conn->close();
?>