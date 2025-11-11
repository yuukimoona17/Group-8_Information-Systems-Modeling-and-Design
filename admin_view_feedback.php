<?php
// admin_view_feedback.php
include 'admin_header.php';
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: admin_feedback.php");
    exit();
}
$feedback_id = $_GET['id'];

// Tự động cập nhật status thành 'processing' (đang xử lý) nếu nó là 'new'
$sql_update_status = "UPDATE feedback SET status = 'processing' WHERE feedback_id = ? AND status = 'new'";
$stmt_update = $conn->prepare($sql_update_status);
$stmt_update->bind_param("i", $feedback_id);
$stmt_update->execute();
$stmt_update->close();


// Lấy chi tiết feedback (thêm cột admin_response)
$sql = "SELECT f.*, u.username 
        FROM feedback f
        JOIN users u ON f.user_id = u.user_id
        WHERE f.feedback_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $feedback_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Feedback not found.</div>";
    include 'admin_footer.php';
    exit();
}
$feedback = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Xác định badge
$status_badge = '';
if ($feedback['status'] == 'processing') $status_badge = 'badge bg-warning text-dark';
if ($feedback['status'] == 'resolved') $status_badge = 'badge bg-success';
?>

<h1 class="mb-4">Feedback Details</h1>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><?php echo htmlspecialchars($feedback['title']); ?></h5>
        <a href="admin_feedback.php" class="btn btn-secondary">Back to List</a>
    </div>
    <div class="card-body">
        <p>
            <strong>From User:</strong> <?php echo htmlspecialchars($feedback['username']); ?><br>
            <strong>Date:</strong> <?php echo $feedback['created_at']; ?><br>
            <strong>Type:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($feedback['feedback_type']); ?></span><br>
            <strong>Status:</strong> <span class="<?php echo $status_badge; ?>"><?php echo htmlspecialchars($feedback['status']); ?></span>
        </p>
        
        <hr>
        
        <h5>User's Content:</h5>
        <div class="p-3 bg-light border rounded" style="min-height: 150px; white-space: pre-wrap;">
            <?php echo nl2br(htmlspecialchars($feedback['content'])); ?>
        </div>
        
        <?php if ($feedback['status'] == 'resolved' && !empty($feedback['admin_response'])): ?>
            <hr>
            <h5>Admin's Response:</h5>
            <div class="p-3 bg-success bg-opacity-10 border border-success rounded" style="white-space: pre-wrap;">
                <?php echo nl2br(htmlspecialchars($feedback['admin_response'])); ?>
            </div>
        <?php endif; ?>
        
    </div>
    
    <?php if ($feedback['status'] != 'resolved'): ?>
    <div class="card-footer">
        <form action="admin_resolve_feedback_action.php" method="POST">
            <input type="hidden" name="feedback_id" value="<?php echo $feedback_id; ?>">
            <div class="mb-2">
                <label for="admin_response" class="form-label"><strong>Your Response (will be shown to user):</strong></label>
                <textarea name="admin_response" id="admin_response" class="form-control" rows="4" placeholder="Type your response to the user..." required></textarea>
            </div>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle-fill me-2"></i>Submit Response & Mark as Resolved
            </button>
        </form>
    </div>
    <?php endif; ?>
    
</div>


<?php
include 'admin_footer.php';
?>