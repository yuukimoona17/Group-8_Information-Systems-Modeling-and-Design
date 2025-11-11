<?php
// my_feedback.php
include 'user_header.php'; 
include 'db.php'; // <-- SỬA LỖI (THÊM DÒNG NÀY)

$user_id = $_SESSION['user_id'];

// SỬA LỖI (thêm admin_response vào câu SQL)
$sql = "SELECT title, content, feedback_type, status, created_at, admin_response 
        FROM feedback 
        WHERE user_id = ? 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Feedback History</h1>
    <a href="feedback.php" class="btn btn-primary">Send New Feedback</a>
</div>

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $status_badge = '';
        $status_text = '';
        if ($row['status'] == 'new') {
            $status_badge = 'bg-danger';
            $status_text = 'Sent (Chờ xử lý)';
        } elseif ($row['status'] == 'processing') {
            $status_badge = 'bg-warning text-dark';
            $status_text = 'Processing (Đang xử lý)';
        } elseif ($row['status'] == 'resolved') {
            $status_badge = 'bg-success';
            $status_text = 'Resolved (Đã giải quyết)';
        }
?>
        <div class="card bg-dark text-white mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?php echo htmlspecialchars($row['title']); ?></h5>
                <span class="badge <?php echo $status_badge; ?>"><?php echo $status_text; ?></span>
            </div>
            <div class="card-body">
                <p class="card-text"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                
                <?php if (!empty($row['admin_response'])): ?>
                <hr>
                <div class="p-3 rounded" style="background-color: #3a3f44;"> <strong><i class="bi bi-person-check-fill"></i> Admin Response:</strong>
                    <p class="mt-2 mb-0" style="white-space: pre-wrap;"><?php echo nl2br(htmlspecialchars($row['admin_response'])); ?></p>
                </div>
                <?php endif; ?>
                </div>
            <div class="card-footer text-muted">
                Sent on: <?php echo $row['created_at']; ?>
            </div>
        </div>
<?php
    } // Hết vòng lặp while
} else {
    echo "<div class='alert alert-info'>You have not sent any feedback yet.</div>";
}

$stmt->close();
$conn->close();
include 'user_footer.php'; 
?>