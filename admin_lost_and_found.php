<?php
// admin_lost_and_found.php
include 'admin_header.php';
include 'db.php';

if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . $_SESSION['flash_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Lấy dữ liệu cho dropdowns
$routes = $conn->query("SELECT route_id, route_name FROM routes ORDER BY route_id");
$vehicles = $conn->query("SELECT vehicle_id, license_plate FROM vehicles WHERE status = 'active' ORDER BY license_plate");
$users = $conn->query("SELECT user_id, username FROM users WHERE role = 'user' ORDER BY username"); 

// --- SỬA CÂU SQL (Thêm v.license_plate) ---
$sql = "SELECT 
            lf.*, 
            reporter.username AS reporter_name,
            claimer.username AS claimer_name,
            v.license_plate AS vehicle_plate  -- Lấy biển số xe nếu có
        FROM lost_and_found lf
        JOIN users reporter ON lf.reported_by_user_id = reporter.user_id
        LEFT JOIN users claimer ON lf.claimed_by_user_id = claimer.user_id 
        LEFT JOIN vehicles v ON lf.vehicle_id = v.vehicle_id -- Join thêm bảng vehicles
        ORDER BY lf.status ASC, lf.report_date DESC";
// --- KẾT THÚC SỬA SQL ---
$items = $conn->query($sql);
?>

<h1 class="mb-4">Lost & Found Management</h1>

<div class="card mb-4">
    <div class="card-header">Add Item Found by Staff</div>
    <div class="card-body">
        <form action="add_found_item_action.php" method="POST">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" class="form-control" name="item_name" placeholder="e.g., Black Wallet" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Description</label>
                    <input type="text" class="form-control" name="description" placeholder="Details..." required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Found on Route (Optional)</label>
                    <select name="route_id" class="form-select">
                        <option value="">-- Select Route --</option>
                        <?php 
                        mysqli_data_seek($routes, 0); 
                        while($row = $routes->fetch_assoc()) { 
                            echo "<option value='" . $row['route_id'] . "'>" . htmlspecialchars($row['route_id']) . "</option>"; 
                        } 
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 mt-4">Add Found Item</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">All Reported Items</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Item Name</th>
                        <th>Reported/Claimed By</th> 
                        <th>Route / Vehicle</th> <th>Date Reported</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $items->fetch_assoc()): 
                        $status_badge = '';
                        $status_text = $row['status']; 
                        if ($row['status'] == 'found_by_staff') $status_badge = 'badge bg-info text-dark';
                        if ($row['status'] == 'reported_by_user') $status_badge = 'badge bg-warning text-dark';
                        if ($row['status'] == 'claimed') {
                            $status_badge = 'badge bg-success';
                            $status_text = 'Claimed'; 
                        }
                    ?>
                    <tr>
                        <td><span class="<?php echo $status_badge; ?>"><?php echo htmlspecialchars($status_text); ?></span></td>
                        <td><strong><?php echo htmlspecialchars($row['item_name']); ?></strong></td>
                        <td>
                            <?php 
                            if ($row['status'] == 'claimed' && !empty($row['claimer_name'])) {
                                echo 'Claimed by: <strong>' . htmlspecialchars($row['claimer_name']) . '</strong>'; 
                            } else {
                                echo 'Reported by: ' . htmlspecialchars($row['reporter_name']); 
                            }
                            ?>
                        </td>
                        
                        <td>
                            <?php 
                            if (!empty($row['route_id'])) {
                                echo 'Route: ' . htmlspecialchars($row['route_id']);
                            }
                            if (!empty($row['vehicle_plate'])) {
                                echo '<br><small class="text-muted">Vehicle: ' . htmlspecialchars($row['vehicle_plate']) . '</small>';
                            }
                            ?>
                        </td>
                        <td><?php echo $row['report_date']; ?></td>
                        <td class="text-end text-nowrap">
                            <?php if ($row['status'] != 'claimed'): ?>
                            <form action="admin_update_lost_item_action.php" method="POST" class="d-inline">
                                <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                <select name="claimed_by_user_id" class="form-select form-select-sm d-inline" style="width: 150px;" required>
                                    <option value="">-- Select User --</option>
                                    <?php 
                                    mysqli_data_seek($users, 0);
                                    while($user = $users->fetch_assoc()) { 
                                        echo "<option value='" . $user['user_id'] . "'>" . htmlspecialchars($user['username']) . "</option>"; 
                                    } 
                                    ?>
                                </select>
                                <button type="submit" name="action" value="claim" class="btn btn-sm btn-success">Mark Claimed</button>
                            </form>
                            <?php endif; ?>
                            <a href="admin_update_lost_item_action.php?action=delete&id=<?php echo $row['item_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
include 'admin_footer.php';
?>