<?php include_once 'inc/auto_login.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Free MBTIVerse</title>
  <link rel="stylesheet" href="style/dn_dk.css" />
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" href="style/header.css" />
  <script src="assets/js/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include 'inc/header.php'; ?>
<div class="main">
  <div class="container">
    <div class="toggle-buttons">
      <button id="loginBtn" class="active">Đăng nhập</button>
      <button id="registerBtn">Đăng ký</button>
    </div>
    <!-- Đăng nhập -->
    <form id="loginForm" class="form active" method="POST" action="inc/login.php">
      <label>Tên người dùng</label>
      <input type="text" name="username" placeholder="Nhập tên người dùng"
      value="<?php echo isset($_COOKIE['remember_username']) ? htmlspecialchars($_COOKIE['remember_username']) : ''; ?>" required />
      <label>Mật khẩu</label>
      <input type="password" name="password" class="password-input" placeholder="Nhập mật khẩu" required />
      <div class="form-actions">
        <label><input type="checkbox" name="remember" /> Ghi nhớ tôi</label>
        <a href="#">Quên mật khẩu?</a>
      </div>
      <div class="submit-buttons-wrapper">
        <button class="submit-btn" type="submit">Đăng nhập</button>
      </div>
    </form>
    <!-- Đăng ký -->
    <form id="registerForm" class="form hidden" method="POST" action="inc/register.php">
      <label>Tên người dùng</label>
      <input type="text" name="username" placeholder="Nhập tên người dùng" required />
      <label>Email</label>
      <input type="email" name="email" placeholder="Nhập email" required />
      <label>Mật khẩu</label>
      <input type="password" name="password" class="password-input" placeholder="Nhập mật khẩu" required />
      <label>Xác nhận mật khẩu</label>
      <input type="password" name="confirm_password" class="password-input" placeholder="Xác nhận mật khẩu" required />
      <div class="submit-buttons-wrapper">
        <button class="submit-btn" type="submit">Đăng ký</button>
      </div>
    </form>
    <!-- Quên mật khẩu -->
    <form id="forgotForm" class="form hidden" method="POST" action="inc/reset_password.php">
        <h2 class="forgot-title">Quên mật khẩu</h2>
        <label>Email</label>
        <input type="email" name="email" placeholder="Nhập email đã đăng ký" required />
        <label>Mật khẩu mới</label> 
        <input type="password" name="new_password" class="password-input" placeholder="Nhập mật khẩu mới" required />
        <label>Xác nhận mật khẩu</label>
        <input type="password" name="confirm_password" class="password-input" placeholder="Xác nhận mật khẩu mới" required />
        <div class="submit-buttons-wrapper">
          <button class="submit-btn" type="submit">Đặt lại mật khẩu</button>
        </div>
        <div class="back-to-login-wrapper">
          <a href="#" id="backToLogin" class="back-to-login">Quay lại đăng nhập</a>
        </div>
    </form>
  </div>
</div>
<script>
$(document).ready(function () {
  const loginBtn = $("#loginBtn");
  const registerBtn = $("#registerBtn");
  const loginForm = $("#loginForm");
  const registerForm = $("#registerForm");
  const forgotForm = $("#forgotForm");
  // Đổi tab
  loginBtn.click(() => {
    $(".toggle-buttons button").removeClass("active");
    loginBtn.addClass("active");
    $(".form").removeClass("active").addClass("hidden");
    loginForm.removeClass("hidden").addClass("active");
  });
  registerBtn.click(() => {
    $(".toggle-buttons button").removeClass("active");
    registerBtn.addClass("active");
    $(".form").removeClass("active").addClass("hidden");
    registerForm.removeClass("hidden").addClass("active");
  });
  // Hiển thị form quên mật khẩu
  $(".form-actions a").click(function (e) {
    e.preventDefault();
    $(".toggle-buttons").hide();
    $(".form").removeClass("active").addClass("hidden");
    forgotForm.removeClass("hidden").addClass("active");
  });
  // Quay lại form đăng nhập
  $("#backToLogin").click(function (e) {
    e.preventDefault();
    $(".toggle-buttons").show();
    $(".form").removeClass("active").addClass("hidden");
    loginForm.removeClass("hidden").addClass("active");
  });
  // Submit đổi mật khẩu
  $("#forgotForm").submit(function (e) {
    e.preventDefault();
    $.post("inc/reset_password.php", $(this).serialize(), function (res) {
      if (res.trim() === "success") {
        Swal.fire("Thành công", "Đổi mật khẩu thành công! Vui lòng đăng nhập lại.", "success");
        $(".toggle-buttons").show(); 
        loginBtn.click();
      } else {
        Swal.fire("Lỗi", res, "error");
      }
    }).fail(() => Swal.fire("Lỗi", "Có lỗi xảy ra. Vui lòng thử lại.", "error"));
  });
  // Đăng ký Ajax
  $("#registerForm").submit(function (e) {
    e.preventDefault();
    $.post("inc/register.php", $(this).serialize(), function (res) {
      if (res.trim() === "success") {
        loginBtn.click();
        alert("Đăng ký thành công! Vui lòng đăng nhập.");
      } else {
        alert(res);
      }
    }).fail(() => alert("Có lỗi xảy ra."));
  });
  // Icon ẩn/hiện mật khẩu
  $(".password-input").each(function () {
    const $input = $(this);
    if ($input.parent().hasClass("password-wrapper")) return;
    const $wrapper = $("<div>").addClass("password-wrapper");
    const $icon = $("<span>")
      .addClass("password-toggle-icon")
      .html("🙈")
      .on("click", function () {
        const isPassword = $input.attr("type") === "password";
        $input.attr("type", isPassword ? "text" : "password");
        $(this).html(isPassword ? "👁" : "🙈");
      });
    $input.wrap($wrapper);
    $input.after($icon);
    $icon.hide();
    $input.on("input", function () {
      $icon.toggle($(this).val().length > 0);
    });
  });
  // Kiểm tra URL
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('error') === 'login_required') {
    alert("Vui lòng đăng nhập để truy cập.");
  } else if (urlParams.get('registered') === '1') {
    loginBtn.click();
    alert("Đăng ký thành công! Vui lòng đăng nhập.");
  } else if (urlParams.get('error') === 'wrong_password') {
    Swal.fire("Đăng nhập thất bại", "Mật khẩu không đúng!", "error");
  } else if (urlParams.get('error') === 'account_not_found') {
    Swal.fire("Đăng nhập thất bại", "Không tìm thấy tài khoản!", "error");
  } else if (urlParams.get('error') === 'missing_input') {
    Swal.fire("Lỗi", "Vui lòng nhập đầy đủ tên người dùng và mật khẩu!", "warning");
  }
});
</script>
</body>
</html>
