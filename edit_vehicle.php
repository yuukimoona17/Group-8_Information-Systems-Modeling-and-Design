<?php
include 'admin_header.php';
include 'db.php';

$vehicle_id = $_GET['id'];

$sql = "SELECT license_plate, model, capacity, status FROM vehicles WHERE vehicle_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();

if (!$vehicle) {
    die("Vehicle not found.");
}
?>

<h1 class="mb-4">Edit Vehicle</h1>

<div class="card">
    <div class="card-header">
        Editing Vehicle: <?php echo htmlspecialchars($vehicle['license_plate']); ?>
    </div>
    <div class="card-body">
        <form action="update_vehicle_action.php" method="POST">
            <input type="hidden" name="vehicle_id" value="<?php echo htmlspecialchars($vehicle_id); ?>">
            
            <div class="mb-3">
                <label for="license_plate" class="form-label">License Plate</label>
                <input type="text" class="form-control" id="license_plate" name="license_plate" value="<?php echo htmlspecialchars($vehicle['license_plate']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="model" class="form-label">Model</label>
                <input type="text" class="form-control" id="model" name="model" value="<?php echo htmlspecialchars($vehicle['model']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" value="<?php echo htmlspecialchars($vehicle['capacity']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active" <?php echo ($vehicle['status'] == 'active' ? 'selected' : ''); ?>>Active</option>
                    <option value="maintenance" <?php echo ($vehicle['status'] == 'maintenance' ? 'selected' : ''); ?>>Maintenance</option>
                    <option value="inactive" <?php echo ($vehicle['status'] == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Vehicle</button>
            <a href="admin_vehicles.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include 'admin_footer.php';
?>