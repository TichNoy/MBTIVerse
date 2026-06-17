<?php
session_start();
include 'config.php';
// Hiển thị lỗi trong quá trình phát triển
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Kiểm tra nếu dữ liệu POST đầy đủ
if (
    isset($_POST['username']) &&
    isset($_POST['email']) &&
    isset($_POST['password']) &&
    isset($_POST['confirm_password'])
) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // 1. Kiểm tra email trống
    if (empty($email)) {
        echo "Vui lòng nhập email!";
        exit;
    }
    // 2. Kiểm tra cú pháp email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email không hợp lệ! Vui lòng nhập đúng định dạng như ten@example.com";
        exit;
    }
    // 3. Kiểm tra định dạng email phổ biến
    $pattern = '/^[a-zA-Z0-9._%+-]+@'
             . '(gmail\\.com|yahoo\\.com|outlook\\.com|icloud\\.com|hotmail\\.com|live\\.com|aol\\.com|protonmail\\.com'
             . '|[\\w\\-]+\\.(com|net|org|vn|com\\.vn|net\\.vn|edu(\\.vn)?))$/';
    if (!preg_match($pattern, $email)) {
        echo "Email không hợp lệ hoặc không phổ biến.";
        exit;
    }
    // 4. Chặn domain sai chính tả
    $common_mistakes = ['gmai.com', 'gmial.com', 'gnail.com', 'gamil.com', 'yaho.com', 'hotnail.com'];
    $domain = strtolower(substr(strrchr($email, "@"), 1));
    if (in_array($domain, $common_mistakes)) {
        echo "Bạn có thể đã gõ sai địa chỉ email: @$domain. Vui lòng kiểm tra lại.";
        exit;
    }
    // 5. Kiểm tra mật khẩu khớp
    if ($password !== $confirm_password) {
        echo "Mật khẩu xác nhận không khớp!";
        exit;
    }
    // 6. Kiểm tra độ dài và độ mạnh của mật khẩu
    if (strlen($password) < 6) {
        echo "Mật khẩu phải có ít nhất 6 ký tự!";
        exit;
    }
    // Tùy chọn nâng cao: kiểm tra độ mạnh mật khẩu
    if (!preg_match('/[A-Z]/', $password) ||  // ít nhất 1 chữ hoa
        !preg_match('/[a-z]/', $password) ||  // ít nhất 1 chữ thường
        !preg_match('/[0-9]/', $password) ||  // ít nhất 1 số
        !preg_match('/[\W]/', $password)) {   // ít nhất 1 ký tự đặc biệt
        echo "Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt!";
        exit;
    }
    // 7. Kiểm tra username hoặc email đã tồn tại
    $sql_check = "SELECT username, email FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $existing = $result->fetch_assoc();
        if ($existing['username'] == $username) {
            echo "Tên người dùng đã tồn tại!";
        } else {
            echo "Email đã được sử dụng!";
        }
        exit;
    }
    // 8. Mã hóa mật khẩu
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    // 9. Tạo tài khoản mới với role mặc định
    $role = 'user';
    $sql_insert = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
    if ($stmt->execute()) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        echo "success";
        exit;
    } else {
        echo "Lỗi khi thêm tài khoản: " . $stmt->error;
        exit;
    }
} else {
    echo "Thiếu thông tin. Vui lòng kiểm tra lại!";
    exit;
}
?>
