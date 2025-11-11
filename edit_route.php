<?php
// edit_route.php
include 'admin_header.php';
include 'db.php';

$route_id = $_GET['id'];

// Lấy thông tin hiện tại của tuyến xe, bao gồm cả giá vé
$sql = "SELECT route_name, ticket_price FROM routes WHERE route_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $route_id);
$stmt->execute();
$result = $stmt->get_result();
$route = $result->fetch_assoc();

if (!$route) {
    die("Route not found.");
}
?>

<h1 class="mb-4">Edit Route Information</h1>

<div class="card">
    <div class="card-header">
        Editing Route: <?php echo htmlspecialchars($route_id); ?>
    </div>
    <div class="card-body">
        <form action="update_route_action.php" method="POST">
            <input type="hidden" name="route_id" value="<?php echo htmlspecialchars($route_id); ?>">
            
            <div class="mb-3">
                <label for="route_name" class="form-label">New Route Name</label>
                <input type="text" class="form-control" id="route_name" name="route_name" value="<?php echo htmlspecialchars($route['route_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="ticket_price" class="form-label">Ticket Price</label>
                <input type="number" class="form-control" id="ticket_price" name="ticket_price" value="<?php echo htmlspecialchars($route['ticket_price']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="admin_routes.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include 'admin_footer.php';
?>