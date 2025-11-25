<?php
// admin_manage_tickets.php - Fixed Single Tab Link
include 'admin_header.php';
include 'db.php';

// Thông báo
if (isset($_SESSION['flash_message'])) {
    $msg_type = $_SESSION['flash_message_type'];
    echo '<div class="alert alert-'.$msg_type.' alert-dismissible fade show">'.$_SESSION['flash_message'].'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
}

// Lấy danh sách vé
$sql = "SELECT t.*, u.username, u.full_name, r.route_name 
        FROM monthly_tickets t 
        JOIN users u ON t.user_id = u.user_id 
        LEFT JOIN routes r ON t.route_id = r.route_id 
        ORDER BY FIELD(t.status, 'pending', 'active', 'rejected', 'expired'), t.registered_date DESC";
$tickets = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-ticket-detailed-fill me-2"></i>Monthly Tickets</h1>
    <span class="badge bg-primary fs-6"><?php echo $tickets->num_rows; ?> Records</span>
</div>

<div class="card shadow border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>User</th>
                        <th>Ticket Info</th>
                        <th>Evidence</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tickets->num_rows > 0): ?>
                        <?php while($row = $tickets->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4 text-muted">#<?php echo $row['ticket_id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo htmlspecialchars($row['face_image_path']); ?>" class="rounded-circle border me-2" width="40" height="40" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                        <div class="small text-muted">@<?php echo htmlspecialchars($row['username']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($row['ticket_scope'] == 'inter_route'): ?>
                                    <span class="badge bg-primary">Inter-Bus</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">Route <?php echo $row['route_id']; ?></span>
                                <?php endif; ?>
                                <div class="small mt-1 text-muted">Type: <?php echo ucfirst($row['priority_type']); ?></div>
                            </td>
                            <td>
                                <?php if (!empty($row['evidence_image_path'])): ?>
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEvidence<?php echo $row['ticket_id']; ?>">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    
                                    <div class="modal fade" id="modalEvidence<?php echo $row['ticket_id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content bg-dark">
                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title text-white">Evidence Proof</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="<?php echo htmlspecialchars($row['evidence_image_path']); ?>" class="img-fluid rounded">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">None</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-success"><?php echo number_format($row['price']); ?></td>
                            <td>
                                <?php 
                                    $s = $row['status'];
                                    $badge = 'secondary';
                                    if($s=='pending') $badge='warning text-dark';
                                    if($s=='active') $badge='success';
                                    if($s=='rejected') $badge='danger';
                                    echo "<span class='badge bg-$badge'>".strtoupper($s)."</span>";
                                ?>
                            </td>
                            <td class="text-end pe-4">
                                <?php if ($row['price'] > 0): ?>
                                    <a href="view_invoice.php?ticket_id=<?php echo $row['ticket_id']; ?>" class="btn btn-outline-info btn-sm me-1" title="View Invoice"><i class="bi bi-receipt"></i></a>
                                <?php endif; ?>

                                <?php if ($row['status'] == 'pending'): ?>
                                    <a href="admin_ticket_action.php?action=approve&id=<?php echo $row['ticket_id']; ?>" class="btn btn-success btn-sm" title="Approve"><i class="bi bi-check-lg"></i></a>
                                    <a href="admin_ticket_action.php?action=reject&id=<?php echo $row['ticket_id']; ?>" class="btn btn-outline-danger btn-sm" title="Reject"><i class="bi bi-x-lg"></i></a>
                                <?php else: ?>
                                    <a href="admin_ticket_action.php?action=delete&id=<?php echo $row['ticket_id']; ?>" class="btn btn-light btn-sm text-danger" onclick="return confirm('Delete permanently?');"><i class="bi bi-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">No ticket records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?>