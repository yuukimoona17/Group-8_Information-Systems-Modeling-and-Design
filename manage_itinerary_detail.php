<?php
// manage_itinerary_detail.php
include 'admin_header.php';
include 'db.php';

$route_id = $_GET['route_id'];

// Lấy tên tuyến xe
$sql_route = "SELECT route_name FROM routes WHERE route_id = ?";
$stmt_route = $conn->prepare($sql_route);
$stmt_route->bind_param("s", $route_id);
$stmt_route->execute();
$route_name = $stmt_route->get_result()->fetch_assoc()['route_name'];
$stmt_route->close();

// Lấy danh sách tất cả điểm dừng (cho dropdown)
$sql_all_stops = "SELECT stop_id, stop_name FROM bus_stops ORDER BY stop_name";
$result_all_stops = $conn->query($sql_all_stops);

// Hàm để lấy và hiển thị một lộ trình (lượt đi hoặc về)
function get_itinerary($conn, $route_id, $direction) {
    $sql = "SELECT i.itinerary_id, b.stop_name, i.stop_order 
            FROM itineraries i JOIN bus_stops b ON i.stop_id = b.stop_id 
            WHERE i.route_id = ? AND i.direction = ? ORDER BY i.stop_order";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $route_id, $direction);
    $stmt->execute();
    return $stmt->get_result();
}

$result_outbound = get_itinerary($conn, $route_id, 0); // Lượt đi
$result_inbound = get_itinerary($conn, $route_id, 1); // Lượt về
?>

<h1 class="mb-4">Manage Itinerary for Route: <?php echo htmlspecialchars($route_name); ?></h1>

<div class="card mb-4">
    <div class="card-header">Add Stop to Itinerary</div>
    <div class="card-body">
        <form action="add_stop_to_itinerary_action.php" method="POST">
            <input type="hidden" name="route_id" value="<?php echo htmlspecialchars($route_id); ?>">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Bus Stop</label>
                    <select name="stop_id" class="form-select" required>
                        <option value="">-- Select a Stop --</option>
                        <?php while($row = $result_all_stops->fetch_assoc()) {
                            echo "<option value='" . $row['stop_id'] . "'>" . htmlspecialchars($row['stop_name']) . "</option>";
                        } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Order</label>
                    <input type="number" name="stop_order" class="form-control" placeholder="e.g., 1, 2, 3..." required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Direction</label>
                    <select name="direction" class="form-select" required>
                        <option value="0">Outbound</option>
                        <option value="1">Inbound</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Add to Itinerary</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Outbound Itinerary (Direction 0)</div>
            <div class="card-body">
                <table class="table">
                    <thead><tr><th>Order</th><th>Stop Name</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php while($row = $result_outbound->fetch_assoc()) {
                            echo "<tr><td>" . $row['stop_order'] . "</td><td>" . htmlspecialchars($row['stop_name']) . "</td>";
                            echo "<td><a href='remove_stop_from_itinerary_action.php?id=" . $row['itinerary_id'] . "&route_id=" . urlencode($route_id) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\");'>Remove</a></td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Inbound Itinerary (Direction 1)</div>
            <div class="card-body">
                <table class="table">
                    <thead><tr><th>Order</th><th>Stop Name</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php while($row = $result_inbound->fetch_assoc()) {
                            echo "<tr><td>" . $row['stop_order'] . "</td><td>" . htmlspecialchars($row['stop_name']) . "</td>";
                            echo "<td><a href='remove_stop_from_itinerary_action.php?id=" . $row['itinerary_id'] . "&route_id=" . urlencode($route_id) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\");'>Remove</a></td></tr>";
                        } ?>
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