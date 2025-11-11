<?php
// edit_stop.php
include 'admin_header.php';
include 'db.php';

$stop_id = $_GET['id'];

$sql = "SELECT stop_name, street FROM bus_stops WHERE stop_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $stop_id);
$stmt->execute();
$result = $stmt->get_result();
$stop = $result->fetch_assoc();

if (!$stop) {
    die("Bus stop not found.");
}
?>

<h1 class="mb-4">Edit Bus Stop</h1>

<div class="card">
    <div class="card-header">
        Editing Bus Stop #<?php echo htmlspecialchars($stop_id); ?>
    </div>
    <div class="card-body">
        <form action="update_stop_action.php" method="POST">
            <input type="hidden" name="stop_id" value="<?php echo htmlspecialchars($stop_id); ?>">
            <div class="mb-3">
                <label for="stop_name" class="form-label">Stop Name</label>
                <input type="text" class="form-control" id="stop_name" name="stop_name" value="<?php echo htmlspecialchars($stop['stop_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="street" class="form-label">Street Name</label>
                <input type="text" class="form-control" id="street" name="street" value="<?php echo htmlspecialchars($stop['street']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="admin_bus_stops.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include 'admin_footer.php';
?>