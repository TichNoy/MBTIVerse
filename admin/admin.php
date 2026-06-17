<?php
include_once '../inc/config.php';
start_secure_session();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../dn_dk.php?error=login_required");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MBTIVerse_Admin</title>
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="admin_style/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<script>
    $(document).ready(function () {
        // Xử lý sự kiện click cho các liên kết AJAX
        $("body").on("click", "a.ajax-link", function (e) {
            const message = $(this).data('confirm');
            if (message && !confirm(message)) return false;
            e.preventDefault(); // Ngăn chuyển trang
            var url = $(this).attr("href");
            // Nếu là xóa suggest, dùng AJAX GET riêng
            if (url.includes("delete_suggest.php")) {
                $.get(url, function (response) {
                    if (response.trim() === 'success') {
                        $("#right").load('suggest.php');  
                    } else {
                        alert("Lỗi: " + response);
                    }
                });
            } else {
                $("#right").load(url);
            }
        });
        // Xử lý sự kiện click cho các menu có submenu
        $(".menu-item.has-submenu > a").click(function(e) {
            if ($(this).parent().attr('id') !== 'trang-chu-admin') { 
                $(this).parent().toggleClass("active"); 
            }
        });
        // Xử lí nút "Về đầu trang"
        const btn = document.getElementById("back_to_top");
            window.addEventListener("scroll", () => {
                if (window.scrollY > 100) {
                    btn.classList.add("show");
                } else {
                    btn.classList.remove("show");
                }
            });
            btn.addEventListener("click", () => {
                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
            });
        // Xử lý dropdown
        const dropdownBtn = document.getElementById("dropdownBtn");
        const dropdownMenu = document.getElementById("dropdownMenu");
        dropdownBtn?.addEventListener("click", (e) => {
            e.stopPropagation(); // Ngăn sự kiện lan ra ngoài
            dropdownMenu.classList.toggle("show");
        });
        // Click ngoài menu thì ẩn
        window.addEventListener("click", function () {
            dropdownMenu.classList.remove("show");
        });
    });

</script>
<div id="container">
    <div id="header">
            <div class="header-left">
                <a href="admin.php" class="logo"> <i class="fas fa-user-tie"></i> Administrator </a>
            </div>
            <div class="header-center">
                <a href="../index.php" class="home">Vào trang web </a>
            </div>
            <div id="back_to_top">Về lại đầu trang </div>
            <div class="header-right">
                <div class="dropdown" style="position: relative;">
                    <button class="btn-secondary" id="dropdownBtn">
                        Xin chào <?= htmlspecialchars($_SESSION['username'] ?? 'admin') ?>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu" id="dropdownMenu">
                        <li><a href="../inc/logout.php">Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
    </div>
    <div id="wrapper">
        <div id="left">
            <ul>
                <li class="menu-item has-submenu">
                    <a href="questions.php" class="ajax-link"><i class="fas fa-list-alt"></i> Quản lí dữ liệu câu hỏi <i class="fas fa-caret-down submenu-arrow"></i></a>
                </li>
                <li class="menu-item has-submenu">
                    <a href="personality_types.php" class="ajax-link"><i class="fas fa-desktop"></i> Quản lí dữ liệu loại tính cách <i class="fas fa-caret-down submenu-arrow"></i></a>
                </li>
                <li class="menu-item has-submenu">
                    <a href="majors.php" class="ajax-link"><i class="fas fa-user-graduate"></i>Quản lí dữ liệu ngành đề xuất <i class="fas fa-caret-down submenu-arrow"></i></a>
                </li>
                <li class="menu-item has-submenu">
                    <a href="schools.php" class="ajax-link"><i class="fas fa-university"></i> Quản lí dữ liệu trường đề xuất <i class="fas fa-caret-down submenu-arrow"></i></a>
                </li>
                <li class="menu-item has-submenu">
                    <a href="users.php" class="ajax-link"><i class="fas fa-users"></i> Quản lí dữ liệu người dùng <i class="fas fa-caret-down submenu-arrow"></i></a>
                </li>
                <li class="menu-item has-submenu">
                    <a href="results.php"class="ajax-link"><i class="fas fa-poll-h"></i> Quản lí kết quả của người dùng<i class="fas fa-caret-down submenu-arrow"></i></a>
                </li>
                <li class="menu-item has-submenu">
                    <a href="suggest.php"class="ajax-link"><i class="fas fa-thumbs-up"></i>Quản lí đề xuất<i class="fas fa-caret-down submenu-arrow"></i></a>
                </li>
            </ul>
        </div>
        <div id="right"> 
            <h1 style="margin: 20px; ">Chào mừng bạn đến trang admin quản lí dữ liệu hệ thống.</h1>
            <p style="margin: 20px; "> Hãy chọn chức năng quản lí. <p>
        </div>
    </div> 
</div>
</body>
</html>