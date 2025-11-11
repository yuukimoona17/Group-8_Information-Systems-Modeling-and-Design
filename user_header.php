<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
// Lấy vai trò để kiểm tra
$user_role = $_SESSION['role'] ?? 'user'; 
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanoi Bus System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        
        <?php if ($user_role === 'driver'): ?>
            <a class="navbar-brand" href="driver_dashboard.php"><i class="bi bi-person-badge-fill"></i> Driver Portal</a> <?php else: ?>
             <a class="navbar-brand" href="index.php"><i class="bi bi-bus-front-fill"></i> Hanoi Bus</a> <?php endif; ?>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                 
                 <?php if ($user_role === 'driver'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="driver_dashboard.php">
                            <i class="bi bi-arrow-left-circle-fill me-1"></i> Back to Driver Dashboard
                        </a>
                    </li>
                 <?php endif; ?>
                 <li class="nav-item">
                    <a class="nav-link" href="chat.php"><i class="bi bi-chat-dots-fill me-2"></i>Chat with Admin</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="feedback.php"><i class="bi bi-chat-left-quote-fill me-2"></i>Send Feedback</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="my_feedback.php"><i class="bi bi-card-checklist me-2"></i>My Feedback</a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link" href="lost_and_found.php"><i class="bi bi-box me-2"></i>Lost & Found</a>
                 </li>
                 
                 <?php if ($user_role !== 'driver'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-search me-2"></i>Search Routes</a>
                    </li>
                 <?php endif; ?>
                 </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
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