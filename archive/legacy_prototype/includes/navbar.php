<?php
if (!isset($currentPage)) {
    $currentPage = '';
}
?>
<!-- شريط التنقل العلوي (Navbar) -->
<nav class="navbar navbar-expand-lg navbar-dark glass-navbar sticky-top py-2">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
      <img src="images/img.png" alt="شعار منصة ردود" class="nav-logo-img" style="height: 42px; width: auto; object-fit: contain;">
      <span class="fw-bold fs-3 text-gold"></span>
    </a>

    <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarRodood" aria-controls="navbarRodood" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarRodood">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold align-items-center">
        <li class="nav-item">
          <a class="nav-link px-3 <?php echo ($currentPage == 'home') ? 'active text-gold fw-bold' : 'text-white-50'; ?>" href="index.php">الرئيسية</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 <?php echo ($currentPage == 'features') ? 'active text-gold fw-bold' : 'text-white-50'; ?>" href="features.php">المميزات</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 <?php echo ($currentPage == 'pricing') ? 'active text-gold fw-bold' : 'text-white-50'; ?>" href="pricing.php">التسعيرة</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 <?php echo ($currentPage == 'blog') ? 'active text-gold fw-bold' : 'text-white-50'; ?>" href="blog.php">المدونة</a>
        </li>
        
        <!-- قائمة أقسام المنصة المنسدلة -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle px-3 <?php echo in_array($currentPage, ['auto', 'chat', 'ai', 'ai-manage', 'dash', 'live-chat']) ? 'active text-gold fw-bold' : 'text-white-50'; ?>" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            أقسام المنصة
          </a>
          <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border border-secondary border-opacity-25 rounded-3 mt-2" aria-labelledby="servicesDropdown">
            <li><a class="dropdown-item py-2 <?php echo ($currentPage == 'auto') ? 'active text-gold fw-bold' : ''; ?>" href="auto.php"><i class="bi bi-robot me-2 text-gold"></i>الرد الآلي</a></li>
            <li><a class="dropdown-item py-2 <?php echo ($currentPage == 'chat') ? 'active text-gold fw-bold' : ''; ?>" href="chat.php"><i class="bi bi-chat-dots me-2 text-gold"></i>إدارة المحادثات</a></li>
            <li><a class="dropdown-item py-2 <?php echo ($currentPage == 'ai') ? 'active text-gold fw-bold' : ''; ?>" href="ai.php"><i class="bi bi-cpu me-2 text-gold"></i>الذكاء الاصطناعي 24/7</a></li>
            <li><a class="dropdown-item py-2 <?php echo ($currentPage == 'ai-manage') ? 'active text-gold fw-bold' : ''; ?>" href="ai-manage.php"><i class="bi bi-sliders me-2 text-gold"></i>تدريب المساعد الذكي</a></li>
            <li><a class="dropdown-item py-2 <?php echo ($currentPage == 'dash') ? 'active text-gold fw-bold' : ''; ?>" href="dash.php"><i class="bi bi-speedometer2 me-2 text-gold"></i>لوحة التحكم</a></li>
            <li><a class="dropdown-item py-2 <?php echo ($currentPage == 'live-chat') ? 'active text-gold fw-bold' : ''; ?>" href="live-chat.php"><i class="bi bi-headset me-2 text-gold"></i>المحادثات المباشرة</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link px-3 <?php echo ($currentPage == 'try') ? 'active text-gold fw-bold' : 'text-white-50'; ?>" href="try.php">تواصل معنا</a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <a href="login.php" class="btn btn-outline-light rounded-pill px-4">تسجيل الدخول</a>
        <a href="register.php" class="btn btn-gold rounded-pill px-3 fw-bold d-flex align-items-center gap-1">
          <i class="bi bi-headset"></i> طلب استشارة
        </a>
      </div>
    </div>
  </div>
</nav>
