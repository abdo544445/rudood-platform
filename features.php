<?php
$pageTitle = "منصة ردود - المميزات";
$currentPage = "features";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

    <!-- قسم العنوان الرئيسي للصفحة -->
    <header class="container text-center mt-5 pt-5 mb-5">
        <h1 class="display-4 fw-bold text-gold mb-3">مميزات منصة ردود</h1>
        <p class="lead text-white-50">حلول متكاملة وأدوات ذكية لتطوير خدمة عملاء عملك والارتقاء بها</p>
    </header>

    <!-- قسم شبكة المميزات -->
    <main class="container mb-5 pb-5">
        <div class="row g-4">
            
            <!-- كارت 1 -->
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 rounded-4 text-center text-white">
                    <div class="icon-box mb-3 text-gold display-5">
                        <i class="bi bi-robot"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-gold">ردود آلية ذكية 24/7</h4>
                    <p class="text-white-50 fs-6 lh-lg">
                        تقديم إجابات فورية ودقيقة لاستفسارات العملاء على مدار الساعة دون الحاجة للانتظار أو التدخل البشري.
                    </p>
                </div>
            </div>

            <!-- كارت 2 -->
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 rounded-4 text-center text-white">
                    <div class="icon-box mb-3 text-gold display-5">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-gold">إدارة المحادثات الموحدة</h4>
                    <p class="text-white-50 fs-6 lh-lg">
                        تجميع واستقبال محادثات العملاء من مختلف القنوات والمنصات في لوحة تحكم واحدة وسلسة الاستخدام.
                    </p>
                </div>
            </div>

            <!-- كارت 3 -->
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 rounded-4 text-center text-white">
                    <div class="icon-box mb-3 text-gold display-5">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-gold">تحليلات وتقارير أداء</h4>
                    <p class="text-white-50 fs-6 lh-lg">
                        متابعة إحصائيات الدعم الفني، معدل سرعة الاستجابة، ورضا العملاء عبر تقارير تفاعلية شاملة.
                    </p>
                </div>
            </div>

            <!-- كارت 4 -->
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 rounded-4 text-center text-white">
                    <div class="icon-box mb-3 text-gold display-5">
                        <i class="bi bi-cpu"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-gold">تدريب المساعد الذكي</h4>
                    <p class="text-white-50 fs-6 lh-lg">
                        إمكانية رفع ملفاتك وتدريب الذكاء الاصطناعي على بيانات شركتك أو خدماتك للإجابة وفق سياق عملك تماماً.
                    </p>
                </div>
            </div>

            <!-- كارت 5 -->
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 rounded-4 text-center text-white">
                    <div class="icon-box mb-3 text-gold display-5">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-gold">أمان وحماية البيانات</h4>
                    <p class="text-white-50 fs-6 lh-lg">
                        تشفير كامل للبيانات والمحادثات للحفاظ على خصوصية عملاء المنصة والشركات بشكل صارم.
                    </p>
                </div>
            </div>

            <!-- كارت 6 -->
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 rounded-4 text-center text-white">
                    <div class="icon-box mb-3 text-gold display-5">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-gold">تحويل سلس للدعم البشري</h4>
                    <p class="text-white-50 fs-6 lh-lg">
                        تحويل التذاكر أو المحادثات المعقدة بسلاسة إلى موظفي الدعم البشري عند الحاجة مع توفير التاريخ الكامل.
                    </p>
                </div>
            </div>

        </div>
    </main>

    <!-- قسم الدعوة للإجراء الختامي (CTA) -->
    <section class="container text-center my-5 pb-5">
        <div class="glass-card p-5 rounded-4 mx-auto" style="max-width: 800px;">
            <h2 class="fw-bold text-white mb-3">جاهز لتطوير خدمة عملائك؟</h2>
            <p class="text-white-50 mb-4 fs-5">ابدأ الآن واجعل منصة ردود تجعل دعم العملاء لديك أكثر سهولة وسرعة.</p>
            <a href="login.php" class="btn btn-gold px-5 py-3 fs-5 fw-bold rounded-pill">جرب المنصة الآن</a>
        </div>
    </section>

    <!-- تذييل الصفحة (Footer) بسيط -->
    <footer class="text-center py-4 mt-auto" style="border-top: 1px solid rgba(255,255,255,0.05);">
        <p class="text-white-50 mb-0">جميع الحقوق محفوظة &copy; 2026 منصة ردود</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

