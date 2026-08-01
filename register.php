<?php
$pageTitle = "منصة ردود - إنشاء حساب جديد";
$currentPage = "register";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

    <div class="register-card p-4 p-sm-5 rounded-4 text-center">
        
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
            <p class="text-white-50 fs-6 mt-2">انضم إلينا وابدأ أتمتة أعمالك ✨</p>
        </div>

        <!-- مكان عرض التنبيهات والأخطاء من الباك إند -->
        <div id="alertBox" class="alert alert-danger d-none py-2 fs-7 mb-3" role="alert"></div>

        <!-- نموذج إنشاء حساب جديد -->
        <form action="api/register.php" method="POST" id="registerForm">
            <!-- الاسم الكامل -->
            <div class="mb-3 text-start">
                <label for="fullName" class="form-label text-white-50 fs-7">الاسم الكامل</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-gold border-opacity-25 text-gold">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" class="form-control" id="fullName" name="full_name" placeholder="محمد أحمد" required>
                </div>
            </div>

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

            <!-- رقم الهاتف / الواتساب -->
            <div class="mb-3 text-start">
                <label for="phone" class="form-label text-white-50 fs-7">رقم الهاتف (الواتساب)</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-gold border-opacity-25 text-gold">
                        <i class="bi bi-whatsapp"></i>
                    </span>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+968 9000 0000" required>
                </div>
            </div>

            <!-- كلمة المرور -->
            <div class="mb-3 text-start">
                <label for="password" class="form-label text-white-50 fs-7">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-gold border-opacity-25 text-gold">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <!-- زر إنشاء الحساب -->
            <button type="submit" class="btn btn-gold w-100 rounded-3 mt-3 fs-6">
                إنشاء حساب جديد <i class="bi bi-person-plus ms-1"></i>
            </button>
        </form>

        <!-- رابط العودة لتسجيل الدخول -->
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
            <p class="text-white-50 mb-0 fs-7">
                لديك حساب بالفعل؟ <a href="login.php" class="link-gold fw-bold ms-1">تسجيل الدخول</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

