<?php
// announcements.php
include 'user_header.php';
include 'db.php';

$sql = "SELECT id, title, content, created_at FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="text-white fw-bold border-start border-4 border-warning ps-3">News & Announcements</h2>
        <p class="text-muted ms-4 mb-0">Stay updated with the latest information.</p>
    </div>
    <a href="index.php" class="btn btn-outline-light"><i class="bi bi-arrow-left me-2"></i>Back Home</a>
</div>

<div class="row g-4">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4">
                <div class="news-card h-100">
                    <div class="news-img-wrapper">
                        <img src="img/news-default.jpg" class="news-img" alt="News">
                    </div>
                    <div class="news-body d-flex flex-column">
                        <div class="news-date small text-white-50 mb-2"><i class="bi bi-calendar3 me-1"></i> <?php echo date('M d, Y', strtotime($row['created_at'])); ?></div>
                        <h5 class="news-title text-white"><?php echo htmlspecialchars($row['title']); ?></h5>
                        <p class="news-excerpt text-white-50 small flex-grow-1">
                            <?php echo htmlspecialchars(substr($row['content'], 0, 100)) . '...'; ?>
                        </p>
                        <button class="btn btn-sm btn-outline-primary w-100 mt-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#newsPageModal<?php echo $row['id']; ?>">Read Full Story</button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="newsPageModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <img src="img/news-default.jpg" class="img-fluid rounded mb-4 w-100 shadow" style="max-height: 350px; object-fit: cover;">
                            <div class="text-light" style="white-space: pre-wrap; line-height: 1.8;"><?php echo nl2br(htmlspecialchars($row['content'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12"><div class="alert alert-info bg-transparent border-info text-info">No announcements yet.</div></div>
    <?php endif; ?>
</div>

<?php
$conn->close();
include 'user_footer.php';
?>