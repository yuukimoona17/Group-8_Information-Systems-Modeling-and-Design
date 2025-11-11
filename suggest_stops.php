<?php
// suggest_stops.php: Trả về gợi ý điểm dừng dưới dạng JSON
header('Content-Type: application/json');
include 'db.php'; // Kết nối CSDL

$suggestions = [];
$term = $_GET['term'] ?? ''; // Lấy từ khóa người dùng gõ

if (strlen($term) >= 2) { // Chỉ tìm khi gõ ít nhất 2 ký tự
    $search_query = "%" . $term . "%"; // Thêm dấu % để tìm LIKE

    // Tìm các điểm dừng có tên khớp
    $sql = "SELECT stop_id, stop_name FROM bus_stops 
            WHERE stop_name LIKE ? 
            ORDER BY stop_name ASC 
            LIMIT 10"; // Giới hạn 10 gợi ý
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_query);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $suggestions[] = [
            'id' => $row['stop_id'],    // ID để lưu lại
            'value' => $row['stop_name'] // Tên để hiển thị
        ];
    }
    $stmt->close();
}
$conn->close();
echo json_encode($suggestions); // Trả kết quả về
?>