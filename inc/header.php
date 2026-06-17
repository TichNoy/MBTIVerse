<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once(__DIR__ . '/config.php');
?>
<header class="navbar">
  <a href="<?= $base_url ?>/index.php" class="logo">MBTIverse</a>
  <nav>
    <ul class="nav-links">
      <li><a href="<?= $base_url ?>/about.php">Khám phá</a></li> 
      <li><a href="<?= $base_url ?>/test.php">Kiểm tra</a></li>
      <li><a href="<?= $base_url ?>/guide.php">Hướng dẫn</a></li>
    </ul>
  </nav>
  <div class="user-dropdown">
    <?php if (isset($_SESSION['username'])): ?>
      <button class="btn-secondary" id="dropdownBtn"> Xin chào 
        <?= htmlspecialchars($_SESSION['username']) ?> 
      </button>
      <ul class="dropdown-menu" id="dropdownMenu">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <li><a href="<?= $base_url ?>/admin/admin.php">Trang admin</a></li>
        <?php else: ?>
          <li><a href="<?= $base_url ?>/ca_nhan.php">Trang cá nhân</a></li>
        <?php endif; ?>
        <li><a href="<?= $base_url ?>/inc/logout.php">Đăng xuất</a></li>
      </ul>
    <?php else: ?>
      <div class="btn-secondary">
        <a href="<?= $base_url ?>/dn_dk.php">Đăng nhập / Đăng ký</a>
      </div>
    <?php endif; ?>
</div>
</header>
<script>
  const dropdownBtn = document.getElementById("dropdownBtn");
  const dropdownMenu = document.getElementById("dropdownMenu");
  dropdownBtn?.addEventListener("click", () => {
    dropdownMenu.classList.toggle("show");
  });
  // Ẩn dropdown khi click bên ngoài
  window.addEventListener("click", function(e) {
    if (!dropdownBtn.contains(e.target)) {
      dropdownMenu.classList.remove("show");
    }
  });
</script>
<style>
  .dropdown-menu.show {
    display: block;
  }
</style>
