<?php
// view_invoice.php (Phần đầu PHP) - SỬA LẠI LINK
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) { die("Access Denied"); }

$ticket_id = $_GET['ticket_id'];
$current_user_id = $_SESSION['user_id'];
$current_role = $_SESSION['role'] ?? 'user';

$sql = "SELECT i.*, 
               t.registered_date, t.ticket_scope, t.priority_type, t.route_id, t.price as ticket_price,
               t.user_id as owner_id, 
               u.full_name, u.email 
        FROM payment_invoices i
        JOIN monthly_tickets t ON i.ticket_id = t.ticket_id
        JOIN users u ON t.user_id = u.user_id
        WHERE i.ticket_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();
$inv = $result->fetch_assoc();

// --- SỬA ĐOẠN NÀY ---
// Link về user_view_tickets.php (có s)
$back_link = ($current_role === 'admin') ? 'admin_manage_tickets.php' : 'user_view_tickets.php';
// --------------------

if (!$inv || ($current_role !== 'admin' && $inv['owner_id'] != $current_user_id)) {
    die("Access Denied or Invoice Not Found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $inv['transaction_code']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #525659; font-family: 'Courier New', Courier, monospace; padding: 30px 0; }
        
        .receipt-wrapper {
            max-width: 380px;
            margin: 0 auto;
            background: #fff;
            padding: 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
            filter: drop-shadow(0 5px 10px rgba(0,0,0,0.2));
        }

        .receipt-content {
            padding: 25px 25px 40px 25px;
            background: #fff;
            clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%);
        }

        .receipt-wrapper::after {
            content: "";
            display: block;
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 10px;
            background: radial-gradient(circle, transparent, transparent 50%, #fff 50%, #fff 100%) -7px -10px / 15px 20px repeat-x;
            transform: rotate(180deg);
        }

        .dashed-hr { border-bottom: 2px dashed #333; margin: 15px 0; opacity: 0.5; }
        .logo-area { text-align: center; margin-bottom: 20px; }
        .logo-text { font-weight: 900; font-size: 1.4rem; letter-spacing: 1px; text-transform: uppercase; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem; }
        .info-label { color: #666; }
        .info-val { font-weight: bold; text-align: right; }
        .total-section { background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 15px 0; border: 1px solid #eee; }
        .total-price { font-size: 1.3rem; font-weight: 800; }
        .barcode-area { text-align: center; margin-top: 20px; }
        .print-actions { position: fixed; bottom: 20px; right: 20px; z-index: 100; }
        
        @media print {
            body { background: white; }
            .print-actions { display: none; }
            .receipt-wrapper { box-shadow: none; filter: none; }
        }
    </style>
</head>
<body>

<div class="receipt-wrapper">
    <div class="receipt-content">
        <div class="logo-area">
            <i class="bi bi-bus-front-fill fs-1"></i>
            <div class="logo-text">HANOI BUS</div>
            <div class="small text-muted">E-Ticket Payment Receipt</div>
        </div>

        <div class="dashed-hr"></div>

        <div class="info-row"><span class="info-label">Date:</span><span class="info-val"><?php echo date('d/m/Y H:i', strtotime($inv['payment_time'])); ?></span></div>
        <div class="info-row"><span class="info-label">Trans ID:</span><span class="info-val"><?php echo $inv['transaction_code']; ?></span></div>
        <div class="info-row"><span class="info-label">Method:</span><span class="info-val"><?php echo $inv['payment_method']; ?></span></div>

        <div class="dashed-hr"></div>

        <div class="mb-1 fw-bold text-uppercase">Customer</div>
        <div class="info-row"><span class="info-label">Name:</span><span class="info-val"><?php echo htmlspecialchars($inv['full_name']); ?></span></div>
        <div class="info-row"><span class="info-label">Email:</span><span class="info-val"><?php echo htmlspecialchars($inv['email']); ?></span></div>

        <div class="dashed-hr"></div>

        <div class="mb-1 fw-bold text-uppercase">Description</div>
        <div class="d-flex justify-content-between mb-1">
            <span>Monthly Pass (<?php echo ucfirst($inv['priority_type']); ?>)</span>
            <span class="fw-bold"><?php echo number_format($inv['amount']); ?></span>
        </div>
        <div class="small text-muted fst-italic">
            <?php 
                if ($inv['ticket_scope'] == 'inter_route') echo "Scope: Inter-Bus (All Routes)";
                else echo "Scope: Route " . $inv['route_id'];
            ?>
        </div>

        <div class="total-section">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">TOTAL PAID</span>
                <span class="total-price"><?php echo number_format($inv['amount']); ?> đ</span>
            </div>
        </div>

        <div class="text-center text-success fw-bold small mb-3"><i class="bi bi-check-circle-fill me-1"></i> PAYMENT SUCCESSFUL</div>

        <div class="dashed-hr"></div>

        <div class="barcode-area">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo $inv['transaction_code']; ?>" alt="QR">
            <div class="mt-2 small text-muted">Scan to verify transaction</div>
            <div class="mt-1 small fw-bold">Thank you for choosing public transport!</div>
        </div>
    </div>
</div>

<div class="print-actions">
    <button onclick="window.print()" class="btn btn-primary rounded-pill shadow fw-bold px-4"><i class="bi bi-printer me-2"></i>Print</button>
    <a href="<?php echo $back_link; ?>" class="btn btn-secondary rounded-circle shadow ms-2" style="width: 40px; height: 40px; display:inline-flex; align-items:center; justify-content:center;"><i class="bi bi-arrow-left"></i></a>
</div>

</body>
</html>