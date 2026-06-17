<?php
include '../inc/config.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Kết quả Trắc nghiệm</title>
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="admin_style/hienthi.css">
</head>
<body>
    <div class="container">
        <h2>Quản lý kết quả làm bài MBTI</h2>
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
                        <option value="user_id">ID người dùng</option>
                        <option value="username">Tên người dùng</option>
                    </select>
                    <input type="hidden" name="table" value="results">
                    <button type="submit">Tìm</button>
                    <button type="button" id="reset-btn">Đặt lại</button>
                </form>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="select_all"></th>
                    <th>STT</th>
                    <th>ID kết quả</th>
                    <th>ID người dùng</th>
                    <th>Tên người dùng</th>
                    <th>Nhóm tính cách</th>
                    <th>Thời điểm nộp bài</th>
                    <th class="text-center">Tác vụ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stt = 1;
                // Sửa lại cột JOIN đúng là users.user_id
                $sql = "SELECT results.*, users.username 
                        FROM results 
                        JOIN users ON results.user_id = users.user_id 
                        ORDER BY results.result_id DESC";
                $result = $conn->query($sql);
                // Kiểm tra lỗi SQL
                if (!$result) {
                    echo "<tr><td colspan='8' class='text-center text-error'>Lỗi truy vấn: " . $conn->error . "</td></tr>";
                } elseif ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><input type="checkbox" name="selected[]" value="<?php echo $row['result_id']; ?>" class="result-checkbox"></td>
                            <td><?php echo $stt++; ?></td>
                            <td><?php echo $row['result_id']; ?></td>
                            <td><?php echo $row['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo $row['personality_type']; ?></td>
                            <td><?php echo $row['taken_at']; ?></td>
                            <td class="text-center actions">
                                <a href="actions/delete.php?id=<?php echo $row['result_id']; ?>&table=results&id_column=result_id&mode=redirect" title="Xóa" class="delete-btn ajax-link" data-confirm="Bạn có chắc chắn muốn xóa không?">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>Không có kết quả nào trong database.</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function () {
            // Chức năng chọn/bỏ chọn tất cả checkbox
            $('#select_all').click(function () {
                $('.result-checkbox').prop('checked', this.checked);
            });
            // Cập nhật trạng thái checkbox tổng
            $('.result-checkbox').change(function () {
                $('#select_all').prop('checked', $('.result-checkbox:checked').length === $('.result-checkbox').length);
            });
            // Xử lý tác vụ hàng loạt
            window.applyAction = function () {
                var action = $('#action_type').val();
                var selectedIds = [];
                $('.result-checkbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });
                if (selectedIds.length === 0) {
                    alert('Vui lòng chọn ít nhất một mục để thực hiện tác vụ.');
                    return;
                }
                if (action === 'delete_selected') {
                    if (confirm('Bạn có chắc chắn muốn xóa các mục đã chọn không?')) {
                        var url = 'actions/delete.php?ids=' + selectedIds.join(',') + '&table=results&id_column=result_id';
                        $.get(url, function (response) {
                            if (response.trim() === 'success') {
                                $('#right').load('results.php');
                            } else {
                                alert('Lỗi từ server: ' + response);
                            }
                        });
                    }
                } else if (action === '') {
                    alert('Vui lòng chọn một tác vụ.');
                }
            };
            // Tìm kiếm
            $('#search-form').on('submit', function (e) {
                e.preventDefault();
                $.get('actions/search_handler.php', $(this).serialize(), function (data) {
                    $('tbody').html(data);
                });
            });
            // Đặt lại
            $('#reset-btn').on('click', function () {
                $('#search-form')[0].reset();
                $('#right').load('results.php');
            });
        });
    </script>
</body>
</html>
