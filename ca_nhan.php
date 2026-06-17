<?php
session_start();
include 'inc/config.php';
$user_id = $_SESSION['user_id']; 
// Lấy lịch sử làm bài của người dùng
$sql = "SELECT r.personality_type, r.taken_at, pt.description
        FROM results r
        JOIN personality_types pt ON r.personality_type = pt.personality_type_id
        WHERE r.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$historyResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Free MBTIVerse</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/ca_nhan.css">
    <link rel="stylesheet" href="style/header.css">
</head>
<body>
<?php include 'inc/header.php'; ?>
<div class="center-section">
    <div class="left-frame">
        <div class="user-profile">
            <img src="assets/images/admin.jpg" alt="Avatar" class="avatar">
            <div class="username"><?= htmlspecialchars($_SESSION['username']) ?></div>
        </div>
        <ul class="menu">
            <li onclick="showTab('tab-profile')">Thông tin cá nhân</li>
            <li onclick="showTab('tab-history')">Lịch sử kết quả làm bài</li>
            <li onclick="showTab('tab-settings')">Cài đặt</li>
        </ul>
        <hr>
        <ul class="menu">
            <li>Liên lạc với chúng tôi</li>
            <li><a href="inc/logout.php">Đăng xuất</a></li>
        </ul>
    </div>
    <div class="right-frame">
        <!-- Thông tin cá nhân -->
        <div class="tab-content active" id="tab-profile">
            <h1>TRANG THÔNG TIN TÀI KHOẢN CÁ NHÂN</h1>
            <div class="user-info-card" id="userInfoCard">
                <h2>Thông tin người dùng</h2>
                <div class="info-row">
                    <span class="label">Tên người dùng</span>
                    <span class="value" id="usernameDisplay"><?= htmlspecialchars($_SESSION['username']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Email</span>
                    <span class="value" id="emailDisplay"><?= htmlspecialchars($_SESSION['email']) ?></span>
                </div>
                <div class="button-group">
                    <button class="btn-edit" id="btnEdit">SỬA</button>
                </div>
            </div>
            <form class="user-info-card" id="editProfileForm" method="POST" action="inc/update_user.php" style="display: none;">
                <h2>Chỉnh sửa thông tin</h2>
                <div class="form-group">
                    <label for="editUsername">Tên người dùng:</label>
                    <input type="text" id="editUsername" name="username" class="form-control">
                </div>
                <div class="form-group">
                    <label for="editEmail">Email:</label>
                    <input type="email" id="editEmail" name="email" class="form-control">
                </div>
                <div class="button-group">
                <button class="btn-save" type="submit">LƯU</button>
                    <button class="btn-cancel" id="btnCancelEdit">HỦY</button>
                </div>
            </div>
        </div>
       <!-- Lịch sử kết quả -->
        <div class="tab-content" id="tab-history">
            <h1>LỊCH SỬ LÀM BÀI</h1>
            <div class="user-info-card">
                <?php if ($historyResult->num_rows > 0): ?>
                    <table class="result-table">
                        <tr>
                            <th>Loại tính cách</th>
                            <th>Mô tả</th>
                            <th>Thời gian</th>
                        </tr>
                        <?php while ($row = $historyResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['personality_type']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['taken_at'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>Bạn chưa thực hiện bài kiểm tra nào.</p>
                <?php endif; ?>
            </div>
        </div>
        <!-- Cài đặt -->
        <div class="tab-content" id="tab-settings">
            <h1>CÀI ĐẶT</h1>
            <p>Chức năng đang được phát triển.</p>
        </div>
    </div>
</div>
<script>
function showTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
}
document.getElementById("btnEdit").onclick = function() {
    document.getElementById("userInfoCard").style.display = "none";
    document.getElementById("editProfileForm").style.display = "block";
    document.getElementById("editUsername").value = document.getElementById("usernameDisplay").innerText;
    document.getElementById("editEmail").value = document.getElementById("emailDisplay").innerText;
};
document.getElementById("btnCancelEdit").onclick = function() {
    document.getElementById("editProfileForm").style.display = "none";
    document.getElementById("userInfoCard").style.display = "block";
};
</script>
</body>
</html>
