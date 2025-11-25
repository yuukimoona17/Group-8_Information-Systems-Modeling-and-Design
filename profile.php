<?php
// profile.php - Clean Version
include 'user_header.php'; 

// Đảm bảo đã login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy lại thông tin mới nhất từ DB (để cập nhật ngay sau khi sửa)
include 'db.php';
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<div class="container">
    <h2 class="mb-4 text-white border-start border-4 border-primary ps-3">My Profile</h2>

    <?php
    if (isset($_SESSION['flash_message'])) {
        $msg_type = $_SESSION['flash_message_type'];
        echo '<div class="alert alert-'.$msg_type.'">'.$_SESSION['flash_message'].'</div>';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_message_type']);
    }
    ?>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <img src="<?php echo htmlspecialchars($user['profile_picture_path'] ?? 'uploads/default_avatar.png'); ?>" 
                         class="rounded-circle mb-3 shadow" 
                         style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #3b82f6;">
                    <h4 class="text-white"><?php echo htmlspecialchars($user['full_name']); ?></h4>
                    <span class="badge bg-primary"><?php echo ucfirst($user['role']); ?></span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Update Information</div>
                <div class="card-body">
                    <form action="update_profile_action.php" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username (Read-only)</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Change Avatar</label>
                            <input type="file" class="form-control" name="profile_picture">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Change Password</div>
                <div class="card-body">
                    <form action="change_password_action.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Old Password</label>
                            <input type="password" class="form-control" name="old_password" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="confirm_new_password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning text-dark fw-bold">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'user_footer.php'; ?>