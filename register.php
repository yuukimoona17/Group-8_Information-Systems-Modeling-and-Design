<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Hanoi Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        /* CSS RIÊNG ĐỂ FIX LỖI GIAO DIỆN REGISTER */
        
        /* 1. Fix Banner bên trái: Màu đậm hơn để chữ rõ hơn */
        .register-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); /* Xanh đậm hơn */
            position: relative;
            overflow: hidden;
        }
        
        /* Họa tiết trang trí mờ */
        .register-banner::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.3;
            transform: rotate(45deg);
        }

        /* 2. Fix Input Upload Ảnh: Ẩn input gốc, làm Custom UI */
        .avatar-upload-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .avatar-preview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            transition: 0.3s;
        }
        
        .avatar-upload-wrapper:hover .avatar-preview {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }

        /* Icon mặc định khi chưa chọn ảnh */
        .avatar-icon {
            font-size: 3rem;
            color: rgba(255,255,255,0.3);
        }

        /* Ảnh thật khi đã chọn */
        .avatar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none; /* Ẩn mặc định */
        }

        .upload-icon-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 30px;
            height: 30px;
            background: #3b82f6;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #1e293b; /* Viền trùng màu nền web */
            font-size: 0.8rem;
        }

        /* 3. Fix Input Text: Màu tối cho hợp theme */
        .auth-form-control {
            background-color: rgba(0, 0, 0, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
        }
        .auth-form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25) !important;
        }
        
        .auth-label {
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body class="pro-auth-body">
    <div class="pro-auth-overlay"></div>

    <a href="index.php" class="btn btn-outline-light rounded-pill position-absolute top-0 start-0 m-4 fw-bold" style="z-index: 10; backdrop-filter: blur(5px);">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                
                <div class="pro-auth-card card-register-size" style="background: #1e293b;">
                    <div class="row g-0">
                        
                        <div class="col-lg-5 register-banner p-5 text-white d-flex flex-column justify-content-center text-center">
                            <div class="position-relative z-1">
                                <div class="mb-4">
                                    <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; backdrop-filter: blur(5px);">
                                        <i class="bi bi-stars fs-1"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bold mb-3">Join the Journey</h2>
                                <p class="text-white-50 mb-4 fs-6">Create an account to manage your monthly passes and enjoy seamless travel.</p>
                                
                                <a href="login.php" class="btn btn-outline-light w-75 mx-auto rounded-pill fw-bold mt-auto">
                                    I already have an account
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-7 p-5">
                            <h3 class="fw-bold text-white mb-1">Create Account</h3>
                            <p class="text-secondary small mb-4">Fill in your details below</p>

                            <?php
                            if (isset($_SESSION['flash_message'])) {
                                $type = $_SESSION['flash_message_type'] ?? 'info';
                                echo '<div class="alert alert-'.$type.' small py-2 mb-3 rounded-3">'.$_SESSION['flash_message'].'</div>';
                                unset($_SESSION['flash_message']);
                            }
                            ?>

                            <form action="register_action.php" method="POST" enctype="multipart/form-data">
                                <div class="row g-3">
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="auth-label mb-2">Username</label>
                                            <input type="text" class="form-control auth-form-control" name="username" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="auth-label mb-2">Email</label>
                                            <input type="email" class="form-control auth-form-control" name="email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="auth-label mb-2">Password</label>
                                            <input type="password" class="form-control auth-form-control" name="password" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        
                                        <div class="d-flex flex-column align-items-center mb-3">
                                            <label class="auth-label mb-2 align-self-start">Profile Picture</label>
                                            
                                            <label for="profile_upload" class="avatar-upload-wrapper">
                                                <div class="avatar-preview">
                                                    <i class="bi bi-person-fill avatar-icon" id="defaultIcon"></i>
                                                    <img src="#" id="avatarImg" class="avatar-image">
                                                </div>
                                                <div class="upload-icon-badge">
                                                    <i class="bi bi-camera-fill"></i>
                                                </div>
                                            </label>
                                            
                                            <input type="file" id="profile_upload" name="profile_picture" class="d-none" accept="image/*" onchange="previewFile()">
                                            <div class="text-secondary small" style="font-size: 0.75rem;">Click to upload (Optional)</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="auth-label mb-2">Full Name</label>
                                            <input type="text" class="form-control auth-form-control" name="full_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="auth-label mb-2">Phone</label>
                                            <input type="tel" class="form-control auth-form-control" name="phone_number" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input bg-dark border-secondary" type="checkbox" value="" id="termsCheck" required>
                                        <label class="form-check-label small text-secondary" for="termsCheck">
                                            I agree to the <a href="#" class="text-primary text-decoration-none">Terms of Service</a> & Policy.
                                        </label>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-lg text-uppercase" style="letter-spacing: 1px;">
                                        Register Account
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function previewFile() {
            const preview = document.getElementById('avatarImg');
            const icon = document.getElementById('defaultIcon');
            const file = document.getElementById('profile_upload').files[0];
            const reader = new FileReader();

            reader.addEventListener("load", function () {
                // Có ảnh -> Hiện ảnh, ẩn icon
                preview.src = reader.result;
                preview.style.display = 'block';
                icon.style.display = 'none';
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>