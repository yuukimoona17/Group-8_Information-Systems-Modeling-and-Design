<?php
// admin_routes.php
include 'admin_header.php';
include 'db.php';

// Code hiển thị Flash Message (giữ nguyên)
$flash_message = '';
if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    $flash_message = '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . $_SESSION['flash_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

// Lấy tất cả các tuyến xe để hiển thị (thêm cột ticket_price)
$sql = "SELECT route_id, route_name, ticket_price FROM routes ORDER BY route_id";
$result = $conn->query($sql);
?>

<h1 class="mb-4">Route Management</h1>

<?php
// In ra alert box nếu có
echo $flash_message; 
?>

<div class="card mb-4">
    <div class="card-header">Add New Route</div>
    <div class="card-body">
        <form action="add_route_action.php" method="POST">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="route_id" placeholder="Route ID (e.g., R01)" required>
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="route_name" placeholder="Route Name" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="ticket_price" placeholder="Price" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Add New</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">List of Routes</div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Route ID</th>
                    <th>Route Name</th>
                    <th>Price (VND)</th> <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["route_id"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["route_name"]) . "</td>";
                        // Hiển thị giá vé, định dạng lại cho đẹp
                        echo "<td>" . number_format($row["ticket_price"]) . "</td>";
                        echo "<td class='text-end'>";
                        echo "<a href='edit_route.php?id=" . urlencode($row["route_id"]) . "' class='btn btn-sm btn-warning'>Edit</a> ";
                        echo "<a href='delete_route_action.php?id=" . urlencode($row["route_id"]) . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\");'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No routes found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
include 'admin_footer.php';
?>