<?php
// ...
session_start(); // Nếu file chưa có session_start
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = "Please login to access this feature.";
    $_SESSION['flash_message_type'] = "warning";
    header("Location: login.php");
    exit();
}
// ...
include 'user_header.php';
?>

<h1 class="mb-4">Send Feedback or Report</h1>

<?php
// Hiển thị thông báo (nếu có)
if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    echo '<div class="alert alert-' . $message_type . '">' . $_SESSION['flash_message'] . '</div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}
?>

<div class="card">
    <div class="card-header">
        Your feedback is important to us
    </div>
    <div class="card-body">
        <form action="feedback_action.php" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required placeholder="e.g., Driver was rude">
            </div>
            
            <div class="mb-3">
                <label for="feedback_type" class="form-label">Feedback Type</label>
                <select class="form-select" id="feedback_type" name="feedback_type" required>
                    <option value="complaint">Complaint (Phàn nàn)</option>
                    <option value="suggestion">Suggestion (Góp ý)</option>
                    <option value="report">Report (Báo cáo sự cố)</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required placeholder="Please provide details..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Feedback</button>
        </form>
    </div>
</div>


<?php
include 'user_footer.php';
?>