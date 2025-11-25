<?php
// search_results.php - PRO UI UPDATE
include 'user_header.php';
include 'db.php';

$page_title = "Search Results";
$results_content = "";

// LOGIC 1: Xem chi tiết 1 tuyến (Itinerary)
if (isset($_GET['route_id']) && !empty($_GET['route_id'])) {
    $route_id = $_GET['route_id'];
    $page_title = "Itinerary: Route " . htmlspecialchars($route_id);
    
    $sql = "SELECT i.stop_order, b.stop_name, b.street, i.direction 
            FROM itineraries i JOIN bus_stops b ON i.stop_id = b.stop_id 
            WHERE i.route_id = ? ORDER BY i.direction, i.stop_order";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $route_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Chia 2 cột: Lượt đi & Lượt về
        $outbound = [];
        $inbound = [];
        while($row = $result->fetch_assoc()) {
            if ($row['direction'] == 0) $outbound[] = $row;
            else $inbound[] = $row;
        }
        
        $results_content .= "<div class='row'>";
        
        // Cột Lượt đi
        $results_content .= "<div class='col-md-6 mb-4'><div class='content-card h-100'><div class='card-header bg-transparent border-bottom border-secondary text-success fw-bold py-3'><i class='bi bi-arrow-right-circle-fill me-2'></i> Outbound</div><div class='card-body p-0'><ul class='list-group list-group-flush'>";
        foreach ($outbound as $stop) {
            $results_content .= "<li class='list-group-item bg-transparent text-white border-secondary d-flex py-3'><span class='badge bg-secondary me-3 align-self-center'>" . $stop['stop_order'] . "</span><div><strong class='text-primary'>" . htmlspecialchars($stop['stop_name']) . "</strong><br><small class='text-muted'>" . htmlspecialchars($stop['street']) . "</small></div></li>";
        }
        $results_content .= "</ul></div></div></div>";
        
        // Cột Lượt về
        $results_content .= "<div class='col-md-6 mb-4'><div class='content-card h-100'><div class='card-header bg-transparent border-bottom border-secondary text-info fw-bold py-3'><i class='bi bi-arrow-left-circle-fill me-2'></i> Inbound</div><div class='card-body p-0'><ul class='list-group list-group-flush'>";
        foreach ($inbound as $stop) {
            $results_content .= "<li class='list-group-item bg-transparent text-white border-secondary d-flex py-3'><span class='badge bg-secondary me-3 align-self-center'>" . $stop['stop_order'] . "</span><div><strong class='text-info'>" . htmlspecialchars($stop['stop_name']) . "</strong><br><small class='text-muted'>" . htmlspecialchars($stop['street']) . "</small></div></li>";
        }
        $results_content .= "</ul></div></div></div>";
        
        $results_content .= "</div>";
        
    } else { 
        $results_content = "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle me-2'></i> No itinerary found for Route <strong>" . htmlspecialchars($route_id) . "</strong>.</div>"; 
    }
    $stmt->close();
}

// LOGIC 2: Danh sách tuyến (All Routes) - PHẦN BẠN MUỐN SỬA ĐẸP
elseif (isset($_GET['search_term']) || isset($_GET['search_term_empty'])) { 
    $search_term = $_GET['search_term'] ?? '';
    $page_title = empty($search_term) ? "All Bus Routes" : "Routes: '" . htmlspecialchars($search_term) . "'";
    
    $search_query = "%" . $search_term . "%";
    
    if (empty($search_term)) {
        $sql = "SELECT route_id, route_name, ticket_price FROM routes ORDER BY route_id";
        $stmt = $conn->prepare($sql);
    } else {
        $sql = "SELECT DISTINCT r.route_id, r.route_name, r.ticket_price 
                FROM routes r 
                JOIN itineraries i ON r.route_id = i.route_id 
                JOIN bus_stops bs ON i.stop_id = bs.stop_id 
                WHERE bs.stop_name LIKE ? OR bs.street LIKE ?
                ORDER BY r.route_id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $search_query, $search_query);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $results_content .= "<div class='row g-4'>"; // Thêm g-4 cho khoảng cách đều
        while($row = $result->fetch_assoc()) {
            // --- SỬA: Dùng class 'service-card' để đẹp như trang chủ ---
            $results_content .= "
            <div class='col-md-6 col-lg-4'>
                <a href='search_results.php?route_id=" . htmlspecialchars($row['route_id']) . "' class='service-card text-decoration-none d-flex flex-column h-100 p-4'>
                    <div class='d-flex justify-content-between align-items-start mb-3 w-100'>
                        <span class='badge bg-primary fs-5 px-3 py-2 rounded-pill shadow'>" . htmlspecialchars($row['route_id']) . "</span>
                        <span class='text-warning fw-bold'>" . number_format($row['ticket_price']) . " đ</span>
                    </div>
                    <h5 class='service-title text-start mb-2'>" . htmlspecialchars($row['route_name']) . "</h5>
                    <p class='service-desc text-start mt-auto mb-0'>Click to view full itinerary <i class='bi bi-arrow-right ms-1'></i></p>
                </a>
            </div>";
            // --- KẾT THÚC SỬA ---
        }
        $results_content .= "</div>";
    } else { 
        $results_content = "<div class='alert alert-warning'><i class='bi bi-exclamation-circle me-2'></i> No routes found matching your search.</div>"; 
    }
    $stmt->close();
}
else {
    $results_content = "<div class='alert alert-info'>Please enter a search term.</div>";
}

$conn->close();
?>

<div class="container mt-5"> <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="text-white border-start border-4 border-primary ps-3 fw-bold"><?php echo $page_title; ?></h2>
        <a href="index.php" class="btn btn-outline-light rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>

    <?php echo $results_content; ?>
</div>

<?php include 'user_footer.php'; ?>