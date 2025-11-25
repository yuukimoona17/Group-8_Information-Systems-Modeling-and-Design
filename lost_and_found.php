<?php
// ...
session_start(); // Nếu file chưa có session_start
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = "Please login to access this feature.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: login.php");
    exit();
}
// ...
include 'user_header.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

// Tab 1: Đồ do nhân viên tìm thấy (chưa ai nhận)
$sql_found = "SELECT lf.*, u.username AS reporter_name 
              FROM lost_and_found lf
              JOIN users u ON lf.reported_by_user_id = u.user_id
              WHERE lf.status = 'found_by_staff'
              ORDER BY lf.report_date DESC";
$found_items = $conn->query($sql_found);

// Tab 3: Đồ do chính user này báo mất
$sql_mine = "SELECT * FROM lost_and_found 
             WHERE reported_by_user_id = ? 
             ORDER BY report_date DESC";
$stmt_mine = $conn->prepare($sql_mine);
$stmt_mine->bind_param("i", $user_id);
$stmt_mine->execute();
$my_items = $stmt_mine->get_result();

// Lấy routes cho form (Tab 2)
$routes = $conn->query("SELECT route_id, route_name FROM routes ORDER BY route_id");
?>

<h1 class="mb-4">Lost & Found</h1>

<?php
if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . $_SESSION['flash_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}
?>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="found-tab" data-bs-toggle="tab" data-bs-target="#found-tab-pane" type="button">Items Found by Staff</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="report-tab" data-bs-toggle="tab" data-bs-target="#report-tab-pane" type="button">Report a Lost Item</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="myreports-tab" data-bs-toggle="tab" data-bs-target="#myreports-tab-pane" type="button">My Reported Items</button>
    </li>
</ul>

<div class="tab-content" id="myTabContent">

    <div class="tab-pane fade show active" id="found-tab-pane" role="tabpanel">
        <div class="card bg-dark text-white border-top-0 rounded-0 rounded-bottom">
            <div class="card-body">
                <?php if ($found_items->num_rows > 0): ?>
                <ul class="list-group list-group-flush">
                    <?php while($row = $found_items->fetch_assoc()): ?>
                    <li class="list-group-item bg-dark text-white">
                        <h5><?php echo htmlspecialchars($row['item_name']); ?></h5>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <small class="text-muted">
                            Found on Route: <?php echo htmlspecialchars($row['route_id'] ?? 'N/A'); ?> | 
                            Reported on: <?php echo $row['report_date']; ?>
                        </small>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <?php else: ?>
                <p class="text-center text-muted">No items found by staff match this criteria.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="report-tab-pane" role="tabpanel">
        <div class="card bg-dark text-white border-top-0 rounded-0 rounded-bottom">
            <div class="card-body">
                <form action="report_lost_item_action.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Item Name</label>
                        <input type="text" class="form-control" name="item_name" placeholder="e.g., Red Umbrella" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (color, brand, etc.)</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                     <div class="mb-3">
                        <label class="form-label">Lost on Route (if you remember)</label>
                        <select name="route_id" class="form-select">
                            <option value="">-- Select Route --</option>
                            <?php while($row = $routes->fetch_assoc()) { echo "<option value='" . $row['route_id'] . "'>" . htmlspecialchars($row['route_id'] . ' - ' . $row['route_name']) . "</option>"; } ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Lost Report</button>
                </form>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="myreports-tab-pane" role="tabpanel">
         <div class="card bg-dark text-white border-top-0 rounded-0 rounded-bottom">
            <div class="card-body">
                <?php if ($my_items->num_rows > 0): ?>
                <ul class="list-group list-group-flush">
                    <?php while($row = $my_items->fetch_assoc()): 
                        $status_badge = '';
                        if ($row['status'] == 'reported_by_user') $status_badge = 'badge bg-warning text-dark';
                        if ($row['status'] == 'claimed') $status_badge = 'badge bg-success';
                    ?>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5><?php echo htmlspecialchars($row['item_name']); ?></h5>
                            <small class="text-muted">Reported on: <?php echo $row['report_date']; ?></small>
                        </div>
                        <span class="<?php echo $status_badge; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <?php else: ?>
                <p class="text-center text-muted">You have not reported any lost items.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php
$conn->close();
include 'user_footer.php';
?>