<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Lấy thông tin Staff & Avatar từ bảng Users
$sql_staff = "SELECT s.staff_id, s.full_name, u.profile_picture_path 
              FROM staff s 
              JOIN users u ON s.user_id = u.user_id 
              WHERE s.user_id = ?";
$stmt_staff = $conn->prepare($sql_staff);
$stmt_staff->bind_param("i", $_SESSION['user_id']);
$stmt_staff->execute();
$staff_result = $stmt_staff->get_result();

if ($staff_result->num_rows === 0) {
    die("Your staff profile is not configured. Please contact admin.");
}

$staff_info = $staff_result->fetch_assoc();
$_SESSION['staff_id'] = $staff_info['staff_id']; 
$_SESSION['full_name'] = $staff_info['full_name'];
$_SESSION['profile_picture'] = $staff_info['profile_picture_path'] ?? 'uploads/default_avatar.png';
$stmt_staff->close();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="user-page"> <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container">
        <a class="navbar-brand" href="driver_dashboard.php"><i class="bi bi-person-badge-fill me-2"></i>Driver Portal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-lg-4">
                 <li class="nav-item"><a class="nav-link" href="driver_dashboard.php">My Schedule</a></li>
                 <li class="nav-item"><a class="nav-link" href="driver_report_found_item.php">Report Found Item</a></li>
                 <li class="nav-item"><a class="nav-link" href="index.php">Search Routes</a></li>
                 <li class="nav-item"><a class="nav-link" href="chat.php">Chat with Admin</a></li>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" class="rounded-circle me-2 border border-secondary" width="32" height="32" style="object-fit: cover;">
                        <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg">
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
      </div>
    </nav>
    <div class="container main-user-container">