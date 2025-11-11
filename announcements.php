<?php
// announcements.php
include 'user_header.php'; // Đã đổi sang header của user
include 'db.php';

$sql = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>All Announcements</h1>
    <a href="index.php" class="btn btn-secondary">Back to Home</a>
</div>

<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="card bg-dark text-white mb-3">
            <div class="card-header">
                <h5 class="mb-0"><?php echo htmlspecialchars($row['title']); ?></h5>
            </div>
            <div class="card-body">
                <p class="card-text"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                <p class="card-text"><small class="text-muted">Posted on: <?php echo $row['created_at']; ?></small></p>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p class="alert alert-info">There are no announcements yet.</p>
<?php endif; ?>

<?php
$conn->close();
include 'user_footer.php'; // Đã đổi sang footer của user
?>