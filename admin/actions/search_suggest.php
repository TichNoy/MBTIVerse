<?php
include '../../inc/config.php';
$search_by = $_GET['search_by'] ?? '';
$keyword   = $_GET['keyword'] ?? '';
$allowed_columns = ['personality_type_id', 'type_name'];
if ($keyword === '' || !in_array($search_by, $allowed_columns)) {
    die('<tr><td colspan="7" class="text-center">Không có từ khóa hoặc cột không hợp lệ.</td></tr>');
}
$keyword = $conn->real_escape_string($keyword);
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
    WHERE pt.$search_by LIKE '%$keyword%'
    ORDER BY pt.personality_type_id, m.major_name, s.school_name
";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $stt = 1;
    while ($row = $result->fetch_assoc()) {
        $row_id = $row['personality_type_id'] . '_' . $row['major_id'] . '_' . $row['school_id'];
        echo "<tr>";
        echo "<td><input type='checkbox' class='checkbox' name='selected[]' value='$row_id'></td>";
        echo "<td>" . $stt++ . "</td>";
        echo "<td>" . htmlspecialchars($row['personality_type_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['type_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['major_name'] ?? '---') . "</td>";
        echo "<td>" . htmlspecialchars($row['school_name'] ?? '---') . "</td>";
        echo "<td class='text-center actions'>
                <a href='form_suggest.php?table=suggest&id={$row['personality_type_id']}&major={$row['major_id']}&school={$row['school_id']}' class='edit-btn ajax-link'><i class='fas fa-edit'></i></a>
                <a href='delete_suggest.php?id={$row['personality_type_id']}&major={$row['major_id']}&school={$row['school_id']}' class='delete-btn ajax-link'  data-confirm='Bạn có chắc chắn muốn xóa không?'><i class='fas fa-times-circle'></i></a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>Không tìm thấy kết quả.</td></tr>";
}

$conn->close();
