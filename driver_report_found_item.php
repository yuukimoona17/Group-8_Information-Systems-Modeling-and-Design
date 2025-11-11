<?php
// driver_report_found_item.php
include 'driver_header.php'; // Đã có db.php và session
// driver_header.php cũng đã lấy $_SESSION['staff_id']

// Lấy chuyến đi (schedules) mà tài xế này đang chạy HOẶC vừa chạy xong
// Để họ có thể chọn "Tìm thấy trên chuyến này"
$driver_staff_id = $_SESSION['staff_id'];
$sql_trips = "SELECT sc.trip_id, sc.vehicle_id, r.route_id, r.route_name, sc.departure_time
              FROM schedules sc
              JOIN routes r ON sc.route_id = r.route_id
              WHERE sc.driver_id = ? 
              AND (sc.status = 'running' OR (sc.status = 'completed' AND sc.departure_time >= CURDATE()))
              ORDER BY sc.departure_time DESC";
$stmt_trips = $conn->prepare($sql_trips);
$stmt_trips->bind_param("i", $driver_staff_id);
$stmt_trips->execute();
$trips = $stmt_trips->get_result();

?>

<h1 class="mb-4">Report Item Found on Bus</h1>

<?php
if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . $_SESSION['flash_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}
?>

<div class="card">
    <div class="card-header">New Item Details</div>
    <div class="card-body">
        <form action="add_found_item_action.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Item Name</label>
                <input type="text" class="form-control" name="item_name" placeholder="e.g., Blue Backpack" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description (color, brand, location found...)</label>
                <textarea class="form-control" name="description" rows="3" required></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Found on my Trip (Optional)</label>
                <select name="trip_id" class="form-select" onchange="updateTripData(this)">
                    <option value="">-- Select Trip (if applicable) --</option>
                    <?php while($row = $trips->fetch_assoc()): ?>
                    <option value="<?php echo $row['trip_id']; ?>" 
                            data-route="<?php echo $row['route_id']; ?>" 
                            data-vehicle="<?php echo $row['vehicle_id']; ?>">
                        <?php echo htmlspecialchars($row['route_id'] . ' @ ' . $row['departure_time']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <input type="hidden" name="route_id" id="hidden_route_id">
            <input type="hidden" name="vehicle_id" id="hidden_vehicle_id">

            <button type="submit" class="btn btn-primary">Submit Report</button>
        </form>
    </div>
</div>

<script>
// Script này tự động điền route_id và vehicle_id khi tài xế chọn chuyến
function updateTripData(selectElement) {
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    document.getElementById('hidden_route_id').value = selectedOption.getAttribute('data-route') || '';
    document.getElementById('hidden_vehicle_id').value = selectedOption.getAttribute('data-vehicle') || '';
}
</script>

<?php
$conn->close();
include 'driver_footer.php';
?>