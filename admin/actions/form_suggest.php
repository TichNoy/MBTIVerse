<script src="../assets/js/jquery-3.7.1.min.js"></script>
<style> 
    h2 {
        margin-top: 20px;
        margin-left: 200px;
    }
    form {
        width: 700px;
        margin-left: 200px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    textarea, input[type="text"], select {
        width: 500px;
        height: 38px;
        padding: 6px;
        font-size: 15px;
    }
    button, a {
        width: fit-content;
        padding: 6px 12px;
        margin-top: 10px;
    }
</style>
<?php
include '../../inc/config.php';
$mbti = $_GET['id'] ?? '';
$major = $_GET['major'] ?? '';
$school = $_GET['school'] ?? '';
$is_edit = $mbti && $major && $school;
?>
<h2><?= $is_edit ? "Sửa" : "Thêm mới" ?> đề xuất thông minh</h2>
<form action="actions/save_suggest.php" method="POST" class="ajax-form">
    <!-- Truyền thông tin gốc nếu là sửa -->
    <?php if ($is_edit): ?>
        <input type="hidden" name="old_mbti" value="<?= htmlspecialchars($mbti) ?>">
        <input type="hidden" name="old_major" value="<?= htmlspecialchars($major) ?>">
        <input type="hidden" name="old_school" value="<?= htmlspecialchars($school) ?>">
        <input type="hidden" name="edit_mode" value="1">
    <?php endif; ?>
    <!-- MBTI -->
    <label>Nhóm tính cách (MBTI):</label>
    <select name="mbti" required <?= $is_edit ?  : '' ?>>
        <option value="">-- Chọn MBTI --</option>
        <?php
        $rs = mysqli_query($conn, "SELECT * FROM personality_types");
        while ($row = mysqli_fetch_assoc($rs)) {
            $selected = ($row['personality_type_id'] == $mbti) ? 'selected' : '';
            echo "<option value='{$row['personality_type_id']}' $selected>{$row['type_name']} ({$row['personality_type_id']})</option>";
        }
        ?>
    </select>
    <!-- Ngành học -->
    <label>Ngành học:</label>
    <select name="major" required <?= $is_edit ?  : '' ?>>
        <option value="">-- Chọn ngành --</option>
        <?php
        $rs = mysqli_query($conn, "SELECT * FROM majors");
        while ($row = mysqli_fetch_assoc($rs)) {
            $selected = ($row['major_id'] == $major) ? 'selected' : '';
            echo "<option value='{$row['major_id']}' $selected>{$row['major_name']}</option>";
        }
        ?>
    </select>
    <!-- Trường học -->
    <label>Trường học:</label>
    <select name="school" required>
        <option value="">-- Chọn trường --</option>
        <?php
        $rs = mysqli_query($conn, "SELECT * FROM schools");
        while ($row = mysqli_fetch_assoc($rs)) {
            $selected = ($row['school_id'] == $school) ? 'selected' : '';
            echo "<option value='{$row['school_id']}' $selected>{$row['school_name']}</option>";
        }
        ?>
    </select>
    <button type="submit">Lưu</button>
    <a href="suggest.php" class="ajax-link">Quay lại</a>
</form>
<script>
$(document).ready(function() {
    $('form.ajax-form').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const formData = form.serialize();
        $.post('actions/save_suggest.php', formData, function(response) {
            if (response.trim() === 'success') {
                $('#right').load('suggest.php');
            } else {
                alert("Lỗi: " + response);
            }
        });
    });
});
</script>
