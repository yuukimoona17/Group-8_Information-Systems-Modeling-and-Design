<?php
// user_view_tickets.php - Final ID Card Style (With Visible Ticket ID)
include 'user_header.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy vé
$sql = "SELECT t.*, r.route_name, u.full_name 
        FROM monthly_tickets t 
        LEFT JOIN routes r ON t.route_id = r.route_id 
        JOIN users u ON t.user_id = u.user_id
        WHERE t.user_id = ? 
        ORDER BY t.registered_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<style>
    /* --- ID CARD STYLE --- */
    .wallet-container {
        min-height: 85vh;
        padding-bottom: 50px;
    }

    /* Khung thẻ */
    .id-card {
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        transition: transform 0.3s ease;
        min-height: 260px;
        height: 100%;
        border: 1px solid rgba(255,255,255,0.15);
        display: flex;
        flex-direction: column;
    }

    .id-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.6);
    }

    /* --- MÀU SẮC PHÂN LOẠI --- */
    .card-active { background: linear-gradient(135deg, #004e92, #000428); border-left: 5px solid #00d2ff; }
    .card-pending { background: linear-gradient(135deg, #ff9966, #ff5e62); border-left: 5px solid #ffc107; }
    .card-inactive { background: linear-gradient(135deg, #232526, #414345); border-left: 5px solid #6c757d; filter: grayscale(100%); opacity: 0.8; }

    /* Header của thẻ */
    .card-header-strip {
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(0,0,0,0.2);
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .logo-text {
        font-weight: 900;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-size: 0.9rem;
        color: rgba(255,255,255,0.9);
    }

    /* Badge trạng thái */
    .status-badge {
        font-size: 0.7rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 4px;
        text-transform: uppercase;
    }
    .status-active { background: #00d2ff; color: #000; box-shadow: 0 0 10px #00d2ff; }
    .status-pending { background: #fff; color: #d35400; }
    .status-inactive { background: #6c757d; color: #fff; }

    /* Nội dung chính */
    .card-body-content {
        padding: 20px;
        display: flex;
        flex-grow: 1;
    }

    /* Khung ảnh thẻ User */
    .photo-box {
        width: 90px;
        height: 120px;
        border-radius: 8px;
        border: 2px solid rgba(255,255,255,0.8);
        background: #ddd;
        flex-shrink: 0;
        object-fit: cover;
        margin-right: 15px;
        box-shadow: 3px 3px 10px rgba(0,0,0,0.3);
    }

    /* Thông tin chi tiết */
    .info-group {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .user-name {
        font-size: 1.1rem;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 5px;
        color: #fff;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        line-height: 1.2;
    }

    .detail-row {
        display: flex;
        font-size: 0.85rem;
        margin-bottom: 3px;
        color: rgba(255,255,255,0.8);
        align-items: center;
    }
    
    .detail-label {
        width: 70px;
        font-size: 0.7rem;
        text-transform: uppercase;
        opacity: 0.6;
    }

    .detail-value {
        font-weight: 600;
    }
    
    /* ID VÉ NỔI BẬT */
    .ticket-id-display {
        font-family: 'Courier New', Courier, monospace;
        font-weight: bold;
        color: #00d2ff;
        letter-spacing: 1px;
    }

    .category-tag {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    .cat-student { background: #f1c40f; color: #000; }
    .cat-normal { background: #ecf0f1; color: #000; }
    .cat-elderly { background: #e67e22; color: #fff; }
    .cat-priority { background: #e74c3c; color: #fff; }

    .card-footer-strip {
        background: rgba(0,0,0,0.4);
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        margin-top: auto;
    }

    .qr-wrapper {
        background: white;
        padding: 4px;
        border-radius: 6px;
        height: 80px;
        width: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 10px;
    }
    
    .price-tag {
        position: absolute;
        bottom: 60px;
        right: 20px;
        font-size: 1.2rem;
        font-weight: 900;
        color: rgba(255,255,255,0.15);
        transform: rotate(-15deg);
        border: 2px solid rgba(255,255,255,0.15);
        padding: 5px 10px;
        border-radius: 8px;
        pointer-events: none;
    }
</style>

<div class="container mt-5 wallet-container">
    
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <h2 class="text-white fw-bold border-start border-4 border-info ps-3">
                <i class="bi bi-person-vcard me-2"></i>My Bus Passes
            </h2>
            <p class="text-white-50 mb-0 ms-4">Manage your monthly passes & history</p>
        </div>
        <a href="user_register_ticket.php" class="btn btn-info fw-bold rounded-pill px-4 shadow">
            <i class="bi bi-plus-lg me-1"></i> New Registration
        </a>
    </div>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['flash_message_type']; ?> alert-dismissible fade show bg-dark text-white border-secondary mb-4">
            <i class="bi bi-bell-fill me-2"></i><?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4 align-items-stretch">
            <?php while($ticket = $result->fetch_assoc()): 
                // Logic hiển thị
                $card_style = "card-inactive"; 
                $status_badge = "EXPIRED";
                $badge_style = "status-inactive";
                
                if ($ticket['status'] == 'active') { $card_style = "card-active"; $status_badge = "VALID PASS"; $badge_style = "status-active"; }
                elseif ($ticket['status'] == 'pending') { $card_style = "card-pending"; $status_badge = "PENDING"; $badge_style = "status-pending"; }
                elseif ($ticket['status'] == 'rejected') { $status_badge = "REJECTED"; }

                $cat_bg = "cat-normal";
                $cat_name = "STANDARD";
                if ($ticket['priority_type'] == 'student') { $cat_bg = "cat-student"; $cat_name = "STUDENT / PUPIL"; }
                if ($ticket['priority_type'] == 'elderly') { $cat_bg = "cat-elderly"; $cat_name = "ELDERLY (>60)"; }
                if ($ticket['priority_type'] == 'disabled' || $ticket['priority_type'] == 'contributor') { $cat_bg = "cat-priority"; $cat_name = "PRIORITY"; }

                $scope_text = ($ticket['ticket_scope'] == 'inter_route') ? "INTER-BUS (Liên Tuyến)" : "ROUTE " . $ticket['route_id'];
                
                // Format ID vé dạng 6 chữ số (ví dụ: 000015)
                $display_id = str_pad($ticket['ticket_id'], 6, '0', STR_PAD_LEFT);
            ?>
            
            <div class="col-lg-6 col-xl-4">
                <div class="id-card <?php echo $card_style; ?>">
                    
                    <div class="card-header-strip">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bus-front-fill text-white fs-5 me-2"></i>
                            <span class="logo-text">HANOI BUS <span class="opacity-50 mx-1">|</span> #<?php echo $ticket['ticket_id']; ?></span>
                        </div>
                        <span class="status-badge <?php echo $badge_style; ?>"><?php echo $status_badge; ?></span>
                    </div>

                    <div class="card-body-content">
                        <img src="<?php echo htmlspecialchars($ticket['face_image_path']); ?>" class="photo-box" alt="User">
                        
                        <div class="info-group">
                            <div class="user-name"><?php echo htmlspecialchars($ticket['full_name']); ?></div>
                            
                            <div><span class="category-tag <?php echo $cat_bg; ?>"><?php echo $cat_name; ?></span></div>
                            
                            <div class="mt-2">
                                <div class="detail-row">
                                    <span class="detail-label">Ticket No</span>
                                    <span class="detail-value ticket-id-display text-white">
                                        <?php echo $display_id; ?>
                                    </span>
                                </div>

                                <div class="detail-row">
                                    <span class="detail-label">Scope</span>
                                    <span class="detail-value text-warning"><?php if($ticket['ticket_scope'] == 'inter_route') echo '<i class="bi bi-globe2"></i> '; ?><?php echo $scope_text; ?></span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Price</span>
                                    <span class="detail-value"><?php echo ($ticket['price'] == 0) ? "FREE (0 VND)" : number_format($ticket['price']) . " VND"; ?></span>
                                </div>
                            </div>
                        </div>

                        <?php if ($ticket['status'] == 'active'): ?>
                            <div class="qr-wrapper shadow">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=HB-<?php echo $ticket['ticket_id']; ?>" width="70">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="price-tag"><?php echo ($ticket['price'] == 0) ? "FREE" : number_format($ticket['price']); ?></div>

                    <div class="card-footer-strip">
                        <div class="text-white-50">Exp: <span class="text-white fw-bold ms-1"><?php echo ($ticket['end_date']) ? date('d/m/Y', strtotime($ticket['end_date'])) : '--/--/----'; ?></span></div>
                        <?php if ($ticket['price'] > 0): ?>
                            <a href="view_invoice.php?ticket_id=<?php echo $ticket['ticket_id']; ?>" class="btn btn-sm btn-outline-light rounded-pill px-3" style="border-color: rgba(255,255,255,0.4);"><i class="bi bi-receipt me-1"></i> Bill</a>
                        <?php else: ?>
                            <span class="text-success fw-bold" style="font-size: 0.7rem;"><i class="bi bi-gift-fill"></i> SUBSIDIZED</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 rounded-4 border border-dashed border-secondary bg-dark bg-opacity-50">
            <i class="bi bi-person-vcard text-secondary" style="font-size: 4rem; opacity: 0.5;"></i>
            <h4 class="text-white mt-3">No Bus Pass Found</h4>
            <p class="text-white-50">You haven't registered any monthly ticket yet.</p>
            <a href="user_register_ticket.php" class="btn btn-primary rounded-pill px-5 mt-2 fw-bold">Register Now</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'user_footer.php'; ?>