<?php
// Cấu hình đường dẫn gốc (dùng cho liên kết trong header/footer)
$base_url = "/MBTIVerse";
//Cấu hình kết nối database
$db_host = "localhost";     // Server CSDL (thường là localhost)
$db_user = "root";          // Tên người dùng (XAMPP thường là root)
$db_pass = "";              // Mật khẩu (XAMPP thường để trống)
$db_name = "mbti_schema";       // Tên database bạn đã tạo
//Kết nối đến MySQL
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
//Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
