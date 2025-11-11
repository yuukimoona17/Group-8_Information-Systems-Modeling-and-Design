<?php
// admin_vehicles.php
include 'admin_header.php';
include 'db.php';

$flash_message = '';
if (isset($_SESSION['flash_message'])) {
    $message_type = $_SESSION['flash_message_type'];
    $flash_message = '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">' . $_SESSION['flash_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_type']);
}

$sql = "SELECT vehicle_id, license_plate, model, capacity, status FROM vehicles ORDER BY vehicle_id";
$result = $conn->query($sql);
?>

<h1 class="mb-4">Vehicle Management</h1>
<?php echo $flash_message; ?>

<div class="card mb-4">
    <div class="card-header">Add New Vehicle</div>
    <div class="card-body">
        <form action="add_vehicle_action.php" method="POST">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">License Plate</label>
                    <input type="text" class="form-control" name="license_plate" placeholder="29B-12345" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Model</label>
                    <input type="text" class="form-control" name="model" placeholder="Thaco B80" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Capacity</label>
                    <input type="number" class="form-control" name="capacity" placeholder="40" value="40" required>
                </div>
                <div class="col-md-2">
                     <label class="form-label">Status</label>
                     <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Add New</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">List of Vehicles</div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>License Plate</th>
                    <th>Model</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["vehicle_id"] . "</td>";
                        echo "<td><strong>" . htmlspecialchars($row["license_plate"]) . "</strong></td>";
                        echo "<td>" . htmlspecialchars($row["model"]) . "</td>";
                        echo "<td>" . $row["capacity"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                        echo "<td class='text-end'>";
                        echo "<a href='edit_vehicle.php?id=" . $row["vehicle_id"] . "' class='btn btn-sm btn-warning'>Edit</a> ";
                        echo "<a href='delete_vehicle_action.php?id=" . $row["vehicle_id"] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure? Deleting this might affect schedules.\");'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No vehicles found.</td></tr>";
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