<?php
// search_results.php
include 'user_header.php'; // Đã đổi sang header của user
include 'db.php';

$page_title = "Search Results";
$search_results_html = "";

// ... (toàn bộ code xử lý logic tìm kiếm giữ nguyên như cũ) ...
if (isset($_GET['route_id']) && !empty($_GET['route_id'])) {
    $route_id = $_GET['route_id'];
    $page_title = "Itinerary for Route " . htmlspecialchars($route_id);
    $sql = "SELECT i.stop_order, b.stop_name, b.street, i.direction FROM itineraries i JOIN bus_stops b ON i.stop_id = b.stop_id WHERE i.route_id = ? ORDER BY i.direction, i.stop_order";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $route_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $search_results_html .= "<h3>Outbound Itinerary</h3><ul class='list-group'>";
        $has_inbound = false;
        while($row = $result->fetch_assoc()) {
            if ($row['direction'] == 1 && !$has_inbound) {
                $search_results_html .= "</ul><h3 class='mt-4'>Inbound Itinerary</h3><ul class='list-group'>";
                $has_inbound = true;
            }
            $search_results_html .= "<li class='list-group-item'>" . $row['stop_order'] . ". " . htmlspecialchars($row['stop_name']) . " <small class='text-muted'>(" . htmlspecialchars($row['street']) . ")</small></li>";
        }
        $search_results_html .= "</ul>";
    } else { $search_results_html = "<p class='alert alert-warning'>No itinerary found for this route ID.</p>"; }
    $stmt->close();
}
elseif (isset($_GET['search_term']) && !empty($_GET['search_term'])) {
    $search_term = $_GET['search_term'];
    $page_title = "Routes passing through '" . htmlspecialchars($search_term) . "'";
    $search_query = "%" . $search_term . "%";
    $sql = "SELECT DISTINCT r.route_id, r.route_name FROM routes r JOIN itineraries i ON r.route_id = i.route_id JOIN bus_stops bs ON i.stop_id = bs.stop_id WHERE bs.stop_name LIKE ? OR bs.street LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search_query, $search_query);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $search_results_html .= "<ul class='list-group'>";
        while($row = $result->fetch_assoc()) {
            $search_results_html .= "<li class='list-group-item'><a href='search_results.php?route_id=" . htmlspecialchars($row['route_id']) . "'><strong>" . htmlspecialchars($row['route_id']) . "</strong>: " . htmlspecialchars($row['route_name']) . "</a></li>";
        }
        $search_results_html .= "</ul>";
    } else { $search_results_html = "<p class='alert alert-warning'>No routes found for this location.</p>"; }
    $stmt->close();
}
else {
    $search_results_html = "<p class='alert alert-info'>Please enter a search term.</p>";
}

$conn->close();
?>

<div class="card">
    <div class="card-header">
        <h2><?php echo $page_title; ?></h2>
    </div>
    <div class="card-body">
        <?php echo $search_results_html; ?>
        <a href="index.php" class="btn btn-secondary mt-4">Back to Search</a>
    </div>
</div>

<?php include 'user_footer.php'; // Đã đổi sang footer của user ?>