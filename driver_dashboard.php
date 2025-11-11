<?php
// driver_dashboard.php
include 'driver_header.php'; // Header này đã lấy staff_id và lưu vào $_SESSION['staff_id']

$driver_staff_id = $_SESSION['staff_id'];

// Hiển thị thông báo (nếu có)
if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . $_SESSION['flash_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Lấy các chuyến đi HÔM NAY VÀ SẮP TỚI của tài xế này
$sql = "SELECT 
            sc.trip_id, 
            sc.departure_time, 
            sc.status, 
            r.route_id,
            r.route_name, 
            v.license_plate 
        FROM schedules sc
        JOIN routes r ON sc.route_id = r.route_id
        JOIN vehicles v ON sc.vehicle_id = v.vehicle_id
        WHERE sc.driver_id = ? AND sc.departure_time >= CURDATE()
        ORDER BY sc.departure_time ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $driver_staff_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1 class="mb-4">My Schedule</h1>

<div class="card">
    <div class="card-header">
        Today's and Upcoming Trips
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Departure Time</th>
                        <th>Route</th>
                        <th>Vehicle</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $status_badge = '';
                            if ($row['status'] == 'scheduled') $status_badge = 'badge bg-warning text-dark';
                            if ($row['status'] == 'running') $status_badge = 'badge bg-primary';
                            if ($row['status'] == 'completed') $status_badge = 'badge bg-success';
                            if ($row['status'] == 'cancelled') $status_badge = 'badge bg-danger';

                            echo "<tr>";
                            echo "<td><strong>" . htmlspecialchars($row["departure_time"]) . "</strong></td>";
                            echo "<td>" . htmlspecialchars($row["route_id"] . " - " . $row["route_name"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["license_plate"]) . "</td>";
                            echo "<td><span class='" . $status_badge . "'>" . $row["status"] . "</span></td>";
                            
                            // Nút hành động
                            echo "<td class='text-end text-nowrap'>";
                            if ($row['status'] == 'scheduled') {
                                echo "<a href='driver_update_trip_action.php?trip_id=" . $row["trip_id"] . "&status=running' class='btn btn-sm btn-primary' onclick='return confirm(\"Start this trip?\");'>Start Trip</a> ";
                            }
                            if ($row['status'] == 'running') {
                                echo "<a href='driver_update_trip_action.php?trip_id=" . $row["trip_id"] . "&status=completed' class='btn btn-sm btn-success' onclick='return confirm(\"Complete this trip?\");'>Complete Trip</a> ";
                            }
                            if ($row['status'] == 'scheduled') {
                                 echo "<a href='driver_update_trip_action.php?trip_id=" . $row["trip_id"] . "&status=cancelled' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to cancel?\");'>Cancel</a>";
                            }
                            echo "</td>";
                            
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>You have no scheduled trips.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php
$stmt->close();
$conn->close();
include 'driver_footer.php';
?>