<?php
$pageTitle = "منصة ردود - تسجيل الدخول";
$currentPage = "login copy";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

  <div class="auth-bg">
    <div class="auth-overlay"></div>
    
    <div class="rodood-glass-card auth-card">
      <a href="index.php" class="d-inline-block mb-4">
        <img src="images/photo_2026-07-28_19-16-05.jpg" alt="شعار ردود" class="nav-logo" style="height: 60px;">
      </a>
      
      <h2 class="fw-bold text-dark mb-2">مرحباً بعودتك! 👋</h2>
      <p class="text-muted mb-4">سجل دخولك للمتابعة إلى لوحة التحكم</p>
      
      <form action="index.php" method="get">
        <div class="mb-3 text-start">
          <label class="form-label fw-bold text-dark small">البريد الإلكتروني</label>
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0 text-muted rounded-start-4"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control form-control-custom border-start-0 rounded-end-4" placeholder="example@domain.com" required>
          </div>
        </div>
        
        <div class="mb-4 text-start">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label fw-bold text-dark small mb-0">كلمة المرور</label>
            <a href="#" class="text-decoration-none small text-navy">نسيت كلمة المرور؟</a>
          </div>
          <div class="input-group mt-2">
            <span class="input-group-text bg-white border-end-0 text-muted rounded-start-4"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control form-control-custom border-start-0 rounded-end-4" placeholder="••••••••" required>
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold fs-5 mb-3">
          تسجيل الدخول <i class="bi bi-box-arrow-in-left ms-1"></i>
        </button>
      </form>
      
      <p class="text-muted small mb-0">
        ليس لديك حساب؟ <a href="register.php" class="text-navy fw-bold text-decoration-none">إنشاء حساب جديد</a>
      </p>
    </div>
  </div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>

