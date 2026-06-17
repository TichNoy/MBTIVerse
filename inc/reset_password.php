<?php
require 'config.php'; 
$email = trim($_POST['email']);
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Email không hợp lệ.");
}
if ($new_password !== $confirm_password) {
    exit("Mật khẩu xác nhận không khớp.");
}
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
// Kiểm tra email tồn tại
$stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    exit("Không tìm thấy email.");
}
// Cập nhật mật khẩu
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed_password, $email);
if ($stmt->execute()) {
    echo "success";
} else {
    echo "Đổi mật khẩu thất bại.";
}
?>
