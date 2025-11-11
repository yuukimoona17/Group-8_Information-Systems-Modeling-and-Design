<?php
// admin_bus_stops.php
include 'admin_header.php';
include 'db.php';

// Lấy tất cả điểm dừng, sắp xếp theo ID mới nhất lên đầu
$sql = "SELECT stop_id, stop_name, street FROM bus_stops ORDER BY stop_id DESC";
$result = $conn->query($sql);
?>

<h1 class="mb-4">Bus Stop Management</h1>

<div class="card mb-4">
    <div class="card-header">Add New Bus Stop</div>
    <div class="card-body">
        <form action="add_stop_action.php" method="POST">
            <div class="row">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="stop_name" placeholder="Stop Name" required>
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="street" placeholder="Street Name">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Add New</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">List of Bus Stops</div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Stop Name</th>
                    <th>Street</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["stop_id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["stop_name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["street"]) . "</td>";
                        echo "<td class='text-end'>";
                        echo "<a href='edit_stop.php?id=" . $row["stop_id"] . "' class='btn btn-sm btn-warning'>Edit</a> ";
                        echo "<a href='delete_stop_action.php?id=" . $row["stop_id"] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\");'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No bus stops found.</td></tr>";
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