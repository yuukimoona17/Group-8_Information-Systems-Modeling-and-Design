<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') header("Location: admin_dashboard.php");
    else if ($_SESSION['role'] == 'driver') header("Location: driver_dashboard.php");
    else header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hanoi Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="pro-auth-body">
    <div class="pro-auth-overlay"></div>

    <a href="index.php" class="btn btn-outline-light rounded-pill position-absolute top-0 start-0 m-4 fw-bold" style="z-index: 10; backdrop-filter: blur(5px);">
        <i class="bi bi-arrow-left me-2"></i>Back to Home
    </a>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                
                <div class="pro-auth-card card-login-size p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="auth-icon-circle mx-auto">
                            <i class="bi bi-bus-front-fill text-white"></i>
                        </div>
                        <h2 class="text-white fw-bold mb-1">Welcome Back</h2>
                        <p class="text-white-50 small">Please sign in to your account</p>
                    </div>

                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['flash_message_type']; ?> text-center py-2 small mb-4 border-0 shadow-sm">
                            <?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="login_action.php" method="POST">
                        <div class="pro-form-group">
                            <label class="pro-form-label text-white-50">USERNAME</label>
                            <div class="position-relative">
                                <input type="text" class="form-control pro-form-control" name="username" placeholder="Type your username" required>
                                <i class="bi bi-person position-absolute top-50 end-0 translate-middle-y me-3"></i>
                            </div>
                        </div>

                        <div class="pro-form-group mb-4">
                            <label class="pro-form-label text-white-50">PASSWORD</label>
                            <div class="position-relative">
                                <input type="password" class="form-control pro-form-control" name="password" placeholder="••••••••" required>
                                <i class="bi bi-lock position-absolute top-50 end-0 translate-middle-y me-3"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn-pro-primary mb-4 shadow-lg">
                            SIGN IN <i class="bi bi-arrow-right-short fs-4 align-middle"></i>
                        </button>

                        <div class="text-center border-top border-white border-opacity-10 pt-4">
                            <p class="text-white-50 mb-0 small">Don't have an account?</p>
                            <a href="register.php" class="text-info fw-bold text-decoration-none">Create New Account</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>