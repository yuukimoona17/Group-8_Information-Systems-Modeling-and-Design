<?php
include 'admin_header.php';
include 'db.php';

// Xử lý logic xóa nếu có yêu cầu
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM announcements WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $delete_id);
    if ($stmt_delete->execute()) {
        $_SESSION['flash_message'] = "Announcement deleted successfully.";
        $_SESSION['flash_message_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Error deleting announcement.";
        $_SESSION['flash_message_type'] = "danger";
    }
    $stmt_delete->close();
    header("Location: admin_announcements.php");
    exit();
}

// Lấy tất cả thông báo để hiển thị
$sql = "SELECT id, title, content, created_at FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<h1 class="mb-4">Announcements Management</h1>

<?php
if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . $_SESSION['flash_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}
?>

<div class="card mb-4">
    <div class="card-header">Post New Announcement</div>
    <div class="card-body">
        <form action="add_announcement_action.php" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Announcement</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Posted Announcements</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Posted On</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . $row['created_at'] . "</td>";
                        echo "<td class='text-end'><a href='admin_announcements.php?delete_id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\");'>Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>No announcements posted yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
include 'admin_footer.php';
?>