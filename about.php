<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Free MBTIVerse</title>
  <link rel="stylesheet" href="style/style.css">  
  <link rel="stylesheet" href="style/about.css">
  <link rel="stylesheet" href="style/case8_style.css" />
  <link rel="stylesheet" href="style/case16_style.css" />
  <link rel="stylesheet" href="style/header.css">
</head>
<body >
<?php 
include 'inc/header.php';
include 'inc/config.php';
// 1. Lấy danh sách ngành, trường và link
// -----------------------------------------
$sqlMajors = "
    SELECT 
      p.personality_type_id,
      m.major_id,
      m.major_name,
      s.school_name,
      s.URL AS school_link
    FROM personality_types p
    JOIN personality_major pm
      ON p.personality_type_id = pm.personality_type_id
    JOIN majors m
      ON pm.major_id = m.major_id
    JOIN major_school ms
      ON m.major_id = ms.major_id
    JOIN schools s
      ON ms.school_id = s.school_id
    ORDER BY p.personality_type_id, m.major_id
";
$resultMajors = mysqli_query($conn, $sqlMajors);
$majorsByMbti = [];
// Lưu từng major và danh sách schools (mỗi school có name và link)
while ($row = mysqli_fetch_assoc($resultMajors)) {
    $type = $row['personality_type_id'];
    $majorId = $row['major_id'];
    if (!isset($majorsByMbti[$type][$majorId])) {
        $majorsByMbti[$type][$majorId] = [
            'major_name' => $row['major_name'],
            'schools' => []
        ];
    }
    $majorsByMbti[$type][$majorId]['schools'][] = [
        'school_name' => $row['school_name'],
        'school_link' => $row['school_link'],
    ];
}
// 2. Lấy thông tin MBTI
$sqlMbti = "SELECT * FROM personality_types";
$resultMbti = mysqli_query($conn, $sqlMbti);
$mbtiData = [];
while ($row = mysqli_fetch_assoc($resultMbti)) {
    $code = $row['personality_type_id'];
    $mbtiData[$code] = [
        'title' => $row['type_name'] . " – " . $code,
        'desc' => $row['description'],
        'strengths' => $row['strengths'],
        'weaknesses' => $row['weaknesses'],
        'majors' => $majorsByMbti[$code] ?? []
    ];
}?>
<!-- hiển thị đầu tiên -->
<section class="initial-cards-wrapper">
  <h1 class="section-heading">Hãy cùng chúng mình khám phá thế giới tính cách kì diệu!</h1>
  <div class="initial-cards-container">
    <div class="card_8">
      <div class="icon">
        <img src="assets/The/5a6841ac1dee052677ee9e7cf0e174e1-removebg-preview.png" alt="type8" />
      </div>
      <h2>Các phân loại đặc trưng tính cách MBTI</h2>
    </div>
    <div class="card_16">
      <div class="icon">
        <img src="assets/The/report-delve-deeper.png" alt="type16" />
    </div>
      <h2>Thông tin chi tiết các loại tính cách </h2>
    </div>
  </div>
</section>
<!-- Hiển thị khi bấm card_8 -->
<div class="card-grid">
    <!-- Card 1 -->
    <div class="flip-card">
      <div class="flip-card-inner">
        <div class="flip-card-front">
          <img src="assets/The/I.jpg" alt="I" />
          <h3>I - INTROVERT</h3>
        </div>
        <div class="flip-card-back">
          Hướng năng lượng vào bên trong, thích suy nghĩ, yên tĩnh
        </div>
      </div>
    </div>
    <!-- Card 2 -->
    <div class="flip-card">
      <div class="flip-card-inner">
        <div class="flip-card-front">
          <img src="assets/The/E.jpg" alt="E" />
          <h3>E – EXTRAVERSION</h3>
        </div>
        <div class="flip-card-back">
          Hướng năng lượng ra thế giới bên ngoài, thích giao tiếp, hành động
        </div>
      </div>
    </div>
    <!-- Card 3 -->
    <div class="flip-card">
      <div class="flip-card-inner">
        <div class="flip-card-front">
          <img src="assets/The/S.jpg" alt="S" />
          <h3>S – SENSING</h3>
        </div>
        <div class="flip-card-back">
          Chú ý chi tiết thực tế, thích dữ liệu cụ thể, thực nghiệm
        </div>
      </div>
    </div>
    <!-- Card 4 -->
    <div class="flip-card">
      <div class="flip-card-inner">
        <div class="flip-card-front">
          <img src="assets/The/N.jpg" alt="N" />
          <h3>N – INTUITION</h3>
        </div>
        <div class="flip-card-back">
          Tập trung vào ý nghĩa, tương lai, mô hình, khái niệm
        </div>
      </div>
    </div>
    <!-- Card 5 -->
    <div class="flip-card">
      <div class="flip-card-inner">
        <div class="flip-card-front">
          <img src="assets/The/T.jpg" alt="T" />
          <h3>T – THINKING</h3>
        </div>
        <div class="flip-card-back">
          Quyết định dựa trên logic, nguyên tắc và khách quan
        </div>
      </div>
    </div>
    <!-- Card 6 -->
    <div class="flip-card">
      <div class="flip-card-inner">
        <div class="flip-card-front">
          <img src="assets/The/F.jpg" alt="F" />
          <h3>F – FEELING</h3>
        </div>
        <div class="flip-card-back">
          Quyết định dựa trên giá trị cá nhân, sự đồng cảm, cảm xúc
        </div>
      </div>
    </div>
    <!-- Card 7 -->
    <div class="flip-card">
      <div class="flip-card-inner">
        <div class="flip-card-front">
          <img src="assets/The/J.jpg" alt="J" />
          <h3>J – JUDGING</h3>
        </div>
        <div class="flip-card-back">
          Có kế hoạch, thích sự rõ ràng, cấu trúc, tổ chức
        </div>
      </div>
    </div>
    <!-- Card 8 -->
    <div class="flip-card">
      <div class="flip-card-inner">
        <div class="flip-card-front">
          <img src="assets/The/P.jpg" alt="P" />
          <h3>P – PERCEIVING</h3>
        </div>
        <div class="flip-card-back">
          Tự do, linh hoạt, thích thích nghi, khám phá
        </div>
      </div>
    </div>
  </div>
<!-- Hiển thị khi bấm card_16 -->
<div>
    <!-- Nhóm 1: Nhà phân tích -->
    <section class="group analyst">
      <h2>Nhà phân tích</h2>
      <div class="cards">
        <div class="card" data-type="INTJ">
          <img src="assets/16hinh/INTJ.jpg" alt="INTJ" />
          <div class="title">Architect</div>
          <div class="description">INTJ</div>
        </div>
        <div class="card" data-type="INTP">
          <img src="assets/16hinh/INTP.jpg" alt="INTP" />
          <div class="title">Logician</div>
          <div class="description">INTP</div>
        </div>
        <div class="card" data-type="ENTJ">
          <img src="assets/16hinh/ENTJ.jpg" alt="ENTJ" />
          <div class="title">Commander</div>
          <div class="description">ENTJ</div>
        </div>
        <div class="card" data-type="ENTP">
          <img src="assets/16hinh/ENTP.jpg" alt="ENTP" />
          <div class="title">Debater</div>
          <div class="description">ENTP</div>
        </div>
      </div>
    </section>
    <!-- Nhóm 2: Nhà ngoại giao -->
    <section class="group diplomat">
      <h2>Nhà ngoại giao</h2>
      <div class="cards">
        <div class="card" data-type="INFJ">
          <img src="assets/16hinh/INFJ.jpg" alt="INFJ" />
          <div class="title">Advocate</div>
          <div class="description">INFJ</div>
        </div>
        <div class="card" data-type="INFP">
          <img src="assets/16hinh/INFP.jpg" alt="INFP" />
          <div class="title">Mediator</div>
          <div class="description">INFP</div>
        </div>
        <div class="card" data-type="ENFJ">
          <img src="assets/16hinh/ENFJ.jpg" alt="ENFJ" />
          <div class="title">Protagonist</div>
          <div class="description">ENFJ</div>
        </div>
        <div class="card" data-type="ENFP">
          <img src="assets/16hinh/ENFP.jpg" alt="ENFP" />
          <div class="title">Campaigner</div>
          <div class="description">ENFP</div>
        </div>
      </div>
    </section>
    <!-- Nhóm 3: Người canh gác -->
    <section class="group sentinel">
      <h2>Người canh gác</h2>
      <div class="cards">
        <div class="card" data-type="ISTJ">
          <img src="assets/16hinh/ISTJ.jpg" alt="ISTJ" />
          <div class="title">Logistician</div>
          <div class="description">ISTJ</div>
        </div>
        <div class="card" data-type="ISFJ">
          <img src="assets/16hinh/ISFJ.jpg" alt="ISFJ" />
          <div class="title">Defender</div>
          <div class="description">ISFJ</div>
        </div>
        <div class="card" data-type="ESTJ">
          <img src="assets/16hinh/ESTJ.jpg" alt="ESTJ" />
          <div class="title">Executive</div>
          <div class="description">ESTJ</div>
        </div>
        <div class="card" data-type="ESFJ">
          <img src="assets/16hinh/ESFJ.jpg" alt="ESFJ" />
          <div class="title">Consul</div>
          <div class="description">ESFJ</div>
        </div>
      </div>
    </section>
    <!-- Nhóm 4: Nhà thám hiểm -->
    <section class="group explorer">
  <h2>Nhà thám hiểm</h2>
  <div class="cards">
    <div class="card" data-type="ISTP">
      <img src="assets/16hinh/ISTP.jpg" alt="ISTP" />
      <div class="title">Virtuoso</div>
      <div class="description">ISTP</div>
    </div>
    <div class="card" data-type="ISFP">
      <img src="assets/16hinh/ISFP.jpg" alt="ISFP" />
      <div class="title">Adventurer</div>
      <div class="description">ISFP</div>
    </div>
    <div class="card" data-type="ESTP">
      <img src="assets/16hinh/ESTP.jpg" alt="ESTP" />
      <div class="title">Entrepreneur</div>
      <div class="description">ESTP</div>
    </div>
    <div class="card" data-type="ESFP">
      <img src="assets/16hinh/ESFP.jpg" alt="ESFP" />
      <div class="title">Entertainer</div>
      <div class="description">ESFP</div>
    </div>
  </div>
</section>
    <div id="mbtiOverlay" class="overlay">
      <div class="overlay-content">
        <span class="close" onclick="closeOverlay()">&times;</span>
        <h2 id="overlayTitle"></h2>
        <p id="overlayDesc"></p>
      </div>
    </div>
</div>
<!-- sửa script để khi ấn vào thẻ sẽ hiện nội dung từ DB, và các dấu "gạch nối" không mất -->
    <script>
 const descriptions = <?php echo json_encode($mbtiData, JSON_UNESCAPED_UNICODE); ?>;
  document.querySelectorAll(".card").forEach((card) => {
    card.addEventListener("click", () => {
      const code = card.getAttribute("data-type");
      const data = descriptions[code];
      if (data) {
        document.getElementById("overlayTitle").innerText = data.title;
        function renderList(text) {
          return text
            .split(/\n|•|^-\s*/gm)  // chia theo xuống dòng, dấu "•", hoặc gạch đầu dòng ở đầu dòng
            .map(item => item.trim())
            .filter(item => item.length > 0)
            .map(item => `<li>${item}</li>`)
            .join('');
        }
          // Trong phần renderList của bạn
        let majorsList = '';
        if (data.majors && Object.keys(data.majors).length > 0) {
          majorsList = '<h4>🎓 Gợi ý ngành và trường phù hợp:</h4>';
          // Duyệt từng major
          Object.values(data.majors).forEach(m => {
            majorsList += `<h5>${m.major_name}</h5><ul>`;
            // Duyệt từng school
            m.schools.forEach(school => {
              majorsList += `
                <li>
                  <a href="${school.school_link}" target="_blank" rel="noopener noreferrer">
                    ${school.school_name}
                  </a>
                </li>
              `;
            });
            majorsList += '</ul>';
          });
        }
        document.getElementById("overlayDesc").innerHTML = `
          <div class="desc-block">
            <p><strong>📝 Mô tả:</strong></p>
            <p>${data.desc.replace(/\n/g, "<br>")}</p>
            
            <p><strong>🌟 Điểm mạnh:</strong></p>
            <ul>${renderList(data.strengths)}</ul>
            
            <p><strong>⚠️ Điểm yếu:</strong></p>
            <ul>${renderList(data.weaknesses)}</ul>

            ${majorsList}
          </div>
        `;
        document.getElementById("mbtiOverlay").style.display = "block";
      }
    });
  });
  function closeOverlay() {
    document.getElementById("mbtiOverlay").style.display = "none";
  }
  window.onclick = function (event) {
    const overlay = document.getElementById("mbtiOverlay");
    if (event.target === overlay) {
      overlay.style.display = "none";
    }
  };
    </script>
<!-- xử lí ẩn hiện các tab -->
<script>
  document.querySelector(".card_8").addEventListener("click", function () {
    document.querySelector(".card-grid").style.display = "grid";
    document.querySelectorAll("section.group").forEach((s) => (s.style.display = "none"));
  });
  document.querySelector(".card_16").addEventListener("click", function () {
    document.querySelector(".card-grid").style.display = "none";
    document.querySelectorAll("section.group").forEach((s) => (s.style.display = "block"));
  });
  // Mặc định ẩn các phần:
  document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".card-grid").style.display = "none";
    document.querySelectorAll("section.group").forEach((s) => (s.style.display = "none"));
  });
</script>
</body>
</html>
