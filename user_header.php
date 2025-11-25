<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['role'] ?? 'guest';
if ($is_logged_in) {
    include_once 'db.php';
    if (!isset($_SESSION['current_user_info'])) {
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql); $stmt->bind_param("i", $_SESSION['user_id']); $stmt->execute();
        $_SESSION['current_user_info'] = $stmt->get_result()->fetch_assoc();
        $_SESSION['profile_picture'] = $_SESSION['current_user_info']['profile_picture_path'] ?? 'uploads/default_avatar.png';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanoi Bus System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="user-page"> <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container">
        <?php if ($user_role === 'driver'): ?>
            <a class="navbar-brand" href="driver_dashboard.php"><i class="bi bi-person-badge-fill me-2"></i>Driver Portal</a> 
        <?php else: ?>
             <a class="navbar-brand" href="index.php"><i class="bi bi-bus-front-fill me-2"></i>Hanoi Bus</a> 
        <?php endif; ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-3">
                 <?php if ($user_role === 'driver'): ?><li class="nav-item"><a class="nav-link text-warning" href="driver_dashboard.php">Dashboard</a></li><?php endif; ?>
                 
                 <?php if ($user_role !== 'driver'): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php">Search</a></li>
                    <li class="nav-item"><a class="nav-link" href="announcements.php">News</a></li>
                 <?php endif; ?>
                 
                 <?php if ($is_logged_in): ?>
                     <li class="nav-item"><a class="nav-link text-info fw-bold" href="user_view_tickets.php"><i class="bi bi-ticket-perforated me-1"></i>My Ticket</a></li>
                     
                     <li class="nav-item"><a class="nav-link" href="chat.php">Chat</a></li>
                     <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                     <li class="nav-item"><a class="nav-link" href="lost_and_found.php">Lost & Found</a></li>
                 <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if ($is_logged_in): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg">
                            <?php if ($user_role === 'driver'): ?><li><a class="dropdown-item" href="driver_dashboard.php">Driver Dashboard</a></li><li><hr class="dropdown-divider"></li><?php endif; ?>
                            <li><a class="dropdown-item" href="user_view_tickets.php">My Ticket</a></li>
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="my_feedback.php">History</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link fw-bold" href="login.php">Login</a></li>
                    <li class="nav-item ms-2"><a class="btn btn-primary btn-sm fw-bold px-4" href="register.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
      </div>
    </nav>
    <div class="container main-user-container">