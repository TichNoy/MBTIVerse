<?php
// Cấu hình đường dẫn gốc
$base_url = "";

// Thông tin kết nối Railway MySQL
$db_host = "homas.proxy.rlwy.net";
$db_user = "root";
$db_pass = "crnqsvSwHcqPDQJKOlqOyXPmkRBLcAhU";
$db_name = "railway";
$db_port = 17405;

// Kết nối MySQL
$conn = new mysqli(
    $db_host,
    $db_user,
    $db_pass,
    $db_name,
    $db_port
);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập UTF-8
$conn->set_charset("utf8mb4");
?>