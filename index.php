<?php
// index.php - Phiên bản cuối cùng với Autocomplete
include 'user_header.php';
include 'db.php';

// Lấy thông báo mới nhất
$sql_announcement = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC LIMIT 1";
$announcement_result = $conn->query($sql_announcement);
$latest_announcement = $announcement_result->fetch_assoc();

// Lấy danh sách tất cả các tuyến xe
$sql_routes = "SELECT route_id, route_name, ticket_price FROM routes ORDER BY route_id";
$routes_result = $conn->query($sql_routes);

// Không cần lấy $all_stops_result ở đây nữa vì Autocomplete sẽ tự tìm
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <?php
    // Hiển thị thông báo nếu có (với Read more/Collapse)
    if ($latest_announcement):
        $character_limit = 200;
        $content = $latest_announcement['content'];
    ?>
        <div class="alert alert-info">
            <h4 class="alert-heading"><?php echo htmlspecialchars($latest_announcement['title']); ?></h4>
            <?php
            if (strlen($content) > $character_limit) {
                $visible_text = substr($content, 0, $character_limit);
                echo '<div id="announcement-summary">';
                echo nl2br(htmlspecialchars($visible_text));
                echo '... <a data-bs-toggle="collapse" href="#collapseAnnouncement" role="button" id="read-more-link">Read more...</a>';
                echo '</div>';
                echo '<div class="collapse" id="collapseAnnouncement">';
                echo nl2br(htmlspecialchars($content));
                echo ' <a data-bs-toggle="collapse" href="#collapseAnnouncement" role="button" id="collapse-link">Collapse</a>';
                echo '</div>';
            } else { echo nl2br(htmlspecialchars($content)); }
            ?>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0"><small>Posted on: <?php echo $latest_announcement['created_at']; ?></small></p>
                <a href="announcements.php" class="alert-link">View all announcements</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="card mt-4">
        <div class="card-header">
            <h3>Bus Route Search Tools</h3>
        </div>
        <div class="card-body">
            <form action="search_results.php" method="GET" class="mb-4">
                <h5>Search Itinerary by Route ID</h5>
                <div class="input-group">
                    <input type="text" class="form-control" name="route_id" placeholder="Enter Route ID (e.g., R01)" required>
                    <button type="submit" class="btn btn-primary">Find Itinerary</button>
                </div>
            </form>

            <form action="search_results.php" method="GET" class="mb-4">
                <h5>Find Routes by Street/Landmark</h5>
                <div class="input-group">
                    <input type="text" class="form-control" name="search_term" placeholder="Enter a street or stop name..." required>
                    <button type="submit" class="btn btn-primary">Find Routes</button>
                </div>
            </form>

            <form action="find_path_action.php" method="POST" id="find-path-form">
                <h5 class="mt-4">Find Path Between Two Stops (Type to search)</h5>
                <div class="mb-3 position-relative">
                    <label for="start_stop_name" class="form-label">Start Point:</label>
                    <input type="text" class="form-control" id="start_stop_name" name="start_stop_name" placeholder="Type start stop name..." required autocomplete="off">
                    <input type="hidden" id="start_stop_id" name="start_stop_id">
                    <div id="start-suggestions" class="list-group position-absolute w-100 mt-1" style="z-index: 1000;"></div>
                </div>
                <div class="mb-3 position-relative">
                    <label for="end_stop_name" class="form-label">End Point:</label>
                    <input type="text" class="form-control" id="end_stop_name" name="end_stop_name" placeholder="Type end stop name..." required autocomplete="off">
                    <input type="hidden" id="end_stop_id" name="end_stop_id">
                    <div id="end-suggestions" class="list-group position-absolute w-100 mt-1" style="z-index: 1000;"></div>
                </div>
                <button type="submit" class="btn btn-success w-100">Find Path</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
       <div class="card-header">
            <h3>All Bus Routes</h3>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Route ID</th>
                        <th>Route Name</th>
                        <th>Price (VND)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($routes_result && $routes_result->num_rows > 0) {
                        while($row = $routes_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><strong>" . htmlspecialchars($row["route_id"]) . "</strong></td>";
                            echo "<td><a href='search_results.php?route_id=" . htmlspecialchars($row["route_id"]) . "'>" . htmlspecialchars($row["route_name"]) . "</a></td>";
                            echo "<td>" . (isset($row["ticket_price"]) ? number_format($row["ticket_price"]) : 'N/A') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No routes found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
include 'user_footer.php';
?>

<script>
    var collapseElement = document.getElementById('collapseAnnouncement');
    var summaryElement = document.getElementById('announcement-summary');
    if (collapseElement) {
        collapseElement.addEventListener('show.bs.collapse', function () { if(summaryElement) summaryElement.style.display = 'none'; });
        collapseElement.addEventListener('hide.bs.collapse', function () { if(summaryElement) summaryElement.style.display = 'block'; });
    }
</script>

<script>
// Hàm xử lý Autocomplete
function setupAutocomplete(inputId, hiddenId, suggestionsId) {
    const inputField = document.getElementById(inputId);
    const hiddenField = document.getElementById(hiddenId);
    const suggestionsContainer = document.getElementById(suggestionsId);
    let debounceTimer;

    inputField.addEventListener('input', function() {
        const searchTerm = this.value;
        suggestionsContainer.innerHTML = '';
        hiddenField.value = '';
        clearTimeout(debounceTimer);
        if (searchTerm.length < 2) return;

        debounceTimer = setTimeout(async () => {
            try {
                const response = await fetch(`suggest_stops.php?term=${encodeURIComponent(searchTerm)}`);
                const suggestions = await response.json();
                suggestionsContainer.innerHTML = '';

                if (suggestions.length > 0) {
                    suggestions.forEach(suggestion => {
                        const item = document.createElement('a');
                        item.href = '#';
                        item.classList.add('list-group-item', 'list-group-item-action');
                        const regex = new RegExp(`(${searchTerm})`, 'gi');
                        item.innerHTML = suggestion.value.replace(regex, '<strong>$1</strong>');
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            inputField.value = suggestion.value;
                            hiddenField.value = suggestion.id;
                            suggestionsContainer.innerHTML = '';
                        });
                        suggestionsContainer.appendChild(item);
                    });
                } else {
                     suggestionsContainer.innerHTML = '<span class="list-group-item disabled text-muted">No suggestions found</span>';
                }
            } catch (error) {
                console.error('Autocomplete error:', error);
                 suggestionsContainer.innerHTML = '<span class="list-group-item disabled text-danger">Error fetching suggestions</span>';
            }
        }, 300);
    });
    document.addEventListener('click', function(e) {
        if (!suggestionsContainer.contains(e.target) && e.target !== inputField) {
            suggestionsContainer.innerHTML = '';
        }
    });
}
setupAutocomplete('start_stop_name', 'start_stop_id', 'start-suggestions');
setupAutocomplete('end_stop_name', 'end_stop_id', 'end-suggestions');
document.getElementById('find-path-form').addEventListener('submit', function(e) {
    if (!document.getElementById('start_stop_id').value || !document.getElementById('end_stop_id').value) {
        alert('Please select a valid start and end stop from the suggestions.');
        e.preventDefault();
    }
});
</script>