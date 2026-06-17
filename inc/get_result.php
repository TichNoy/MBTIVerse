<?php
include 'config.php'; 
$user_id = $_SESSION['user_id'];
$sql = "SELECT r.personality_type, r.taken_at, pt.description
        FROM results r
        JOIN personality_types pt ON r.personality_type = pt.personality_type_id
        WHERE r.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="tab-content" id="tab-history">
    <h1>LỊCH SỬ LÀM BÀI</h1>
    <?php if ($result->num_rows > 0): ?>
        <table class="result-table">
            <tr>
                <th>Loại tính cách</th>
                <th>Mô tả</th>
                <th>Thời gian</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
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
