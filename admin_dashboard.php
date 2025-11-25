<?php
// admin_dashboard.php - Pro Version
include 'admin_header.php';
include 'db.php';

// 1. Lấy các con số thống kê
$total_revenue = $conn->query("SELECT SUM(amount) as total FROM payment_invoices")->fetch_assoc()['total'] ?? 0;
$total_tickets = $conn->query("SELECT COUNT(*) as total FROM monthly_tickets")->fetch_assoc()['total'];
$active_tickets = $conn->query("SELECT COUNT(*) as total FROM monthly_tickets WHERE status='active'")->fetch_assoc()['total'];
$pending_tickets = $conn->query("SELECT COUNT(*) as total FROM monthly_tickets WHERE status='pending'")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user'")->fetch_assoc()['total'];

// 2. Lấy 5 giao dịch mới nhất
$recent_trans = $conn->query("SELECT i.*, u.username FROM payment_invoices i JOIN monthly_tickets t ON i.ticket_id = t.ticket_id JOIN users u ON t.user_id = u.user_id ORDER BY i.payment_time DESC LIMIT 5");

// 3. Dữ liệu biểu đồ (DOANH THU THẬT TỪ DATABASE)
$chart_labels = [];
$revenue_map = [];

// Bước 1: Tạo khung 7 ngày mặc định là 0đ
for ($i = 6; $i >= 0; $i--) {
    $date_key = date('Y-m-d', strtotime("-$i days")); // Định dạng để khớp với SQL
    $label_key = date('d/m', strtotime("-$i days"));  // Định dạng hiển thị
    
    $chart_labels[] = $label_key;
    $revenue_map[$date_key] = 0; // Mặc định doanh thu bằng 0
}

// Bước 2: Query tổng tiền theo ngày từ bảng hóa đơn
$sql_chart = "SELECT DATE(payment_time) as p_date, SUM(amount) as total 
              FROM payment_invoices 
              WHERE payment_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
              GROUP BY DATE(payment_time)";
$result_chart = $conn->query($sql_chart);

// Bước 3: Lấp đầy dữ liệu thật vào khung
if ($result_chart->num_rows > 0) {
    while ($row = $result_chart->fetch_assoc()) {
        if (isset($revenue_map[$row['p_date']])) {
            $revenue_map[$row['p_date']] = (int)$row['total'];
        }
    }
}

// Bước 4: Tách dữ liệu để đưa vào Chart
$chart_data = array_values($revenue_map);
?>

<div class="dashboard-header">
    <div>
        <h2 class="fw-bold text-white mb-1">Dashboard Overview</h2>
        <p class="text-secondary mb-0">Welcome back, Admin!</p>
    </div>
    <div class="text-end text-white-50">
        <i class="bi bi-calendar3 me-2"></i> <?php echo date('l, F j, Y'); ?>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card-pro">
            <div class="stat-content">
                <h3><?php echo number_format($total_revenue); ?></h3>
                <p>Total Revenue (VND)</p>
            </div>
            <div class="stat-icon-box bg-gradient-green">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="stat-card-pro">
            <div class="stat-content">
                <h3><?php echo $total_tickets; ?></h3>
                <p>Total Passes Issued</p>
            </div>
            <div class="stat-icon-box bg-gradient-blue">
                <i class="bi bi-ticket-perforated"></i>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card-pro">
            <div class="stat-content">
                <h3 class="text-warning"><?php echo $pending_tickets; ?></h3>
                <p>Pending Approval</p>
            </div>
            <div class="stat-icon-box bg-gradient-orange">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card-pro">
            <div class="stat-content">
                <h3><?php echo $total_users; ?></h3>
                <p>Active Users</p>
            </div>
            <div class="stat-icon-box bg-gradient-purple">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="dashboard-panel">
            <div class="panel-header">
                <h5 class="panel-title"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Revenue Analytics (7 Days)</h5>
                <button class="btn btn-sm btn-outline-light rounded-pill px-3">Weekly</button>
            </div>
            <canvas id="revenueChart" height="120"></canvas>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="dashboard-panel">
            <div class="panel-header">
                <h5 class="panel-title"><i class="bi bi-pie-chart me-2 text-primary"></i>Ticket Status</h5>
            </div>
            <div style="height: 250px; display: flex; justify-content: center;">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="text-center mt-3 text-white-50 small">
                Total Active: <strong class="text-white"><?php echo $active_tickets; ?></strong>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="dashboard-panel">
            <div class="panel-header">
                <h5 class="panel-title"><i class="bi bi-clock-history me-2 text-info"></i>Recent Transactions</h5>
                <a href="#" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            
            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Trans ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_trans->num_rows > 0): ?>
                            <?php while($row = $recent_trans->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold text-white-50">#<?php echo $row['transaction_code']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:30px; height:30px;">
                                            <i class="bi bi-person-fill text-white small"></i>
                                        </div>
                                        <?php echo htmlspecialchars($row['username']); ?>
                                    </div>
                                </td>
                                <td class="fw-bold text-success">+<?php echo number_format($row['amount']); ?> đ</td>
                                <td><span class="badge bg-dark border border-secondary text-white-50"><?php echo $row['payment_method']; ?></span></td>
                                <td><?php echo date('H:i d/m', strtotime($row['payment_time'])); ?></td>
                                <td>
                                    <span class="status-dot dot-success"></span> Success
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No transactions yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$conn->close();
include 'admin_footer.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Revenue Chart (Bar)
    const ctxRev = document.getElementById('revenueChart');
    new Chart(ctxRev, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Revenue (VND)',
                data: <?php echo json_encode($chart_data); ?>,
                backgroundColor: '#10b981',
                borderRadius: 5,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            plugins: { 
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false } 
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#94a3b8' }
                },
                x: { 
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            }
        }
    });

    // 2. Status Chart (Doughnut)
    const ctxStat = document.getElementById('statusChart');
    new Chart(ctxStat, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Pending', 'Others'],
            datasets: [{
                data: [<?php echo $active_tickets; ?>, <?php echo $pending_tickets; ?>, <?php echo ($total_tickets - $active_tickets - $pending_tickets); ?>],
                backgroundColor: ['#3b82f6', '#f59e0b', '#64748b'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { color: '#cbd5e1', padding: 20 } }
            }
        }
    });
</script>