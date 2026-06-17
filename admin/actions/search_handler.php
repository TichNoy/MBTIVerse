<?php
include '../../inc/config.php'; // Kết nối DB
// Lấy input từ GET
$table      = $_GET['table'] ?? '';
$search_by  = $_GET['search_by'] ?? '';
$keyword    = $_GET['keyword'] ?? '';
// Danh sách bảng hợp lệ và các cột được phép tìm kiếm
$allowed_tables = [
    'majors' => ['major_id', 'major_name', 'description'],
    'schools' => ['school_id', 'school_name', 'description'],
    'questions' => ['question_id', 'category', 'question_text'],
    'personality_types' => ['personality_type_id', 'type_name'],
    'users' => ['user_id', 'username', 'email', 'role'],
    'results' => ['result_id', 'user_id', 'personality_type_id', 'taken_at', 'username'] 
];
// Kiểm tra hợp lệ
if (!array_key_exists($table, $allowed_tables)) {
    echo "Bảng không hợp lệ.";
    exit;
}
if ($keyword !== '' && !in_array($search_by, $allowed_tables[$table])) {
    echo "Cột tìm kiếm không hợp lệ.";
    exit;
}
// Xử lý câu truy vấn
$keyword = $conn->real_escape_string($keyword);
if ($table === 'results') {
    if ($keyword === '') {
        $sql = "SELECT results.*, users.username 
                FROM results 
                JOIN users ON results.user_id = users.user_id 
                ORDER BY result_id DESC";
        $result = $conn->query($sql);
    } else {
        $escaped = "%$keyword%";
        if ($search_by === 'username') {
            $sql = "SELECT results.*, users.username 
                    FROM results 
                    JOIN users ON results.user_id = users.user_id 
                    WHERE users.username LIKE ? 
                    ORDER BY result_id DESC";
        } elseif ($search_by === 'personality_type_id') {
            $sql = "SELECT results.*, users.username 
                    FROM results 
                    JOIN users ON results.user_id = users.user_id 
                    WHERE results.personality_type_id LIKE ? 
                    ORDER BY result_id DESC";
        } else {
            $sql = "SELECT results.*, users.username 
                    FROM results 
                    JOIN users ON results.user_id = users.user_id 
                    WHERE results.$search_by LIKE ? 
                    ORDER BY result_id DESC";
        }
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $escaped);
        $stmt->execute();
        $result = $stmt->get_result();
    }
} else {
    if ($keyword === '') {
        $sql = "SELECT * FROM $table ORDER BY 1 DESC";
        $result = $conn->query($sql);
    } else {
        $sql = "SELECT * FROM $table WHERE $search_by LIKE '%$keyword%' ORDER BY 1 DESC";
        $result = $conn->query($sql);
    }
}
if ($result === false) {
    echo "<tr><td colspan='8' class='text-center text-error'>Lỗi truy vấn: " . $conn->error . "</td></tr>";
    exit;
}
if ($result->num_rows > 0) {
    $stt = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        // Lấy ID động
        $id_col = array_keys($row)[0];
        $id_val = $row[$id_col];
        echo "<td><input type='checkbox' name='selected[]' value='$id_val' class='checkbox'></td>";
        echo "<td>" . ($stt++) . "</td>";
        // HIỂN THỊ CỘT DỮ LIỆU
        if ($table === 'majors') {
            echo "<td>{$row['major_id']}</td>";
            echo "<td>" . htmlspecialchars($row['major_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        } elseif ($table === 'schools') {
            echo "<td>{$row['school_id']}</td>";
            echo "<td>" . htmlspecialchars($row['school_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        } elseif ($table === 'questions') {
            echo "<td>{$row['question_id']}</td>";
            echo "<td>" . htmlspecialchars($row['question_text']) . "</td>";
            echo "<td>{$row['category']}</td>";
            echo "<td>" . htmlspecialchars($row['trait_positive']) . "</td>";
            echo "<td>" . htmlspecialchars($row['trait_negative']) . "</td>";
        } elseif ($table === 'personality_types') {
            echo "<td>{$row['personality_type_id']}</td>";
            echo "<td>" . htmlspecialchars($row['type_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        } elseif ($table === 'users') {
            echo "<td>{$row['user_id']}</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['role']) . "</td>";  
        } elseif ($table === 'results') {
            echo "<td>{$row['result_id']}</td>";
            echo "<td>{$row['user_id']}</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>{$row['personality_type']}</td>"; 
            echo "<td>{$row['taken_at']}</td>";
        }
        // TÁC VỤ
        echo "<td class='text-center actions'>";
        if ($table === 'results') {
            // Chỉ hiển thị nút Xóa
            echo "<a href='actions/delete.php?id=$id_val&table=$table&id_column=$id_col' 
                    class='delete-btn ajax-link' 
                    data-confirm='Bạn có chắc chắn muốn xóa không?'>
                    <i class='fas fa-times-circle'></i>
                </a>";
        } else {
            // Hiển thị cả Sửa và Xóa
            echo "<a href='actions/form.php?id=$id_val&table=$table&id_column=$id_col' 
                    class='edit-btn ajax-link'>
                    <i class='fas fa-edit'></i>
                </a>";
            echo "<a href='actions/delete.php?id=$id_val&table=$table&id_column=$id_col' 
                    class='delete-btn ajax-link' 
                     data-confirm='Bạn có chắc chắn muốn xóa không?'>
                    <i class='fas fa-times-circle'></i>
                </a>";
        }
        echo "</td>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>Không tìm thấy kết quả phù hợp.</td></tr>";
}
$conn->close();
?>