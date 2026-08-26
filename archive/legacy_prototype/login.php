<?php
$pageTitle = "منصة ردود - تسجيل الدخول";
$currentPage = "login";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

    <div class="login-card p-4 p-sm-5 rounded-4 text-center">
        
        <!-- زر العودة السريع للرئيسية -->
        <div class="text-start mb-3">
            <a href="index.php" class="btn-back text-decoration-none fs-7 d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-right"></i> الرئيسية
            </a>
        </div>

        <!-- الشعار / اسم المنصة -->
        <div class="mb-4">
            <a href="index.php" class="text-decoration-none">
                <h2 class="fw-bold text-gold m-0">ردود</h2>
            </a>
            <p class="text-white-50 fs-6 mt-2">مرحباً بعودتك! 👋</p>
        </div>

        <!-- مكان عرض التنبيهات والأخطاء من الباك إند -->
        <div id="alertBox" class="alert alert-danger d-none py-2 fs-7 mb-3" role="alert"></div>

        <!-- نموذج تسجيل الدخول -->
        <form action="dash.php" method="POST" id="loginForm">
            <!-- البريد الإلكتروني -->
            <div class="mb-3 text-start">
                <label for="email" class="form-label text-white-50 fs-7">البريد الإلكتروني</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-gold border-opacity-25 text-gold">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                </div>
            </div>

            <!-- كلمة المرور -->
            <div class="mb-3 text-start">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label text-white-50 fs-7 mb-0">كلمة المرور</label>
                    <a href="#" class="link-gold fs-7">نسيت كلمة المرور؟</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-gold border-opacity-25 text-gold">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <!-- زر الدخول -->
            <button type="submit" class="btn btn-gold w-100 rounded-3 mt-3 fs-6">
                تسجيل الدخول <i class="bi bi-box-arrow-in-left ms-1"></i>
            </button>
        </form>

        <!-- رابط إنشاء حساب جديد -->
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
            <p class="text-white-50 mb-0 fs-7">
                ليس لديك حساب؟ <a href="register.php" class="link-gold fw-bold ms-1">إنشاء حساب جديد</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

