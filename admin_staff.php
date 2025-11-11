<?php
// admin_staff.php
include 'admin_header.php';
include 'db.php';

$flash_message = '';
if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    $flash_message = '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . $_SESSION['flash_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Lấy danh sách user CHƯA PHẢI là staff và CÓ VAI TRÒ 'driver' để admin chọn
$sql_users = "SELECT user_id, username FROM users 
              WHERE role = 'driver' 
              AND user_id NOT IN (SELECT user_id FROM staff)";
$users_result = $conn->query($sql_users);

// Lấy danh sách staff đã tạo (JOIN với users để lấy username)
$sql_staff = "SELECT s.staff_id, s.full_name, s.staff_role, s.license_number, u.username 
              FROM staff s 
              JOIN users u ON s.user_id = u.user_id 
              ORDER BY s.full_name";
$staff_result = $conn->query($sql_staff);
?>

<h1 class="mb-4">Staff Management</h1>
<?php echo $flash_message; ?>

<div class="card mb-4">
    <div class="card-header">Add New Staff Profile</div>
    <div class="card-body">
        <form action="add_staff_action.php" method="POST">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">User Account (Role: Driver)</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Select a User --</option>
                        <?php while($user = $users_result->fetch_assoc()) {
                            echo "<option value='" . $user['user_id'] . "'>" . htmlspecialchars($user['username']) . "</option>";
                        } ?>
                    </select>
                    <small>Go to <a href="admin_users.php">User Management</a> to create a user with 'driver' role first.</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="full_name" placeholder="Nguyen Van A" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">License Number</label>
                    <input type="text" class="form-control" name="license_number" placeholder="Bằng lái A1...">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Add Staff Profile</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">List of Staff</div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>License</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($staff_result->num_rows > 0) {
                    while($row = $staff_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["staff_id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                        echo "<td><strong>" . htmlspecialchars($row["full_name"]) . "</strong></td>";
                        echo "<td>" . htmlspecialchars($row["staff_role"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["license_number"]) . "</td>";
                        echo "<td class='text-end'>";
                        echo "<a href='edit_staff.php?id=" . $row["staff_id"] . "' class='btn btn-sm btn-warning'>Edit</a> ";
                        echo "<a href='delete_staff_action.php?id=" . $row["staff_id"] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\");'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No staff found.</td></tr>";
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