<?php
session_start();
include 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    // Lấy thông tin người dùng
    $sql = "SELECT * FROM users WHERE BINARY username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    // Kiểm tra có tồn tại tài khoản không
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Kiểm tra mật khẩu đúng
        if (password_verify($password, $user['password'])) {
            // Đăng nhập thành công → lưu thông tin vào session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
             //  GHI NHỚ TÔI — thêm cookie nếu người dùng chọn checkbox
            if (isset($_POST['remember'])) {
                // Lưu username vào cookie 30 ngày
                setcookie("remember_username", $user['username'], time() + (86400 * 30), "/");
            } else {
                // Nếu không chọn, xóa cookie nếu tồn tại
                setcookie("remember_username", "", time() - 3600, "/");
            }
            // Điều hướng theo quyền
            if ($user['role'] === 'admin') {
                header("Location: ../admin/admin.php");
            } else {
                header("Location: ../index.php");
            }
            exit;
        } else {
            // Sai mật khẩu
            header("Location: ../dn_dk.php?error=wrong_password");
            exit;
        }
    } else {
        // Không tìm thấy tài khoản
        header("Location: ../dn_dk.php?error=account_not_found");
        exit;
    }
} else {
    // Thiếu dữ liệu đầu vào
    header("Location: ../dn_dk.php?error=missing_input");
    exit;
}
?>
