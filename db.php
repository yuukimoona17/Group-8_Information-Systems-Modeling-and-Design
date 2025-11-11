<?php
// File: db.php
// Nhiệm vụ: Tạo và kiểm tra kết nối đến CSDL MySQL.

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bus_system_db";

// Tạo kết nối bằng mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Thiết lập bảng mã UTF-8 để hỗ trợ tiếng Việt
$conn->set_charset("utf8mb4");

// Kiểm tra nếu có lỗi kết nối, thì báo lỗi và dừng chương trình
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>