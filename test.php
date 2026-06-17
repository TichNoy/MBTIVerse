<?php
include 'inc/bootstrap.php';

echo "<pre>";
var_dump($_SESSION);
echo "</pre>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Free MBTIVerse</title>
  <link rel="stylesheet" href="style/style.css">
  <link rel="stylesheet" href="style/test.css">
  <link rel="stylesheet" href="style/header.css">    
</head>
<body>
  <?php include 'inc/header.php'; ?>
  <?php
  // nhắc nhở
  if (!isset($_SESSION['user_id'])) {
    echo '<p style="color: red; text-align: center; font-weight: bold; margin-top: 10px;">
      ⚠ Bạn có thể làm bài test mà không cần đăng nhập.<br>
      Tuy nhiên, hãy đăng nhập nếu muốn lưu kết quả và xem lại sau này.<br>
    </p>';
  }?>
  <section class="main-content">
    <div class="heading-content">
      <h1>TRẮC NGHIỆM TÍNH CÁCH MBTI</h1>
    </div>
    <!-- Bắt đầu thẻ Form -->
    <form id="mbti-form" method="POST" action="inc/submit.php">
      <?php include 'inc/config.php';
      // Lấy tất cả câu hỏi
      $sql = "SELECT question_ID, question_text FROM questions ORDER BY question_ID ASC";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              echo '<div class="question-box">';
              echo '<p class="question-text"><strong>' . $row["question_ID"] . '.</strong> ' . $row["question_text"] . '</p>';
              // Trường ẩn để lưu câu trả lời cho mỗi câu hỏi
              echo '<input type="hidden" name="answer[' . $row["question_ID"] . ']" value="">';
              // Lựa chọn (nút)
              echo '<div class="choices">
                    <span class="label agree">Đồng ý</span>
                      <button class="circle circle-large green" type="button" onclick="selectChoice(this, ' . $row["question_ID"] . ', 3)"></button>
                      <button class="circle circle-medium green" type="button" onclick="selectChoice(this, ' . $row["question_ID"] . ', 2)"></button>
                      <button class="circle circle-small green" type="button" onclick="selectChoice(this, ' . $row["question_ID"] . ', 1)"></button>
                      <button class="circle circle-small neutral" type="button" onclick="selectChoice(this, ' . $row["question_ID"] . ', 0)"></button>
                      <button class="circle circle-small purple" type="button" onclick="selectChoice(this, ' . $row["question_ID"] . ', -1)"></button>
                      <button class="circle circle-medium purple" type="button" onclick="selectChoice(this, ' . $row["question_ID"] . ', -2)"></button>
                      <button class="circle circle-large purple" type="button" onclick="selectChoice(this, ' . $row["question_ID"] . ', -3)"></button>
                      <span class="label disagree">Không đồng ý</span>
                    </div>';
              echo '</div>';
          }
      } else {
          echo '<p>Không có câu hỏi nào.</p>';
      }
      $conn->close();
      ?>
      <div class="submit-container">
        <button type="submit" class="submit-btn">Gửi câu trả lời</button>
      </div>
    </form>
    <!-- Kết thúc thẻ Form -->
    <!-- Popup hiển thị kết quả -->
    <div class="overlay" id="popup-overlay"></div>
    <div class="popup" id="result-popup">
      <h2 id="popup-title"></h2>
      <p id="popup-content"></p>
      <button onclick="closePopup()">Đóng</button>
    </div>
  </section>
  <script>
    function selectChoice(button, question_id, score) {
      // Cập nhật giá trị cho trường ẩn với điểm đã chọn
      document.querySelector('input[name="answer[' + question_id + ']"]').value = score;
      // Xóa lớp 'selected' khỏi tất cả các nút và thêm vào nút đã chọn
      const group = button.parentElement;
      group.querySelectorAll('.circle').forEach(btn => {
          btn.classList.remove('selected');
      });
      button.classList.add('selected');
    }
    // Xử lý gửi form qua AJAX
    document.getElementById('mbti-form').addEventListener('submit', function(e) {
      e.preventDefault(); // Ngăn submit form mặc định
      // Kiểm tra xem tất cả câu hỏi đã được trả lời chưa
      const answerInputs = document.querySelectorAll('input[name^="answer"]');
      let allAnswered = true;
      answerInputs.forEach(input => {
        if (input.value === '') {
          allAnswered = false;
        }
      });
      if (!allAnswered) {
        // Hiển thị thông báo lỗi trong popup
        document.getElementById('popup-title').textContent = 'Lỗi';
        document.getElementById('popup-content').textContent = 'Bạn phải hoàn thành tất cả câu hỏi!';
        document.getElementById('popup-overlay').style.display = 'block';
        document.getElementById('result-popup').style.display = 'block';
        return;
      }
      const formData = new FormData(this);
      fetch('inc/submit.php', {
        method: 'POST',
        body: formData,
        credentials: 'include'
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success' || data.status === 'guest') {
          // Hiển thị popup với kết quả
          document.getElementById('popup-title').textContent = 'Kết quả của bạn là: ' + data.personality_type;
          // Nếu là khách thì cảnh báo kết quả không được lưu
          if (data.status === 'guest') {
            document.getElementById('popup-content').innerHTML = `
              <p style="color: red;"><strong>Lưu ý:</strong> Bạn chưa đăng nhập nên kết quả không được lưu.</p>
              <a href="about.php" class="explore-link">Khám phá tính cách của bạn tại đây</a>`;
          } else {
            document.getElementById('popup-content').innerHTML = `
              <a href="about.php" class="explore-link">Khám Phá thêm về tính cách của bạn tại đây</a>`;
          }
          document.getElementById('popup-overlay').style.display = 'block';
          document.getElementById('result-popup').style.display = 'block';
        } else {
          // Hiển thị lỗi từ server
          document.getElementById('popup-title').textContent = 'Lỗi';
          document.getElementById('popup-content').textContent = data.message || 'Có lỗi xảy ra, vui lòng thử lại!';
          document.getElementById('popup-overlay').style.display = 'block';
          document.getElementById('result-popup').style.display = 'block';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        document.getElementById('popup-title').textContent = 'Lỗi';
        document.getElementById('popup-content').textContent = 'Có lỗi xảy ra khi gửi dữ liệu!';
        document.getElementById('popup-overlay').style.display = 'block';
        document.getElementById('result-popup').style.display = 'block';
      });
    });
    // Đóng popup
  function closePopup() {
    // Luôn đóng popup
    document.getElementById('popup-overlay').style.display = 'none';
    document.getElementById('result-popup').style.display = 'none';

    // Kiểm tra lại tất cả câu hỏi đã được trả lời chưa
    const answerInputs = document.querySelectorAll('input[name^="answer"]');
    let allAnswered = true;
    answerInputs.forEach(input => {
      if (input.value === '') {
        allAnswered = false;
      }
    });

    // Nếu đã trả lời hết thì mới reset form và cuộn lên đầu
    if (allAnswered) {
      // Reset form (xoá điểm đã chọn)
      document.getElementById('mbti-form').reset();

      // Xoá lớp 'selected' khỏi các nút đã bấm
      document.querySelectorAll('.circle.selected').forEach(btn => {
        btn.classList.remove('selected');
      });

      // Cuộn lên đầu trang
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }

  </script>
</body>
</html>