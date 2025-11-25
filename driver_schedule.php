<?php
// driver_schedule.php - Full Schedule History
include 'driver_header.php';
include 'db.php';

$driver_id = $_SESSION['staff_id'];

// Lấy toàn bộ lịch trình (Mới nhất lên đầu)
$sql = "SELECT sc.trip_id, sc.departure_time, sc.status, sc.actual_completion_time, 
               r.route_name, r.route_id, v.license_plate 
        FROM schedules sc
        JOIN routes r ON sc.route_id = r.route_id
        JOIN vehicles v ON sc.vehicle_id = v.vehicle_id
        WHERE sc.driver_id = ? 
        ORDER BY sc.departure_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-white fw-bold border-start border-4 border-warning ps-3">My Schedule</h2>
            <p class="text-white-50 mb-0 ms-4">All upcoming and past trips</p>
        </div>
        <a href="driver_dashboard.php" class="btn btn-outline-light rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="card bg-dark border border-secondary shadow-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead class="bg-black text-secondary text-uppercase small">
                        <tr>
                            <th class="ps-4">Date & Time</th>
                            <th>Route Info</th>
                            <th>Vehicle</th>
                            <th>Status</th>
                            <th>Completion Time</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $st = $row['status'];
                                $badge_cls = 'secondary';
                                if ($st == 'scheduled') $badge_cls = 'warning text-dark';
                                if ($st == 'running') $badge_cls = 'primary';
                                if ($st == 'completed') $badge_cls = 'success';
                                if ($st == 'cancelled') $badge_cls = 'danger';
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-white"><?php echo date('d/m/Y', strtotime($row['departure_time'])); ?></div>
                                    <div class="small text-white-50"><?php echo date('H:i', strtotime($row['departure_time'])); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark me-1"><?php echo $row['route_id']; ?></span>
                                    <span class="text-white-50"><?php echo htmlspecialchars($row['route_name']); ?></span>
                                </td>
                                <td>
                                    <span class="text-white"><?php echo htmlspecialchars($row['license_plate']); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $badge_cls; ?> text-uppercase"><?php echo $st; ?></span>
                                </td>
                                <td>
                                    <?php echo ($row['actual_completion_time']) ? date('H:i d/m', strtotime($row['actual_completion_time'])) : '--'; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($st == 'scheduled'): ?>
                                        <a href="driver_update_trip_action.php?trip_id=<?php echo $row['trip_id']; ?>&status=running" class="btn btn-sm btn-primary fw-bold px-3 rounded-pill" onclick="return confirm('Start this trip now?')">START</a>
                                    <?php elseif ($st == 'running'): ?>
                                        <a href="driver_update_trip_action.php?trip_id=<?php echo $row['trip_id']; ?>&status=completed" class="btn btn-sm btn-success fw-bold px-3 rounded-pill" onclick="return confirm('Finish this trip?')">FINISH</a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary px-3 rounded-pill" disabled>CLOSED</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-3 opacity-50"></i>
                                    No schedules found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'driver_footer.php'; ?>