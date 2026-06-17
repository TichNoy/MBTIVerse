<script src="../assets/js/jquery-3.7.1.min.js"></script>
<style> 
    h2{
        margin-top: 20px;
        margin-left: 200px;
    }
    form {
    width: 700px;
    text-align: left;
    margin-left: 200px;
    flex-direction: column;
    }
    textarea,  input[type="text"] {
    width: 500px;
    height: 100px;
    }
    select{
        height: auto; 
    }
</style>
<?php include '../../inc/config.php'; 
$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';
// Cấu hình form theo từng bảng
$schemas = [
    'questions' => [
        'title' => 'Quản lý Câu hỏi',
        'fields' => [
            'question_text' => ['label' => 'Nội dung câu hỏi', 'type' => 'textarea'],
            'category' => ['label' => 'Danh mục', 'type' => 'select', 'options' => ['EI' => 'EI', 'SN' => 'SN', 'TF' => 'TF', 'JP' => 'JP']],
            'trait_positive' => ['label' => 'Chiều hướng ngoại cảnh', 'type' => 'select', 'options' => ['I' => 'I - Introversion', 'S' => 'S - Sensing', 'T' => 'T - Thinking', 'J' => 'J - Judging']],
            'trait_negative' => ['label' => 'Chiều hướng nội tâm', 'type' => 'select', 'options' => ['E' => 'E - Extraversion', 'N' => 'N - Intuition', 'F' => 'F - Feeling', 'P' => 'P - Perceiving']],
        ]
    ],
    'personality_types' => [
        'title' => 'Quản lý tính cách',
        'fields' => [
            'personality_type_id' => ['label' => 'Mã loại (ví dụ INFP)', 'type' => 'text'],
            'type_name' => ['label' => 'Tên loại', 'type' => 'text'],
            'description' => ['label' => 'Mô tả', 'type' => 'textarea'],
            'strengths' => ['label' => 'Điểm mạnh', 'type' => 'textarea'],
            'weaknesses' => ['label' => 'Điểm yếu', 'type' => 'textarea'],
        ]
    ],
    'majors' => [
        'title' => 'Quản lý ngành học đề xuất',
        'fields' => [
            'major_name' => ['label' => 'Tên ngành', 'type' => 'text'],
            'description' => ['label' => 'Mô tả', 'type' => 'textarea'],
        ]
    ],
    'schools' => [
        'title' => 'Quản lý trường học đề xuất',
        'fields' => [
            'school_name' => ['label' => 'Tên trường', 'type' => 'text'],
            'description' => ['label' => 'Mô tả', 'type' => 'textarea'],
        ]
    ]
];
// Kiểm tra hợp lệ
if (!isset($schemas[$table])) {
    die("Bảng không hợp lệ.");
}
$schema = $schemas[$table];
$row = [];
foreach ($schema['fields'] as $field => $props) {
    $row[$field] = '';
}
if ($id) {
    $id_columns = [
        'majors' => 'major_id',
        'questions' => 'question_id',
        'schools' => 'school_id',
        'personality_types' => 'personality_type_id'
    ];
    $id_column = $id_columns[$table] ?? die("Không xác định được cột ID cho bảng $table");
    $safe_id = is_numeric($id) ? intval($id) : "'" . mysqli_real_escape_string($conn, $id) . "'";
    $sql = "SELECT * FROM `$table` WHERE `$id_column` = $safe_id";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Lỗi truy vấn: " . mysqli_error($conn));
    }
    $row = mysqli_fetch_assoc($result) ?? die("Không tìm thấy bản ghi với ID: $id");
}
?>
<h2><?= $id ? "Sửa" : "Thêm mới" ?> <?= $schema['title'] ?></h2>
<form action="actions/save.php" method="POST" class="ajax-form">
    <input type="hidden" name="table" value="<?= htmlspecialchars($table) ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
    <?php foreach ($schema['fields'] as $field => $props): ?>
        <label><?= $props['label'] ?>:</label><br>
        <?php if ($props['type'] === 'textarea'): ?>
            <textarea name="<?= $field ?>" rows="4" required><?= htmlspecialchars($row[$field] ?? '') ?></textarea>
        <?php elseif ($props['type'] === 'select'): ?>
            <select name="<?= $field ?>" required>
                <?php foreach ($props['options'] as $val => $label): ?>
                    <option value="<?= $val ?>" <?= ($row[$field] ?? '') == $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <input type="<?= $props['type'] ?>" name="<?= $field ?>" value="<?= htmlspecialchars($row[$field] ?? '') ?>" required>
        <?php endif; ?>
        <br><br>
    <?php endforeach; ?>
    <button type="submit">Lưu</button>
    <a href="<?= $table ?>.php" class="ajax-link">Quay lại</a>
</form>
<!-- xử lí form sau khi submit hiển thị ngay ở div right -->
<script>
$(document).ready(function() {
    $('form.ajax-form').submit(function(e) {
        e.preventDefault(); // Ngăn reload trang
        const form = $(this);
        const formData = form.serialize();
        $.post('actions/save.php', formData, function(response) {
            if (response.trim() === 'success') {
                const table = form.find('input[name="table"]').val();
                $('#right').load(table + '.php'); // Tải lại danh sách vào div.right
            } else {
                alert("Lỗi: " + response);
            }
        });
    });
});
</script>