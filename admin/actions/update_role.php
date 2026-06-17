<?php
include '../../inc/config.php';
if (isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];
    // Kiểm tra role mới có hợp lệ không
    if (!in_array($new_role, ['admin', 'user'])) {
        echo "invalid_role";
        exit;
    }
    session_start();
    // Không cho đổi vai trò của chính mình
    if ($_SESSION['user_id'] == $user_id) {
        echo "cannot_change_own_role";
        exit;
    }
    // Lấy vai trò hiện tại
    $stmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if (!$user) {
        echo "user_not_found";
        exit;
    }
    // Chỉ cho phép chuyển từ user => admin
    if ($user['role'] === 'user' && $new_role === 'admin') {
        $update = $conn->prepare("UPDATE users SET role = 'admin' WHERE user_id = ?");
        $update->bind_param("i", $user_id);
        if ($update->execute()) {
            echo "success";
        } else {
            echo "error_updating";
        }
    } else {
        echo "not_allowed"; // Ngăn chuyển admin -> user hoặc sửa admin khác
    }
} else {
    echo "missing_data";
}
?>
