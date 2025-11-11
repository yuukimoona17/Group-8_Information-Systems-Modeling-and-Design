<?php
// admin_itineraries.php
include 'admin_header.php';
include 'db.php';

// Lấy tất cả các tuyến xe
$sql = "SELECT route_id, route_name FROM routes ORDER BY route_id";
$result = $conn->query($sql);
?>

<h1 class="mb-4">Itinerary Management</h1>

<div class="card">
    <div class="card-header">Select a Route to Manage its Itinerary</div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Route ID</th>
                    <th>Route Name</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["route_id"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["route_name"]) . "</td>";
                        echo "<td class='text-end'><a href='manage_itinerary_detail.php?route_id=" . urlencode($row["route_id"]) . "' class='btn btn-info btn-sm'>Manage Itinerary</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>No routes found.</td></tr>";
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