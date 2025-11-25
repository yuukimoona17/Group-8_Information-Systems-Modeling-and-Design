<?php
session_start();
session_unset();
session_destroy();

// Chuyển về trang chủ (index.php) thay vì login.php
header("Location: index.php");
exit();
?>