<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// Bảo vệ trang: Chỉ cho phép 'driver' truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Lấy thông tin staff (staff_id, full_name) từ user_id của session
$sql_staff = "SELECT staff_id, full_name FROM staff WHERE user_id = ?";
$stmt_staff = $conn->prepare($sql_staff);
$stmt_staff->bind_param("i", $_SESSION['user_id']);
$stmt_staff->execute();
$staff_result = $stmt_staff->get_result();

if ($staff_result->num_rows === 0) {
    // Nếu user có role 'driver' nhưng chưa được admin tạo hồ sơ staff
    die("Your staff profile is not configured. Please contact admin.");
}
$staff_info = $staff_result->fetch_assoc();
$_SESSION['staff_id'] = $staff_info['staff_id']; // Lưu staff_id vào session để dùng sau
$_SESSION['full_name'] = $staff_info['full_name'];
$stmt_staff->close();

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="driver_dashboard.php"><i class="bi bi-person-badge-fill"></i> Driver Portal</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                 <li class="nav-item">
                    <a class="nav-link" href="driver_dashboard.php"><i class="bi bi-calendar-week me-2"></i>My Schedule</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="driver_report_found_item.php"><i class="bi bi-box-arrow-in-down me-2"></i>Report Found Item</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-search me-2"></i>Search Routes</a>
                 </li>
                 </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
      </div>
    </nav>
    <div class="container my-4">