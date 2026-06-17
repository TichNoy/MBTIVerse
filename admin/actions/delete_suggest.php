<?php
include '../../inc/config.php';
$ids    = $_GET['ids'] ?? ($_GET['id'] ?? '');
$mode   = $_GET['mode'] ?? 'auto'; // auto | ajax | redirect
$redirect = $_GET['redirect'] ?? 'suggest.php';
if (!$ids) {
    die('Thiếu thông tin xóa.');
}
$id_array = explode(',', $ids);
$errors = [];
foreach ($id_array as $composite_id) {
    $parts = explode('_', $composite_id);
    if (count($parts) !== 3) {
        $errors[] = "ID không hợp lệ: $composite_id";
        continue;
    }
    [$personality_id, $major_id, $school_id] = $parts;
    // Chỉ xóa liên kết trường (major_school)
    $sql = "DELETE FROM major_school WHERE major_id = '$major_id' AND school_id = '$school_id'";
    if (!mysqli_query($conn, $sql)) {
        $errors[] = "Lỗi xóa major_school ($composite_id): " . mysqli_error($conn);
    }
}
// Trả về kết quả
$is_ajax = (
    $mode === 'ajax' || 
    ($mode === 'auto' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
);
if ($is_ajax) {
    echo empty($errors) ? 'success' : implode("\n", $errors);
} else {
    if (empty($errors)) {
        header("Location: ../suggest.php");
        exit;
    } else {
        echo implode("<br>", $errors);
    }
}
