<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']);
include 'db.php';
// Get avatar
$sql = "SELECT profile_picture_path FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql); $stmt->bind_param("i", $_SESSION['user_id']); $stmt->execute();
$_SESSION['profile_picture'] = $stmt->get_result()->fetch_assoc()['profile_picture_path'] ?? 'uploads/default_avatar.png';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="admin-page"> <div class="admin-wrapper">
        <nav class="admin-sidebar">
            <div class="p-4 border-bottom border-secondary border-opacity-10 text-center">
                <a href="admin_dashboard.php" class="text-decoration-none text-white">
                    <h4 class="m-0 fw-bold" style="font-family: 'Poppins', sans-serif;">BusAdmin</h4>
                </a>
            </div>
            <div class="py-3 flex-grow-1">
                <div class="px-4 mb-2 text-secondary fw-bold small text-uppercase">Core</div>
                <a href="admin_dashboard.php" class="admin-nav-link <?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="admin_routes.php" class="admin-nav-link <?php echo strpos($current_page, 'route') !== false ? 'active' : ''; ?>"><i class="bi bi-map"></i> Routes</a>
                <a href="admin_bus_stops.php" class="admin-nav-link <?php echo strpos($current_page, 'stop') !== false ? 'active' : ''; ?>"><i class="bi bi-geo-alt"></i> Bus Stops</a>
                <a href="admin_itineraries.php" class="admin-nav-link <?php echo strpos($current_page, 'itinerar') !== false ? 'active' : ''; ?>"><i class="bi bi-signpost-split"></i> Itineraries</a>
                
                <div class="px-4 mt-4 mb-2 text-secondary fw-bold small text-uppercase">Manage</div>
                <a href="admin_vehicles.php" class="admin-nav-link <?php echo strpos($current_page, 'vehicle') !== false ? 'active' : ''; ?>"><i class="bi bi-truck-front"></i> Vehicles</a>
                <a href="admin_staff.php" class="admin-nav-link <?php echo strpos($current_page, 'staff') !== false ? 'active' : ''; ?>"><i class="bi bi-person-badge"></i> Staff</a>
                <a href="admin_schedules.php" class="admin-nav-link <?php echo strpos($current_page, 'schedule') !== false ? 'active' : ''; ?>"><i class="bi bi-calendar-week"></i> Schedules</a>
                
                <a href="admin_manage_tickets.php" class="admin-nav-link <?php echo strpos($current_page, 'ticket') !== false ? 'active' : ''; ?>">
                    <i class="bi bi-ticket-detailed"></i> Monthly Tickets
                </a>

                <div class="px-4 mt-4 mb-2 text-secondary fw-bold small text-uppercase">System</div>
                <a href="admin_feedback.php" class="admin-nav-link <?php echo strpos($current_page, 'feedback') !== false ? 'active' : ''; ?>"><i class="bi bi-chat-left-text"></i> Feedback</a>
                <a href="admin_lost_and_found.php" class="admin-nav-link <?php echo strpos($current_page, 'lost') !== false ? 'active' : ''; ?>"><i class="bi bi-box-seam"></i> Lost & Found</a>
                <a href="admin_announcements.php" class="admin-nav-link <?php echo strpos($current_page, 'announcement') !== false ? 'active' : ''; ?>"><i class="bi bi-megaphone"></i> News</a>
                <a href="chat.php" class="admin-nav-link <?php echo $current_page == 'chat.php' ? 'active' : ''; ?>"><i class="bi bi-chat-dots"></i> Chat</a>
                <a href="admin_users.php" class="admin-nav-link <?php echo strpos($current_page, 'user') !== false ? 'active' : ''; ?>"><i class="bi bi-people"></i> Users</a>
            </div>
            <div class="p-3 border-top border-secondary border-opacity-10 bg-black bg-opacity-20">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" class="rounded-circle me-2 border border-secondary" width="32" height="32" style="object-fit: cover;">
                        <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item text-danger" href="logout.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="admin-main">