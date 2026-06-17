<?php
include '../inc/config.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý trường học đề xuất</title>
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="admin_style/hienthi.css">
</head>
<body>
    <div class="container">
        <h2>Quản lý trường học đề xuất phù hợp MBTI</h2>
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
                        <option value="school_name">Tên trường</option>
                    </select>
                    <input type="hidden" name="table" value="schools">
                    <button type="submit">Tìm</button>
                    <button type="button" id="reset-btn">Đặt lại</button>
                </form>
            </div>
            <a href="actions/form.php?table=schools" class="btn-add-new ajax-link">
                Thêm mới
            </a>
        </div>
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="select_all"></th>
                    <th>STT</th>
                    <th>ID Trường</th>
                    <th>Tên trường</th>
                    <th>URL</th>
                    <th class="text-center">Tác vụ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Số thứ tự ban đầu
                $stt = 1;
                // Lấy dữ liệu từ bảng questions
                $sql = "SELECT * FROM schools ORDER BY school_id DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    // Xuất dữ liệu của mỗi hàng
                    while($row = $result->fetch_assoc()) {
                ?>
                        <tr>
                            <td><input type="checkbox" name="selected[]" value="<?php echo $row['school_id']; ?>" class="checkbox"></td>
                            <td><?php echo $stt++; ?></td>
                            <td><?php echo $row['school_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['school_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="text-center actions">
                                <a href="actions/form.php?id=<?php echo $row['school_id']; ?>&table=schools&id_column=school_id" title="Sửa" class="edit-btn ajax-link">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="actions/delete.php?id=<?php echo $row['school_id']; ?>&table=schools&id_column=school_id&mode=redirect" title="Xóa" class="delete-btn ajax-link" data-confirm="Bạn có chắc chắn muốn xóa không?">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>Không dữ liệu trong database.</td></tr>";
                }
                $conn->close(); // Đóng kết nối database
                ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function () {
            // Chức năng chọn/bỏ chọn tất cả checkbox
            $('#select_all').click(function () {
                $('.checkbox').prop('checked', this.checked);
            });
            // Cập nhật trạng thái checkbox tổng
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
                        var url = 'actions/delete.php?ids='
                                    + selectedIds.join(',')
                                    + '&table=schools&id_column=school_id';
                        // Gọi AJAX thay vì chuyển trang
                        $.get(url, function (response) {
                            if (response.trim() === 'success') {
                                // Load lại nội dung majors vào #right
                                $('#right').load('schools.php');
                            } else {
                                alert('Lỗi từ server: ' + response);
                            }
                        });
                    }
                } else if (action === '') {
                    alert('Vui lòng chọn một tác vụ.');
                }
            };
             // Xử lý tìm kiếm
            $('#search-form').on('submit', function (e) {
                e.preventDefault();
                $.get('actions/search_handler.php', $(this).serialize(), function (data) {
                    $('tbody').html(data);
                });
            });
            $('#reset-btn').on('click', function () {
            // Xóa dữ liệu form
            $('#search-form')[0].reset();
            // Gọi lại danh sách gốc (không tìm kiếm)
            $('#right').load('schools.php'); 
            });    
        });
    </script>
</body>
</html>