<?php
session_start();             // Khởi động phiên làm việc
session_unset();             // Xóa tất cả biến phiên
session_destroy();           // Hủy phiên làm việc hiện tại
// Xóa cookie "remember_username"
setcookie("remember_username", "", time() - 3600, "/");
// Chuyển hướng về trang chủ (hoặc trang đăng nhập)
header("Location: ../index.php");
exit();                     
?>
