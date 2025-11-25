<?php
// admin_ticket_action.php - Logic 5 Years for Free Ticket
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
include 'db.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $ticket_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        // LOGIC MỚI: 
        // Dùng câu lệnh CASE trong SQL để check giá tiền ngay lúc update
        // Nếu price = 0 -> Cộng 5 Năm
        // Nếu price > 0 -> Cộng 30 Ngày
        
        $sql = "UPDATE monthly_tickets 
                SET status = 'active', 
                    start_date = CURDATE(), 
                    end_date = CASE 
                        WHEN price = 0 THEN DATE_ADD(CURDATE(), INTERVAL 5 YEAR) 
                        ELSE DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
                    END
                WHERE ticket_id = ?";
                
        $msg = "Ticket approved. Validity updated based on ticket type.";
        $type = "success";
        
    } elseif ($action == 'reject') {
        $sql = "UPDATE monthly_tickets SET status = 'rejected' WHERE ticket_id = ?";
        $msg = "Ticket request rejected.";
        $type = "warning";
        
    } elseif ($action == 'delete') {
        $sql = "DELETE FROM monthly_tickets WHERE ticket_id = ?";
        $msg = "Ticket deleted permanently.";
        $type = "danger";
        
    } else {
        die("Invalid action");
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticket_id);
    
    if ($stmt->execute()) {
        $_SESSION['flash_message'] = $msg;
        $_SESSION['flash_message_type'] = $type;
    } else {
        $_SESSION['flash_message'] = "Error executing action: " . $stmt->error;
        $_SESSION['flash_message_type'] = "danger";
    }
    $stmt->close();
}

$conn->close();
header("Location: admin_manage_tickets.php");
exit();
?>