<?php
session_start();
include 'config.php';
// Trả dữ liệu dạng JSON
header('Content-Type: application/json');
// Kiểm tra dữ liệu từ form
if (!isset($_POST['answer']) || empty($_POST['answer'])) {
    echo json_encode(['status' => 'error', 'message' => 'Không có câu trả lời nào được gửi!']);
    exit;
}
$answers = $_POST['answer'];
$personality_type = calculatePersonalityType($answers);
// Nếu người dùng chưa đăng nhập, chỉ trả kết quả (không lưu DB)
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'guest',
        'message' => 'Bạn chưa đăng nhập. Kết quả không được lưu.',
        'personality_type' => $personality_type
    ]);
    exit;
}
// === Nếu đã đăng nhập thì lưu kết quả ===
$user_id = $_SESSION['user_id'];
// Ghi hoặc cập nhật kết quả MBTI vào bảng `results`
$stmt = $conn->prepare("REPLACE INTO results (user_id, personality_type) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $personality_type);
$stmt->execute();
$result_id = $conn->insert_id;
$stmt->close();
// Xoá các câu trả lời cũ (nếu có)
$conn->query("DELETE FROM user_answers WHERE result_id = $result_id");
// Ghi câu trả lời mới vào bảng `user_answers`
$stmt2 = $conn->prepare("INSERT INTO user_answers (result_id, question_id, selected_score) VALUES (?, ?, ?)");
foreach ($answers as $question_id => $score) {
    $stmt2->bind_param("iii", $result_id, $question_id, $score);
    $stmt2->execute();
}
$stmt2->close();
// Trả phản hồi thành công
echo json_encode([
    'status' => 'success',
    'message' => 'Kết quả đã được lưu thành công.',
    'personality_type' => $personality_type
]);
exit;
// === Hàm tính toán MBTI ===
function calculatePersonalityType($answers) {
    $scores = ['EI' => 0, 'SN' => 0, 'TF' => 0, 'JP' => 0];
    $conn = new mysqli("localhost", "root", "", "mbti_schema");
    $conn->set_charset("utf8");
    foreach ($answers as $question_id => $score) {
        $sql = "SELECT category FROM questions WHERE question_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row) {
            $category = $row['category'];
            if (isset($scores[$category])) {
                $scores[$category] += $score;
            }
        }
        $stmt->close();
    }
    $conn->close();
    // Ghép kết quả
    $result = '';
    $result .= $scores['EI'] > 0 ? 'E' : 'I';
    $result .= $scores['SN'] > 0 ? 'S' : 'N';
    $result .= $scores['TF'] > 0 ? 'T' : 'F';
    $result .= $scores['JP'] > 0 ? 'J' : 'P';
    return $result;
}
?>
