<?php
// find_path_action.php - Redesign Version
include 'user_header.php';
include 'db.php';

if (!isset($_POST['start_stop_id']) || !isset($_POST['end_stop_id'])) {
    echo "<script>alert('Please select start and end points'); window.location='index.php';</script>";
    exit();
}

$start_stop_id = $_POST['start_stop_id'];
$end_stop_id = $_POST['end_stop_id'];

// Lấy tên điểm dừng
$sql_names = "SELECT stop_id, stop_name FROM bus_stops WHERE stop_id IN (?, ?)";
$stmt_names = $conn->prepare($sql_names);
$stmt_names->bind_param("ii", $start_stop_id, $end_stop_id);
$stmt_names->execute();
$res = $stmt_names->get_result();
$stops = [];
while ($r = $res->fetch_assoc()) $stops[$r['stop_id']] = $r['stop_name'];

$start_name = $stops[$start_stop_id] ?? 'Start Point';
$end_name = $stops[$end_stop_id] ?? 'End Point';
?>

<div class="container mt-5 mb-5">
    <div class="fp-header-box animate-fade-up">
        <div class="text-white-50 text-uppercase small fw-bold mb-2">Path Finding Result</div>
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-3">
            <div class="fp-location"><i class="bi bi-geo-alt-fill text-success me-2"></i><?php echo htmlspecialchars($start_name); ?></div>
            <i class="bi bi-arrow-right text-white-50 fs-4 d-none d-md-block"></i>
            <i class="bi bi-arrow-down text-white-50 fs-4 d-md-none"></i>
            <div class="fp-location"><i class="bi bi-geo-alt-fill text-danger me-2"></i><?php echo htmlspecialchars($end_name); ?></div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php
            $has_result = false;

            // --- CASE 1: ĐI THẲNG (DIRECT) ---
            $sql1 = "SELECT t1.route_id FROM itineraries t1 
                     JOIN itineraries t2 ON t1.route_id = t2.route_id 
                     WHERE t1.stop_id = ? AND t2.stop_id = ? AND t1.direction = t2.direction 
                     AND t1.stop_order < t2.stop_order"; // Đảm bảo chiều đi đúng
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("ii", $start_stop_id, $end_stop_id);
            $stmt1->execute();
            $res1 = $stmt1->get_result();

            if ($res1->num_rows > 0) {
                $has_result = true;
                echo '<div class="fp-section-title text-success"><i class="bi bi-stars me-2"></i>Best Option: Direct Trip</div>';
                
                while ($row = $res1->fetch_assoc()) {
                    echo '
                    <div class="fp-card animate-fade-up">
                        <div class="fp-card-body">
                            <div class="fp-timeline">
                                <div class="fp-step">
                                    <div class="fp-icon bg-success"><i class="bi bi-geo-alt"></i></div>
                                    <div class="fp-content">
                                        <div class="fp-label">Start at</div>
                                        <div class="fp-value">'.htmlspecialchars($start_name).'</div>
                                    </div>
                                </div>
                                <div class="fp-route-line">
                                    <div class="fp-route-badge">
                                        <i class="bi bi-bus-front-fill me-2"></i>Take Route <strong>'.$row['route_id'].'</strong>
                                    </div>
                                </div>
                                <div class="fp-step">
                                    <div class="fp-icon bg-danger"><i class="bi bi-flag-fill"></i></div>
                                    <div class="fp-content">
                                        <div class="fp-label">Arrive at</div>
                                        <div class="fp-value">'.htmlspecialchars($end_name).'</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            }

            // --- CASE 2: CHUYỂN TUYẾN (1 TRANSFER) ---
            // Chỉ tìm nếu không có chuyến đi thẳng (hoặc hiển thị cả 2 tuỳ m, ở đây t hiển thị tiếp bên dưới)
            $sql2 = "SELECT 
                        r1.route_id AS route1, 
                        r2.route_id AS route2, 
                        bs.stop_name AS transfer_stop 
                    FROM itineraries i1
                    JOIN itineraries i2 ON i1.route_id = i2.route_id
                    JOIN itineraries i3 ON i2.stop_id = i3.stop_id
                    JOIN itineraries i4 ON i3.route_id = i4.route_id
                    JOIN routes r1 ON i1.route_id = r1.route_id
                    JOIN routes r2 ON i4.route_id = r2.route_id
                    JOIN bus_stops bs ON i2.stop_id = bs.stop_id 
                    WHERE i1.stop_id = ? AND i4.stop_id = ? 
                    AND i1.route_id != i4.route_id
                    AND i1.stop_order < i2.stop_order -- Đảm bảo thứ tự đúng
                    GROUP BY r1.route_id, r2.route_id 
                    LIMIT 5";
            
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ii", $start_stop_id, $end_stop_id);
            $stmt2->execute();
            $res2 = $stmt2->get_result();

            if ($res2->num_rows > 0) {
                echo '<div class="fp-section-title text-warning mt-5"><i class="bi bi-shuffle me-2"></i>Option: 1 Transfer</div>';
                
                while ($row = $res2->fetch_assoc()) {
                    $has_result = true;
                    echo '
                    <div class="fp-card animate-fade-up">
                        <div class="fp-card-body">
                            <div class="fp-timeline">
                                <div class="fp-step">
                                    <div class="fp-icon bg-success"><i class="bi bi-1-circle"></i></div>
                                    <div class="fp-content">
                                        <div class="fp-label">Start & Take Bus</div>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="badge bg-primary fs-6">Route '.$row['route1'].'</span>
                                            <span class="ms-2 text-white-50 small">from '.htmlspecialchars($start_name).'</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="fp-step mt-4">
                                    <div class="fp-icon bg-warning text-dark"><i class="bi bi-arrow-left-right"></i></div>
                                    <div class="fp-content">
                                        <div class="fp-label text-warning">Transfer Point</div>
                                        <div class="fp-value">'.htmlspecialchars($row['transfer_stop']).'</div>
                                        <div class="small text-white-50 mt-1">Get off Route '.$row['route1'].' and wait for Route '.$row['route2'].'</div>
                                    </div>
                                </div>

                                <div class="fp-step mt-4">
                                    <div class="fp-icon bg-info text-dark"><i class="bi bi-2-circle"></i></div>
                                    <div class="fp-content">
                                        <div class="fp-label">Next Bus</div>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="badge bg-info text-dark fs-6">Route '.$row['route2'].'</span>
                                            <span class="ms-2 text-white-50 small">to destination</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="fp-step mt-4">
                                    <div class="fp-icon bg-danger"><i class="bi bi-flag-fill"></i></div>
                                    <div class="fp-content">
                                        <div class="fp-label">Arrive at</div>
                                        <div class="fp-value">'.htmlspecialchars($end_name).'</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            }

            if (!$has_result) {
                echo '
                <div class="text-center py-5">
                    <i class="bi bi-emoji-frown text-white-50" style="font-size: 4rem;"></i>
                    <h4 class="text-white mt-3">No Path Found</h4>
                    <p class="text-white-50">We could not find a suitable route between these stops.</p>
                    <a href="index.php" class="btn btn-outline-light rounded-pill px-4 mt-2">Try another search</a>
                </div>';
            }
            ?>
        </div>
    </div>
    
    <div class="text-center mt-5">
        <a href="index.php" class="btn btn-secondary rounded-pill px-4 shadow"><i class="bi bi-arrow-left me-2"></i>Back to Search</a>
    </div>
</div>

<?php include 'user_footer.php'; ?>