<?php include 'header.php'; // header.php đã có session_start() ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 auth-card">
            <div class="card">
                <div class="card-header text-center"><h3>Register</h3></div>
                <div class="card-body">

                    <?php
                    if (isset($_SESSION['flash_message'])) {
                        $message_type = $_SESSION['flash_message_type'] ?? 'info';
                        echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' 
                             . $_SESSION['flash_message'] 
                             . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        
                        // Xóa message sau khi hiển thị
                        unset($_SESSION['flash_message']);
                        unset($_SESSION['flash_message_type']);
                    }
                    ?>
                    <form action="register_action.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <small>Already have an account? <a href="login.php">Login here</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>