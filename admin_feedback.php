<?php
// admin_feedback.php
include 'admin_header.php';
include 'db.php';

if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . $_SESSION['flash_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

$sql = "SELECT f.feedback_id, f.title, f.content, f.feedback_type, f.status, f.created_at, u.username 
        FROM feedback f
        JOIN users u ON f.user_id = u.user_id
        ORDER BY f.status = 'new' DESC, f.created_at DESC";
$result = $conn->query($sql);
?>

<h1 class="mb-4">User Feedback Management</h1>

<div class="card">
    <div class="card-header">List of Feedback (Newest first)</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>From User</th>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Content (Snippet)</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $status_badge = '';
                            if ($row['status'] == 'new') $status_badge = 'badge bg-danger';
                            if ($row['status'] == 'processing') $status_badge = 'badge bg-warning text-dark';
                            if ($row['status'] == 'resolved') $status_badge = 'badge bg-success';
                            
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["feedback_type"]) . "</td>";
                            echo "<td><strong>" . htmlspecialchars($row["title"]) . "</strong></td>";
                            echo "<td>" . htmlspecialchars(substr($row["content"], 0, 100)) . "...</td>";
                            echo "<td>" . $row["created_at"] . "</td>";
                            echo "<td><span class='" . $status_badge . "'>" . $row["status"] . "</span></td>";
                            echo "<td class='text-end text-nowrap'>";
                            
                            // --- SỬA CHỖ NÀY ---
                            echo "<a href='admin_view_feedback.php?id=" . $row["feedback_id"] . "' class='btn btn-sm btn-info'>View Details</a> ";
                            
                            // Chỉ giữ lại nút Mark Processing
                            if ($row['status'] == 'new') {
                                echo "<a href='update_feedback_status_action.php?id=" . $row["feedback_id"] . "&status=processing' class='btn btn-sm btn-warning'>Mark Processing</a> ";
                            }
                            // --- KẾT THÚC SỬA ---
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No feedback found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
include 'admin_footer.php';
?>