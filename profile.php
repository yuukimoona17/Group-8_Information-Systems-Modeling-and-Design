<?php
// profile.php
include 'user_header.php'; // Dùng header mới của user
?>

<h1 class="mb-4">My Profile</h1>

<?php
if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    echo '<div class="alert alert-' . $message_type . '">' . $_SESSION['flash_message'] . '</div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}
?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Account Information</h5>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        Change Password
    </div>
    <div class="card-body">
        <form action="change_password_action.php" method="POST">
            <div class="mb-3">
                <label for="old_password" class="form-label">Old Password</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>


<?php
include 'user_footer.php'; // Dùng footer mới của user
?>