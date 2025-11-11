<?php
// find_path_action.php
include 'user_header.php';

include 'db.php';

if (!isset($_POST['start_stop_id']) || !isset($_POST['end_stop_id'])) {
    die("Error: Please select a start and end point.");
}

$start_stop_id = $_POST['start_stop_id'];
$end_stop_id = $_POST['end_stop_id'];
$result_html = "";

// Lấy tên điểm đi và điểm đến
$sql_stops_names = "SELECT stop_id, stop_name FROM bus_stops WHERE stop_id IN (?, ?)";
$stmt_names = $conn->prepare($sql_stops_names);
$stmt_names->bind_param("ii", $start_stop_id, $end_stop_id);
$stmt_names->execute();
$stops_result = $stmt_names->get_result();
$stops = [];
while ($row = $stops_result->fetch_assoc()) {
    $stops[$row['stop_id']] = $row['stop_name'];
}
$start_stop_name = $stops[$start_stop_id] ?? 'Unknown';
$end_stop_name = $stops[$end_stop_id] ?? 'Unknown';

$page_title = "Path from '" . htmlspecialchars($start_stop_name) . "' to '" . htmlspecialchars($end_stop_name) . "'";

// --- KỊCH BẢN 1: TÌM ĐƯỜNG ĐI THẲNG ---
$sql_direct = "SELECT t1.route_id FROM itineraries t1 
                JOIN itineraries t2 ON t1.route_id = t2.route_id 
                WHERE t1.stop_id = ? AND t2.stop_id = ? AND t1.direction = t2.direction";
$stmt_direct = $conn->prepare($sql_direct);
$stmt_direct->bind_param("ii", $start_stop_id, $end_stop_id);
$stmt_direct->execute();
$direct_result = $stmt_direct->get_result();

if ($direct_result->num_rows > 0) {
    $result_html .= "<h3>Direct Trip (No transfer needed):</h3><ul class='list-group'>";
    while ($row = $direct_result->fetch_assoc()) {
        $result_html .= "<li class='list-group-item'>Take route <strong>" . htmlspecialchars($row['route_id']) . "</strong>.</li>";
    }
    $result_html .= "</ul>";
} else {
    // --- KỊCH BẢN 2: TÌM ĐƯỜNG 1 LẦN CHUYỂN ---
    $sql_transfer = "SELECT r1.route_id AS route1, r2.route_id AS route2, bs.stop_name AS transfer_stop
                    FROM itineraries i1
                    JOIN itineraries i2 ON i1.route_id = i2.route_id
                    JOIN itineraries i3 ON i2.stop_id = i3.stop_id
                    JOIN itineraries i4 ON i3.route_id = i4.route_id
                    JOIN routes r1 ON i1.route_id = r1.route_id
                    JOIN routes r2 ON i4.route_id = r2.route_id
                    JOIN bus_stops bs ON i2.stop_id = bs.stop_id
                    WHERE i1.stop_id = ? AND i4.stop_id = ? AND i1.route_id != i4.route_id
                    LIMIT 5";

    $stmt_transfer = $conn->prepare($sql_transfer);
    $stmt_transfer->bind_param("ii", $start_stop_id, $end_stop_id);
    $stmt_transfer->execute();
    $transfer_result = $stmt_transfer->get_result();
    
    if ($transfer_result->num_rows > 0) {
        $result_html .= "<h3>1 Transfer Trip:</h3><ul class='list-group'>";
        while ($row = $transfer_result->fetch_assoc()) {
            $result_html .= "<li class='list-group-item'>1. Take route <strong>" . htmlspecialchars($row['route1']) . "</strong> to <strong>" . htmlspecialchars($row['transfer_stop']) . "</strong>.<br>2. Transfer to route <strong>" . htmlspecialchars($row['route2']) . "</strong>.</li>";
        }
        $result_html .= "</ul>";
    } else {
        $result_html = "<p class='alert alert-danger'>Sorry, no suitable path found.</p>";
    }
}
$conn->close();
?>

<div class="card">
    <div class="card-header">
        <h2><?php echo $page_title; ?></h2>
    </div>
    <div class="card-body">
        <?php echo $result_html; ?>
        <a href="index.php" class="btn btn-secondary mt-4">Search Again</a>
    </div>
</div>

<?php include 'user_footer.php'; ?>