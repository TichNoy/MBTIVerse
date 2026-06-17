<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
// Nếu chưa đăng nhập nhưng có cookie 'remember_username'
if (!isset($_SESSION['username']) && isset($_COOKIE['remember_username'])) {
    require_once 'config.php'; // Kết nối CSDL
    $username = $_COOKIE['remember_username'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Tạo lại session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        // Điều hướng tùy quyền
        if ($user['role'] === 'admin') {
            header("Location: admin/admin.php");
        } else {
            header("Location: index.php");
        }
        exit;
    }
}
?>
