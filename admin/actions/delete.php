<?php
include '../../inc/config.php';
$table      = $_GET['table'] ?? '';
$id_column  = $_GET['id_column'] ?? '';
$ids        = $_GET['ids'] ?? ($_GET['id'] ?? '');
$mode       = $_GET['mode'] ?? 'auto';  // auto | ajax | redirect
if (!$table || !$id_column || !$ids) {
    die('Thiếu thông tin xóa.');
}
// Tách các ID (dù là 1 hay nhiều)
$id_array = explode(',', $ids);
$safe_ids = array_map(function($id) use ($conn) {
    return "'" . mysqli_real_escape_string($conn, $id) . "'";
}, $id_array);
$in = implode(',', $safe_ids);
// Câu lệnh SQL
$sql = "DELETE FROM `$table` WHERE `$id_column` IN ($in)";
$result = mysqli_query($conn, $sql);
// Giao diện phản hồi
if ($result) {
    if (
        $mode === 'ajax' || 
        ($mode === 'auto' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    ) {
        echo "success";
    } else {
        header("Location: ../{$table}.php");
        exit;
    }
} else {
    echo "Lỗi: " . mysqli_error($conn);
}
