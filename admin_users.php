<?php
// admin_users.php - Nâng cấp toàn bộ
include 'admin_header.php';
include 'db.php';

$current_admin_id = $_SESSION['user_id'];

// Sửa SQL: Lấy tất cả thông tin mới
$sql = "SELECT user_id, username, role, full_name, email, phone_number, profile_picture_path 
        FROM users 
        WHERE user_id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_admin_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1 class="mb-4">User Management</h1>

<?php
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
        <div class="table-responsive"> <table class="table table-striped table-hover align-middle"> <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Contact</th>
                        <th>Role</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            // CỘT MỚI: AVATAR
                            echo "<td><img src='" . htmlspecialchars($row["profile_picture_path"]) . "' class='rounded-circle' style='width: 40px; height: 40px; object-fit: cover;'></td>";
                            
                            // CỘT MỚI: FULL NAME
                            echo "<td><strong>" . htmlspecialchars($row["full_name"]) . "</strong></td>";
                            
                            echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                            
                            // CỘT MỚI: CONTACT
                            echo "<td>" . htmlspecialchars($row["email"]) . "<br><small class='text-muted'>" . htmlspecialchars($row["phone_number"]) . "</small></td>";
                            
                            // CỘT SỬA: ROLE
                            echo "<td>";
                            echo "<form action='update_user_role_action.php' method='POST' class='d-inline-flex'>";
                            echo "<input type='hidden' name='user_id' value='" . $row['user_id'] . "'>";
                            echo "<select name='role' class='form-select form-select-sm' onchange='this.form.submit()'>";
                            echo "<option value='user' " . ($row['role'] == 'user' ? 'selected' : '') . ">User</option>";
                            echo "<option value='admin' " . ($row['role'] == 'admin' ? 'selected' : '') . ">Admin</option>";
                            echo "<option value='driver' " . ($row['role'] == 'driver' ? 'selected' : '') . ">Driver</option>";
                            echo "</select>";
                            echo "</form>";
                            echo "</td>";
                            
                            // CỘT ACTIONS
                            echo "<td class='text-end'>";
                            echo "<a href='delete_user_action.php?id=" . $row["user_id"] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this user?\");'>Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No other users found.</td></tr>"; // colspan = 7
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include 'admin_footer.php';
?>