<?php include '../inc/config.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản người dùng</title>
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="admin_style/hienthi.css">
</head>
<body>
    <div class="container">
        <h2>Quản lý tài khoản người dùng</h2>
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
                        <option value="user_id">Mã người dùng</option>
                        <option value="username">Tên người dùng</option>
                    </select>
                    <input type="hidden" name="table" value="users">
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
                    <th>ID người dùng</th>
                    <th>Tên người dùng</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th class="text-center">Tác vụ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stt = 1;
                $sql = "SELECT * FROM users ORDER BY user_id DESC";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $isAdmin = $row['role'] === 'admin';
                        ?>
                        <tr>
                            <td>
                                <?php if (!$isAdmin): ?>
                                    <input type="checkbox" name="selected[]" value="<?= $row['user_id'] ?>" class="checkbox">
                                <?php endif; ?>
                            </td>
                            <td><?= $stt++ ?></td>
                            <td><?= $row['user_id'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td class="text-center actions">
                                <?php if (!$isAdmin): ?>
                                    <a href="actions/form.php?id=<?= $row['user_id'] ?>&table=users&id_column=user_id" 
                                    class="edit-btn promote-btn" 
                                    data-user-id="<?= $row['user_id'] ?>" 
                                    title="Cập nhật vai trò thành admin">
                                    <i class="fas fa-user-shield"></i>
                                    </a>
                                    <a href="actions/delete.php?id=<?= $row['user_id'] ?>&table=users&id_column=user_id&mode=redirect"
                                       title="Xóa" class="delete-btn ajax-link"
                                       data-confirm="Bạn có chắc chắn muốn xóa không?">
                                        <i class="fas fa-times-circle"></i>
                                    </a>
                                <?php else: ?>
                                    <span style="color: gray;">Không thể xóa</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Không có dữ liệu người dùng.</td></tr>";
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
            // Cập nhật checkbox tổng
            $('.checkbox').change(function () {
                $('#select_all').prop('checked', $('.checkbox:checked').length === $('.checkbox').length);
            });
            // Thực hiện tác vụ hàng loạt
            window.applyAction = function () {
                var action = $('#action_type').val();
                var selectedIds = [];
                $('.checkbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });
                if (selectedIds.length === 0) {
                    alert('Vui lòng chọn ít nhất một mục.');
                    return;
                }
                if (action === 'delete_selected') {
                    if (confirm('Bạn có chắc chắn muốn xóa các mục đã chọn không?')) {
                        var url = 'actions/delete.php?ids=' + selectedIds.join(',') + '&table=users&id_column=user_id';
                        $.get(url, function (response) {
                            if (response.trim() === 'success') {
                                $('#right').load('users.php');
                            } else {
                                alert('Lỗi từ server: ' + response);
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
                $.get('actions/search_handler.php', $(this).serialize(), function (data) {
                    $('tbody').html(data);
                });
            });
            // Đặt lại
            $('#reset-btn').on('click', function () {
                $('#search-form')[0].reset();
                $('#right').load('users.php');
            });
            // Cập nhật vai trò thành admin
            $(document).on('click', '.promote-btn', function (e) {
                e.preventDefault();
                const userId = $(this).data('user-id');
                if (confirm("Bạn có chắc chắn muốn nâng người dùng này thành admin không?")) {
                    $.post('actions/update_role.php', {
                        user_id: userId,
                        role: 'admin'
                    }, function (response) {
                        response = response.trim();
                        if (response === 'success') {
                            alert("Vai trò đã được cập nhật thành công!");
                            $('#right').load('users.php'); // reload bảng người dùng
                        } else if (response === 'cannot_change_own_role') {
                            alert("Bạn không thể thay đổi vai trò của chính mình.");
                        } else if (response === 'not_allowed') {
                            alert("Không được phép thay đổi vai trò này.");
                        } else if (response === 'invalid_role') {
                            alert("Vai trò không hợp lệ.");
                        } else if (response === 'user_not_found') {
                            alert("Người dùng không tồn tại.");
                        } else {
                            alert("Lỗi hệ thống: " + response);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
