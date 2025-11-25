<?php
include 'user_header.php';
include 'db.php';

$sql_news = "SELECT id, title, content, created_at FROM announcements ORDER BY created_at DESC LIMIT 3";
$news_result = $conn->query($sql_news);
$news_items = []; if ($news_result) { while($row = $news_result->fetch_assoc()) { $news_items[] = $row; } }

$sql_routes = "SELECT route_id, route_name, ticket_price FROM routes ORDER BY route_id LIMIT 5";
$routes_result = $conn->query($sql_routes);
?>

<div class="hero-section">
    <div class="hero-bg"></div>
    <div class="hero-overlay">
        <div class="hero-content animate-fade-up">
            <h1 class="hero-title">Explore Hanoi City</h1>
            <p class="lead text-white-50 mb-5 fs-4">Public transport made simple, smart, and accessible.</p>
        </div>
        
        <div class="search-tabs-container text-start animate-fade-up" style="animation-delay: 0.2s;">
            <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pills-route"><i class="bi bi-search me-2"></i>Itinerary</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-path"><i class="bi bi-map me-2"></i>Path Finder</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-stop"><i class="bi bi-geo-alt me-2"></i>Bus Stop</button></li>
            </ul>
            
            <div class="tab-content pt-2">
                <div class="tab-pane fade show active" id="pills-route">
                    <form action="search_results.php" method="GET" class="d-flex gap-2">
                        <input type="text" class="form-control form-control-lg" name="route_id" placeholder="Enter Route ID (e.g., 32)..." required>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-arrow-right"></i></button>
                    </form>
                </div>
                <div class="tab-pane fade" id="pills-path">
                    <form action="find_path_action.php" method="POST" id="find-path-form" class="row g-2">
                        <div class="col-md-5 position-relative">
                            <input type="text" class="form-control form-control-lg" id="start_stop_name" name="start_stop_name" placeholder="Start Point..." required autocomplete="off">
                            <input type="hidden" id="start_stop_id" name="start_stop_id">
                            <div id="start-suggestions" class="list-group position-absolute w-100 mt-1 shadow rounded overflow-hidden" style="z-index: 2000;"></div>
                        </div>
                        <div class="col-md-5 position-relative">
                            <input type="text" class="form-control form-control-lg" id="end_stop_name" name="end_stop_name" placeholder="End Point..." required autocomplete="off">
                            <input type="hidden" id="end_stop_id" name="end_stop_id">
                            <div id="end-suggestions" class="list-group position-absolute w-100 mt-1 shadow rounded overflow-hidden" style="z-index: 2000;"></div>
                        </div>
                        <div class="col-md-2"><button type="submit" class="btn btn-success btn-lg w-100">Go</button></div>
                    </form>
                </div>
                <div class="tab-pane fade" id="pills-stop">
                    <form action="search_results.php" method="GET" class="d-flex gap-2">
                        <input type="text" class="form-control form-control-lg" name="search_term" placeholder="Find a street or stop..." required>
                        <button type="submit" class="btn btn-info btn-lg px-4 text-white"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="height: 80px;"></div>

<div class="container">
    
    <div class="row g-4 mb-5">
        <div class="col-12"><h4 class="text-white border-start border-4 border-primary ps-3 mb-4">Quick Services</h4></div>
        
        <div class="col-md-6 col-lg-3 animate-fade-up" style="animation-delay: 0.1s;">
            <a href="search_results.php?search_term=" class="service-card">
                <div class="service-icon"><i class="bi bi-map-fill"></i></div>
                <h5 class="service-title">All Routes</h5>
                <p class="service-desc">Browse full list of bus routes and schedules.</p>
            </a>
        </div>
        <div class="col-md-6 col-lg-3 animate-fade-up" style="animation-delay: 0.2s;">
            <a href="lost_and_found.php" class="service-card">
                <div class="service-icon"><i class="bi bi-box-seam-fill"></i></div>
                <h5 class="service-title">Lost & Found</h5>
                <p class="service-desc">Report lost items or check found property.</p>
            </a>
        </div>
        <div class="col-md-6 col-lg-3 animate-fade-up" style="animation-delay: 0.3s;">
            <a href="feedback.php" class="service-card">
                <div class="service-icon"><i class="bi bi-chat-quote-fill"></i></div>
                <h5 class="service-title">Feedback</h5>
                <p class="service-desc">Send complaints or suggestions to us.</p>
            </a>
        </div>
        <div class="col-md-6 col-lg-3 animate-fade-up" style="animation-delay: 0.4s;">
            <a href="chat.php" class="service-card">
                <div class="service-icon"><i class="bi bi-headset"></i></div>
                <h5 class="service-title">Support Chat</h5>
                <p class="service-desc">Chat directly with our support team.</p>
            </a>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="text-white border-start border-4 border-warning ps-3 m-0">Latest News</h4>
                <a href="announcements.php" class="text-primary small fw-bold">View All News &rarr;</a>
            </div>
            
            <div class="row g-4">
                <?php foreach ($news_items as $news): ?>
                    <div class="col-md-6">
                        <div class="news-card h-100">
                            <div class="news-img-wrapper">
                                <img src="img/news-default.jpg" class="news-img" alt="News">
                                <div class="position-absolute top-0 end-0 m-2"><span class="badge bg-danger shadow">HOT</span></div>
                            </div>
                            <div class="news-body d-flex flex-column">
                                <div class="text-muted small mb-2"><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($news['created_at'])); ?></div>
                                <h5 class="news-title text-white"><?php echo htmlspecialchars($news['title']); ?></h5>
                                <p class="text-muted small flex-grow-1"><?php echo htmlspecialchars(substr($news['content'], 0, 80)) . '...'; ?></p>
                                <button class="btn btn-sm btn-outline-primary w-100 rounded-pill mt-3" data-bs-toggle="modal" data-bs-target="#newsModal<?php echo $news['id']; ?>">Read Details</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($news_items)): ?><div class="col-12 text-muted">No news available.</div><?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <h4 class="text-white border-start border-4 border-info ps-3 mb-4">Popular Routes</h4>
            <div class="d-flex flex-column gap-3">
                <?php while($route = $routes_result->fetch_assoc()): ?>
                    <a href="search_results.php?route_id=<?php echo $route['route_id']; ?>" class="text-decoration-none">
                        <div class="route-card">
                            <div class="route-badge"><?php echo htmlspecialchars($route['route_id']); ?></div>
                            <div class="ms-3 flex-grow-1 overflow-hidden">
                                <div class="text-white fw-bold text-truncate"><?php echo htmlspecialchars($route['route_name']); ?></div>
                                <div class="text-success small fw-bold"><?php echo number_format($route['ticket_price']); ?>đ</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                    </a>
                <?php endwhile; ?>
                <a href="search_results.php?search_term=" class="btn btn-outline-light w-100 rounded-pill mt-2">View All Routes</a>
            </div>
            
            <div class="mt-4 rounded-4 overflow-hidden shadow-lg border border-secondary position-relative">
                <img src="img/ad-1.jpg" class="img-fluid w-100" alt="Ads" style="opacity: 0.8;">
            </div>
        </div>
    </div>
</div>

<?php 
$conn->close();
include 'user_footer.php'; 
?>

<?php foreach ($news_items as $news): ?>
<div class="modal fade" id="newsModal<?php echo $news['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-white"><?php echo htmlspecialchars($news['title']); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img src="img/news-default.jpg" class="img-fluid rounded mb-4 w-100 shadow" style="max-height: 350px; object-fit: cover;">
                <div class="d-flex align-items-center mb-3 text-white-50 small">
                    <i class="bi bi-calendar3 me-2"></i> <?php echo date('F d, Y \a\t H:i', strtotime($news['created_at'])); ?>
                </div>
                <div class="text-light" style="white-space: pre-wrap; line-height: 1.8; font-size: 1rem;"><?php echo nl2br(htmlspecialchars($news['content'])); ?></div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
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
                        item.classList.add('list-group-item', 'list-group-item-action', 'bg-dark', 'text-white', 'border-secondary');
                        item.innerHTML = suggestion.value;
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            inputField.value = suggestion.value;
                            hiddenField.value = suggestion.id;
                            suggestionsContainer.innerHTML = '';
                        });
                        suggestionsContainer.appendChild(item);
                    });
                }
            } catch (error) {}
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
</script>