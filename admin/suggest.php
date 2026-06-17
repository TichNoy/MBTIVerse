<?php
include '../inc/config.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đề xuất thông minh</title>
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="admin_style/hienthi.css">
</head>
<body>
<div class="container">
    <h2>Đề xuất thông minh</h2>
    <div class="action-bar">
        <div class="left-actions">
            <select name="action_type" id="action_type">
                <option value="">Tác vụ</option>
                <option value="delete_selected">Xóa mục đã chọn</option>
            </select>
            <input type="button" value="Thực hiện" onclick="applyAction()">
            <form id="search-form" style="display: inline-flex; gap: 8px; align-items: center;">
                <input type="text" name="keyword" placeholder="Tìm kiếm..." required>
                <select name="search_by" required>
                    <option value="">Tìm theo</option>
                    <option value="personality_type_id">Mã nhóm tính cách</option>
                    <option value="type_name">Tên nhóm tính cách</option>
                </select>
                <button type="submit">Tìm</button>
                <button type="button" id="reset-btn">Đặt lại</button>
            </form>
        </div>
        <a href="actions/form_suggest.php" class="btn-add-new ajax-link">
           Thêm mới
        </a>
    </div>
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="select_all"></th>
                <th>STT</th>
                <th>MBTI</th>
                <th>Tên nhóm tính cách</th>
                <th>Tên ngành</th>
                <th>Tên trường</th>
                <th class="text-center">Tác vụ</th>
            </tr>
        </thead>
        <tbody id="data-table">
            <?php
            $stt = 1;
            $sql = "
                SELECT 
                    pt.personality_type_id,
                    pt.type_name,
                    m.major_id,
                    m.major_name,
                    s.school_id,
                    s.school_name
                FROM 
                    personality_major pm
                LEFT JOIN personality_types pt ON pm.personality_type_id = pt.personality_type_id
                LEFT JOIN majors m ON pm.major_id = m.major_id
                LEFT JOIN major_school ms ON m.major_id = ms.major_id
                LEFT JOIN schools s ON ms.school_id = s.school_id
                ORDER BY pt.personality_type_id, m.major_name, s.school_name
            ";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $row_id = $row['personality_type_id'] . '_' . $row['major_id'] . '_' . $row['school_id'];
                    echo "<tr>";
                    echo "<td><input type='checkbox' class='checkbox' name='selected[]' value='$row_id'></td>";
                    echo "<td>" . $stt++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['personality_type_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['type_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['major_name'] ?? '---') . "</td>";
                    echo "<td>" . htmlspecialchars($row['school_name'] ?? '---') . "</td>";
                    echo "<td class='text-center actions'>
                            <a href='actions/form_suggest.php?id={$row['personality_type_id']}&major={$row['major_id']}&school={$row['school_id']}' class='edit-btn ajax-link'>
                                <i class='fas fa-edit'></i>
                            </a>
                            <a href='actions/delete_suggest.php?ids={$row_id}&redirect=suggest.php' 
                            class='delete-btn ajax-link' 
                            data-confirm='Bạn có chắc chắn muốn xóa liên kết này không?'>
                            <i class='fas fa-times-circle'></i>
                            </a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>Không có dữ liệu.</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>
<script>
$(document).ready(function () {
    // Chọn tất cả
    $('#select_all').click(function () {
        $('.checkbox').prop('checked', this.checked);
    });
    $('.checkbox').change(function () {
        $('#select_all').prop('checked', $('.checkbox:checked').length === $('.checkbox').length);
    });
    // Xử lý tác vụ hàng loạt
    window.applyAction = function () {
        var action = $('#action_type').val();
        var selectedIds = [];
        $('.checkbox:checked').each(function () {
            selectedIds.push($(this).val());
        });
        if (selectedIds.length === 0) {
            alert('Vui lòng chọn ít nhất một mục để thực hiện tác vụ.');
            return;
        }
        if (action === 'delete_selected') {
            if (confirm('Bạn có chắc chắn muốn xóa các mục đã chọn không?')) {
                var url = 'actions/delete_suggest.php?ids=' + selectedIds.join(',');
                $.get(url, function (response) {
                    if (response.trim() === 'success') {
                        $('#right').load('suggest.php');
                    } else {
                        alert('Lỗi: ' + response);
                    }
                });
            }
        } else {
            alert('Vui lòng chọn một tác vụ.');
        }
    };
    // Tìm kiếm
    $('#search-form').on('submit', function (e) {
        e.preventDefault();
        $.get('actions/search_suggest.php', $(this).serialize(), function (data) {
            $('#data-table').html(data);
        });
    });
    // Đặt lại tìm kiếm
    $('#reset-btn').on('click', function () {
        $('#search-form')[0].reset();
        $('#right').load('suggest.php');
    });
});
</script>
</body>
</html>
