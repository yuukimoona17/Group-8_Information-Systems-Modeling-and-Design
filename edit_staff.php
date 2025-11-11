<?php
include 'admin_header.php';
include 'db.php';

$staff_id = $_GET['id'];

$sql = "SELECT s.full_name, s.staff_role, s.license_number, s.contact_info, u.username 
        FROM staff s 
        JOIN users u ON s.user_id = u.user_id 
        WHERE s.staff_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

if (!$staff) {
    die("Staff not found.");
}
?>

<h1 class="mb-4">Edit Staff Profile</h1>

<div class="card">
    <div class="card-header">
        Editing Staff: <?php echo htmlspecialchars($staff['full_name']); ?> (Account: <?php echo htmlspecialchars($staff['username']); ?>)
    </div>
    <div class="card-body">
        <form action="update_staff_action.php" method="POST">
            <input type="hidden" name="staff_id" value="<?php echo htmlspecialchars($staff_id); ?>">
            
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($staff['full_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="license_number" class="form-label">License Number</label>
                <input type="text" class="form-control" id="license_number" name="license_number" value="<?php echo htmlspecialchars($staff['license_number']); ?>">
            </div>

            <div class="mb-3">
                <label for="contact_info" class="form-label">Contact Info</label>
                <input type="text" class="form-control" id="contact_info" name="contact_info" value="<?php echo htmlspecialchars($staff['contact_info']); ?>">
            </div>
            
             <div class="mb-3">
                <label for="staff_role" class="form-label">Role</label>
                <select class="form-select" id="staff_role" name="staff_role" required>
                    <option value="driver" <?php echo ($staff['staff_role'] == 'driver' ? 'selected' : ''); ?>>Driver</option>
                    <option value="conductor" <?php echo ($staff['staff_role'] == 'conductor' ? 'selected' : ''); ?>>Conductor</option>
                    <option value="manager" <?php echo ($staff['staff_role'] == 'manager' ? 'selected' : ''); ?>>Manager</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="admin_staff.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include 'admin_footer.php';
?>