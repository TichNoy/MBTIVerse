<?php
session_start(); // Phải có để truy cập session
include 'config.php';
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$user_id = $_SESSION['user_id']; // Session phải tồn tại thì mới update đúng người dùng
$sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $username, $email, $user_id);
if ($stmt->execute()) {
    // Cập nhật lại session nếu cần
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    header("Location: ../ca_nhan.php");
exit;
    echo "Cập nhật thành công!";
} else {
    echo "Có lỗi xảy ra: " . $stmt->error;
}
?>