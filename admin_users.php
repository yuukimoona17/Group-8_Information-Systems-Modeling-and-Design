<?php
// admin_users.php
include 'admin_header.php';
include 'db.php';

// Lấy tất cả user trừ admin hiện tại để không tự xóa mình
$current_admin_id = $_SESSION['user_id'];
$sql = "SELECT user_id, username, role FROM users WHERE user_id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_admin_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1 class="mb-4">User Management</h1>

<?php
// Hiển thị Flash Message (nếu có)
if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . $_SESSION['flash_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}
?>

<div class="card">
    <div class="card-header">List of Users</div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["user_id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                        echo "<td>";
                        // Form để thay đổi vai trò
                        echo "<form action='update_user_role_action.php' method='POST' class='d-inline-flex'>";
                        echo "<input type='hidden' name='user_id' value='" . $row['user_id'] . "'>";
                        echo "<select name='role' class='form-select form-select-sm' onchange='this.form.submit()'>";
                        echo "<option value='user' " . ($row['role'] == 'user' ? 'selected' : '') . ">User</option>";
                        echo "<option value='admin' " . ($row['role'] == 'admin' ? 'selected' : '') . ">Admin</option>";
                        // CODE MỚI THÊM VÀO
                        echo "<option value='driver' " . ($row['role'] == 'driver' ? 'selected' : '') . ">Driver</option>";
                        // KẾT THÚC CODE MỚI
                        echo "</select>";
                        echo "</form>";
                        echo "</td>";
                        echo "<td class='text-end'>";
                        // Nút xóa user
                        echo "<a href='delete_user_action.php?id=" . $row["user_id"] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this user?\");'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No other users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include 'admin_footer.php';
?>