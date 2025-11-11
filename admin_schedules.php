<?php
// admin_schedules.php
include 'admin_header.php';
include 'db.php';

$flash_message = '';
if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    $flash_message = '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . $_SESSION['flash_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Lấy dữ liệu cho các dropdown
$routes = $conn->query("SELECT route_id, route_name FROM routes ORDER BY route_id");
$vehicles = $conn->query("SELECT vehicle_id, license_plate FROM vehicles WHERE status = 'active' ORDER BY license_plate");
$drivers = $conn->query("SELECT s.staff_id, s.full_name FROM staff s WHERE s.staff_role = 'driver' ORDER BY s.full_name");

// --- SỬA CÂU SQL (Thêm actual_completion_time) ---
$sql_schedules = "SELECT 
                    sc.trip_id, 
                    sc.departure_time, 
                    sc.actual_completion_time, -- Lấy thêm cột này
                    sc.status, 
                    r.route_name, 
                    v.license_plate, 
                    s.full_name 
                  FROM schedules sc
                  JOIN routes r ON sc.route_id = r.route_id
                  JOIN vehicles v ON sc.vehicle_id = v.vehicle_id
                  JOIN staff s ON sc.driver_id = s.staff_id
                  ORDER BY sc.departure_time DESC";
// --- KẾT THÚC SỬA SQL ---
$schedules_result = $conn->query($sql_schedules);
?>

<h1 class="mb-4">Schedule Management</h1>
<?php echo $flash_message; ?>

<div class="card mb-4">
    <div class="card-header">Add New Schedule (Trip)</div>
    <div class="card-body">
        <form action="add_schedule_action.php" method="POST">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Route</label>
                    <select name="route_id" class="form-select" required>
                        <option value="">-- Select Route --</option>
                        <?php 
                        mysqli_data_seek($routes, 0); // Reset con trỏ
                        while($row = $routes->fetch_assoc()) { echo "<option value='" . $row['route_id'] . "'>" . htmlspecialchars($row['route_id'] . ' - ' . $row['route_name']) . "</option>"; } 
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Vehicle (Active only)</label>
                    <select name="vehicle_id" class="form-select" required>
                        <option value="">-- Select Vehicle --</option>
                        <?php 
                        mysqli_data_seek($vehicles, 0); // Reset con trỏ
                        while($row = $vehicles->fetch_assoc()) { echo "<option value='" . $row['vehicle_id'] . "'>" . htmlspecialchars($row['license_plate']) . "</option>"; } 
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Driver</label>
                    <select name="driver_id" class="form-select" required>
                        <option value="">-- Select Driver --</option>
                        <?php 
                        mysqli_data_seek($drivers, 0); // Reset con trỏ
                        while($row = $drivers->fetch_assoc()) { echo "<option value='" . $row['staff_id'] . "'>" . htmlspecialchars($row['full_name']) . "</option>"; } 
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Departure Time</label>
                    <input type="datetime-local" class="form-control" name="departure_time" required>
                </div>
                <div class="col-md-12 text-center mt-3">
                    <button type="submit" class="btn btn-primary w-50">Add Schedule</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">List of Scheduled Trips</div>
    <div class="card-body">
        <div class="table-responsive"> <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Route</th>
                        <th>Vehicle</th>
                        <th>Driver</th>
                        <th>Departure Time</th>
                        <th>Completion Time</th> <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($schedules_result->num_rows > 0) {
                        while($row = $schedules_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><strong>" . htmlspecialchars($row["route_name"]) . "</strong></td>";
                            echo "<td>" . htmlspecialchars($row["license_plate"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["full_name"]) . "</td>";
                            echo "<td>" . $row["departure_time"] . "</td>";
                            
                            // --- HIỂN THỊ COMPLETION TIME ---
                            echo "<td>" . ($row["actual_completion_time"] ?? '<em class="text-muted">N/A</em>') . "</td>"; 
                            // --- KẾT THÚC ---
                            
                            echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                            echo "<td class='text-end'>";
                            echo "<a href='delete_schedule_action.php?id=" . $row["trip_id"] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\");'>Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No schedules found.</td></tr>"; // colspan thành 7
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
include 'admin_footer.php';
?>