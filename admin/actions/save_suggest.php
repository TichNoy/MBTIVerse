<?php 
include '../../inc/config.php';

$mbti = $_POST['mbti'] ?? '';
$major = $_POST['major'] ?? '';
$school = $_POST['school'] ?? '';

$old_mbti = $_POST['old_mbti'] ?? '';
$old_major = $_POST['old_major'] ?? '';
$old_school = $_POST['old_school'] ?? '';

if (!$mbti || !$major || !$school) {
    echo "Thiếu dữ liệu.";
    exit;
}

// Escape input
$mbti = $conn->real_escape_string($mbti);
$major = $conn->real_escape_string($major);
$school = $conn->real_escape_string($school);
$old_mbti = $conn->real_escape_string($old_mbti);
$old_major = $conn->real_escape_string($old_major);
$old_school = $conn->real_escape_string($old_school);
// Nếu đang sửa thì xóa liên kết cũ
if ($old_mbti && $old_major && $old_school) {
    $del1 = $conn->query("DELETE FROM personality_major WHERE personality_type_id='$old_mbti' AND major_id='$old_major'");
    $del2 = $conn->query("DELETE FROM major_school WHERE major_id='$old_major' AND school_id='$old_school'");

    if (!$del1 || !$del2) {
        echo "Lỗi khi xóa liên kết cũ.";
        exit;
    }
}
// Chèn lại nếu chưa tồn tại
$err = [];
$check1 = $conn->query("SELECT * FROM personality_major WHERE personality_type_id='$mbti' AND major_id='$major'");
if ($check1->num_rows == 0) {
    if (!$conn->query("INSERT INTO personality_major (personality_type_id, major_id) VALUES ('$mbti', '$major')")) {
        $err[] = "Lỗi khi chèn personality_major";
    }
}
$check2 = $conn->query("SELECT * FROM major_school WHERE major_id='$major' AND school_id='$school'");
if ($check2->num_rows == 0) {
    if (!$conn->query("INSERT INTO major_school (major_id, school_id) VALUES ('$major', '$school')")) {
        $err[] = "Lỗi khi chèn major_school";
    }
}
if (!empty($err)) {
    echo implode(" - ", $err);
} else {
    echo "success";
}
$conn->close();
