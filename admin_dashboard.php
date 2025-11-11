<?php
// admin_dashboard.php (Phiên bản Nâng cấp)
include 'admin_header.php';
include 'db.php';

// --- 1. Lấy dữ liệu cho các Thẻ thống kê (Stat Cards) ---
$total_routes = $conn->query("SELECT COUNT(*) AS total FROM routes")->fetch_assoc()['total'];
$total_stops = $conn->query("SELECT COUNT(*) AS total FROM bus_stops")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];

// Dữ liệu mới
$total_new_feedback = $conn->query("SELECT COUNT(*) AS total FROM feedback WHERE status = 'new'")->fetch_assoc()['total'];
$total_vehicles = $conn->query("SELECT COUNT(*) AS total FROM vehicles WHERE status = 'active'")->fetch_assoc()['total'];
$total_drivers = $conn->query("SELECT COUNT(*) AS total FROM staff WHERE staff_role = 'driver'")->fetch_assoc()['total'];
$total_trips_today = $conn->query("SELECT COUNT(*) AS total FROM schedules WHERE DATE(departure_time) = CURDATE()")->fetch_assoc()['total'];


// --- 2. Lấy 5 Phản hồi mới nhất chưa được giải quyết ---
$sql_feedback = "SELECT f.feedback_id, f.title, u.username 
                 FROM feedback f
                 JOIN users u ON f.user_id = u.user_id
                 WHERE f.status != 'resolved'
                 ORDER BY f.created_at DESC
                 LIMIT 5";
$recent_feedback = $conn->query($sql_feedback);


// --- 3. Lấy dữ liệu Biểu đồ (Trips trong 7 ngày qua) ---
$chart_labels = [];
$chart_data = [];
$date_range = []; // Mảng 7 ngày

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $date_range[$date] = 0; // Khởi tạo mảng với 7 ngày, count = 0
    $chart_labels[] = date('D, d-M', strtotime($date)); // Format cho đẹp (e.g., Mon, 28-Oct)
}

$sql_chart = "SELECT DATE(departure_time) as trip_date, COUNT(*) as trip_count 
              FROM schedules 
              WHERE departure_time >= CURDATE() - INTERVAL 6 DAY 
              GROUP BY DATE(departure_time)";
$chart_result = $conn->query($sql_chart);

if ($chart_result) {
    while ($row = $chart_result->fetch_assoc()) {
        if (isset($date_range[$row['trip_date']])) {
            $date_range[$row['trip_date']] = $row['trip_count']; // Cập nhật count
        }
    }
}
$chart_data = array_values($date_range); // Lấy
?>

<h1 class="mb-4">System Dashboard</h1>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo $total_routes; ?></div>
                        <div class="text-white-75 small">Total Routes</div>
                    </div>
                    <i class="bi bi-card-list fs-1 text-white-50"></i>
                </div>
            </div>
             <a class="card-footer d-flex align-items-center justify-content-between text-white" href="admin_routes.php">
                <span class="small">View Details</span>
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                 <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo $total_stops; ?></div>
                        <div class="text-white-75 small">Total Bus Stops</div>
                    </div>
                    <i class="bi bi-geo-alt-fill fs-1 text-white-50"></i>
                </div>
            </div>
             <a class="card-footer d-flex align-items-center justify-content-between text-white" href="admin_bus_stops.php">
                <span class="small">View Details</span>
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-dark bg-warning h-100">
            <div class="card-body">
                 <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo $total_users; ?></div>
                        <div class="text-white-75 small">Total Accounts</div>
                    </div>
                    <i class="bi bi-people-fill fs-1 text-white-50"></i>
                </div>
            </div>
             <a class="card-footer d-flex align-items-center justify-content-between text-dark" href="admin_users.php">
                <span class="small">View Details</span>
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-danger h-100">
            <div class="card-body">
                 <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-2 fw-bold"><?php echo $total_new_feedback; ?></div>
                        <div class="text-white-75 small">New Feedbacks</div>
                    </div>
                    <i class="bi bi-chat-left-quote-fill fs-1 text-white-50"></i>
                </div>
            </div>
             <a class="card-footer d-flex align-items-center justify-content-between text-white" href="admin_feedback.php">
                <span class="small">View Details</span>
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-calendar-check-fill fs-2 text-info me-3"></i>
                <div>
                    <div class="fs-4 fw-bold"><?php echo $total_trips_today; ?></div>
                    <div class="text-muted small">Trips Scheduled Today</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-truck-front-fill fs-2 text-secondary me-3"></i>
                <div>
                    <div class="fs-4 fw-bold"><?php echo $total_vehicles; ?></div>
                    <div class="text-muted small">Active Vehicles</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-person-badge fs-2 text-primary me-3"></i>
                <div>
                    <div class="fs-4 fw-bold"><?php echo $total_drivers; ?></div>
                    <div class="text-muted small">Total Drivers</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-bar-chart-line-fill me-1"></i>
                Trips Scheduled (Last 7 Days)
            </div>
            <div class="card-body">
                <canvas id="myBarChart" width="100%" height="40"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-clock-history me-1"></i>
                Pending User Feedback
            </div>
            <div class="card-body">
                <?php if ($recent_feedback && $recent_feedback->num_rows > 0): ?>
                    <ul class="list-group list-group-flush">
                        <?php while($row = $recent_feedback->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="admin_view_feedback.php?id=<?php echo $row['feedback_id']; ?>">
                                    <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                                </a>
                                <br>
                                <small class="text-muted">by <?php echo htmlspecialchars($row['username']); ?></small>
                            </div>
                            <a href="admin_view_feedback.php?id=<?php echo $row['feedback_id']; ?>" class="btn btn-sm btn-info">View</a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-center text-muted">No pending feedback. All caught up! 👍</p>
                <?php endif; ?>
            </div>
            <a class="card-footer d-flex align-items-center justify-content-between" href="admin_feedback.php">
                <span class="small">View All Feedback</span>
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>
</div>


<?php
// Footer đã có sẵn Chart.js
include 'admin_footer.php'; 
$conn->close();
?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("myBarChart");
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: "Trips",
                    backgroundColor: "rgba(0, 97, 242, 0.8)",
                    borderColor: "rgba(0, 97, 242, 1)",
                    borderWidth: 1,
                    data: <?php echo json_encode($chart_data); ?>,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 // Chỉ hiển thị số nguyên (1, 2, 3 chuyến)
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>