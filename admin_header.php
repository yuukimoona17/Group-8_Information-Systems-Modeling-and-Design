<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { display: flex; min-height: 100vh; background-color: #f8f9fa; }
        .sidebar { width: 280px; background-color: #212529; color: white; }
        .main-content { flex-grow: 1; padding: 30px; }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column p-3">
        <a href="admin_dashboard.php" class="d-flex align-items-center mb-3 text-white text-decoration-none"><i class="bi bi-bus-front-fill me-2"></i><span class="fs-4">BusAdmin</span></a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li><a href="admin_dashboard.php" class="nav-link <?php echo ($current_page == 'admin_dashboard.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="admin_routes.php" class="nav-link <?php echo ($current_page == 'admin_routes.php' || $current_page == 'edit_route.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-card-list me-2"></i> Route Management</a></li>
            <li><a href="admin_bus_stops.php" class="nav-link <?php echo ($current_page == 'admin_bus_stops.php' || $current_page == 'edit_stop.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-geo-alt-fill me-2"></i> Bus Stop Management</a></li>
            <li><a href="admin_itineraries.php" class="nav-link <?php echo ($current_page == 'admin_itineraries.php' || $current_page == 'manage_itinerary_detail.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-sign-turn-right-fill me-2"></i> Itinerary Management</a></li>
            <hr>
            <li class="nav-item"><a href="admin_vehicles.php" class="nav-link <?php echo ($current_page == 'admin_vehicles.php' || $current_page == 'edit_vehicle.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-truck-front me-2"></i> Vehicle Management</a></li>
            <li class="nav-item"><a href="admin_staff.php" class="nav-link <?php echo ($current_page == 'admin_staff.php' || $current_page == 'edit_staff.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-person-badge me-2"></i> Staff Management</a></li>
            <li class="nav-item"><a href="admin_schedules.php" class="nav-link <?php echo ($current_page == 'admin_schedules.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-calendar-week me-2"></i> Schedule Management</a></li>
            <hr>
            <li class="nav-item"><a href="admin_feedback.php" class="nav-link <?php echo ($current_page == 'admin_feedback.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-chat-left-quote-fill me-2"></i> User Feedback</a></li>
            
            <li class="nav-item"><a href="admin_lost_and_found.php" class="nav-link <?php echo ($current_page == 'admin_lost_and_found.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-box-fill me-2"></i> Lost & Found</a></li>
            <li><a href="admin_announcements.php" class="nav-link <?php echo ($current_page == 'admin_announcements.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-megaphone-fill me-2"></i> Announcements</a></li>
            <li><a href="admin_users.php" class="nav-link <?php echo ($current_page == 'admin_users.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-people-fill me-2"></i> User Management</a></li>
            <li><a href="chat.php" class="nav-link <?php echo ($current_page == 'chat.php') ? 'active' : 'text-white'; ?>"><i class="bi bi-chat-dots-fill me-2"></i> Chat</a></li>
        </ul>
        <hr>
        <div class="dropdown">
             <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-2"></i>
                <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
            </ul>
        </div>
    </div>
    <div class="main-content">