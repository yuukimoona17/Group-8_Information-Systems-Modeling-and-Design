<?php
// driver_dashboard.php - Modern UI
include 'driver_header.php'; // Đã có session_start và check quyền
include 'db.php';

$driver_staff_id = $_SESSION['staff_id'];
$driver_name = $_SESSION['full_name'];

// Lấy thống kê nhanh
// 1. Số chuyến hôm nay
$sql_today = "SELECT COUNT(*) as total FROM schedules WHERE driver_id = ? AND DATE(departure_time) = CURDATE()";
$stmt = $conn->prepare($sql_today);
$stmt->bind_param("i", $driver_staff_id);
$stmt->execute();
$trips_today = $stmt->get_result()->fetch_assoc()['total'];

// 2. Chuyến sắp tới gần nhất
$sql_next = "SELECT r.route_name, s.departure_time 
             FROM schedules s 
             JOIN routes r ON s.route_id = r.route_id 
             WHERE s.driver_id = ? AND s.departure_time >= NOW() 
             ORDER BY s.departure_time ASC LIMIT 1";
$stmt = $conn->prepare($sql_next);
$stmt->bind_param("i", $driver_staff_id);
$stmt->execute();
$next_trip = $stmt->get_result()->fetch_assoc();
?>

<div class="container mt-4 mb-5">
    
    <div class="driver-welcome-card text-white">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-1">Hello, <?php echo htmlspecialchars($driver_name); ?>! 👋</h2>
                <p class="text-white-50 mb-0">Have a safe journey today.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-inline-block bg-white bg-opacity-10 px-4 py-2 rounded-3 text-center me-2">
                    <div class="fs-4 fw-bold text-warning"><?php echo $trips_today; ?></div>
                    <div class="small text-white-50">Trips Today</div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['flash_message_type']; ?> alert-dismissible fade show shadow-sm border-0 mb-4">
            <i class="bi bi-info-circle-fill me-2"></i><?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($next_trip): ?>
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-clock-history fs-4 me-3"></i>
        <div>
            <strong>Next Trip:</strong> <?php echo date('H:i', strtotime($next_trip['departure_time'])); ?> 
            - Route <span class="fw-bold"><?php echo htmlspecialchars($next_trip['route_name']); ?></span>
        </div>
    </div>
    <?php endif; ?>

    <h5 class="text-white fw-bold mb-3 ps-2 border-start border-4 border-primary">Driver Tools</h5>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="driver_validate_ticket.php" class="driver-action-btn">
                <i class="bi bi-qr-code-scan driver-action-icon"></i>
                <h5 class="fw-bold">Check Ticket</h5>
                <p class="small text-white-50 mb-0">Validate passenger passes</p>
            </a>
        </div>

        <div class="col-md-4">
            <a href="driver_schedule.php" class="driver-action-btn"> <i class="bi bi-calendar-week driver-action-icon"></i>
                <h5 class="fw-bold">My Schedule</h5>
                <p class="small text-white-50 mb-0">View daily tasks</p>
            </a>
        </div>

        <div class="col-md-4">
            <a href="driver_report_found_item.php" class="driver-action-btn">
                <i class="bi bi-box-seam driver-action-icon"></i>
                <h5 class="fw-bold">Found Item</h5>
                <p class="small text-white-50 mb-0">Report lost property</p>
            </a>
        </div>
    </div>

    <div class="mt-5">
        <h5 class="text-white fw-bold mb-3 ps-2 border-start border-4 border-success">Today's Schedule</h5>
        <?php
        // Query lại lịch trình để hiển thị bảng
        $sql_list = "SELECT sc.trip_id, sc.departure_time, sc.status, r.route_name, v.license_plate 
                     FROM schedules sc
                     JOIN routes r ON sc.route_id = r.route_id
                     JOIN vehicles v ON sc.vehicle_id = v.vehicle_id
                     WHERE sc.driver_id = ? AND sc.departure_time >= CURDATE()
                     ORDER BY sc.departure_time ASC";
        $stmt_list = $conn->prepare($sql_list);
        $stmt_list->bind_param("i", $driver_staff_id);
        $stmt_list->execute();
        $res_list = $stmt_list->get_result();
        ?>
        
        <div class="card bg-dark border border-secondary">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead class="text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4">Time</th>
                                <th>Route</th>
                                <th>Vehicle</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($res_list->num_rows > 0): ?>
                                <?php while($row = $res_list->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo date('H:i', strtotime($row['departure_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['route_name']); ?></td>
                                    <td><span class="badge bg-secondary bg-opacity-25 text-light"><?php echo htmlspecialchars($row['license_plate']); ?></span></td>
                                    <td>
                                        <?php 
                                            $st = $row['status'];
                                            $cls = 'secondary';
                                            if($st=='scheduled') $cls='warning text-dark';
                                            if($st=='running') $cls='primary';
                                            if($st=='completed') $cls='success';
                                            echo "<span class='badge bg-$cls'>".ucfirst($st)."</span>";
                                        ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if ($row['status'] == 'scheduled'): ?>
                                            <a href="driver_update_trip_action.php?trip_id=<?php echo $row['trip_id']; ?>&status=running" class="btn btn-sm btn-primary rounded-pill px-3">Start</a>
                                        <?php elseif ($row['status'] == 'running'): ?>
                                            <a href="driver_update_trip_action.php?trip_id=<?php echo $row['trip_id']; ?>&status=completed" class="btn btn-sm btn-success rounded-pill px-3">Finish</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No trips scheduled for today.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include 'driver_footer.php'; ?>