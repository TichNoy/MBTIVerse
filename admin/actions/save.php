<?php
include '../../inc/config.php';
$table = $_POST['table'] ?? '';
$id = $_POST['id'] ?? '';
// Danh sách các bảng được phép thao tác
$allowed_tables = [
    'majors' => 'major_id',
    'schools' => 'school_id',
    'questions' => 'question_id',
    'personality_types' => 'personality_type_id'
];
if (!array_key_exists($table, $allowed_tables)) {
    die('Bảng không hợp lệ.');
}
$id_column = $allowed_tables[$table];
// Xử lý dữ liệu đầu vào
$data = $_POST;
unset($data['table'], $data['id']);
$fields = [];
foreach ($data as $key => $val) {
    $safe_val = mysqli_real_escape_string($conn, $val);
    $fields[] = "`$key` = '$safe_val'";
}
if ($id) {
    // Update
    $id_safe = mysqli_real_escape_string($conn, $id);
    $sql = "UPDATE `$table` SET " . implode(', ', $fields) . " WHERE `$id_column` = '$id_safe'";
} else {
    // Insert
    $sql = "INSERT INTO `$table` SET " . implode(', ', $fields);
}
if (mysqli_query($conn, $sql)) {
    echo "success";
} else {
    echo "Lỗi: " . mysqli_error($conn);
}

