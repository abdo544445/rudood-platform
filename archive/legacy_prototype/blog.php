<?php
$pageTitle = "المدونة - منصة ردود";
$currentPage = "blog";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

    <!-- Header قسم العنوان الرئيسي -->
    <section class="py-5 text-center hero-bg border-bottom border-secondary border-opacity-25">
        <div class="container py-4">
            <span class="badge bg-gold-subtle text-gold border border-gold rounded-pill px-3 py-2 mb-3 fs-7">
                <i class="bi bi-journal-text ms-1"></i> مركز المعرفة والأخبار
            </span>
            <h1 class="fw-bold display-5 text-white mb-3">مدونة <span class="text-gold">ردود</span></h1>
            <p class="text-white-50 lead mx-auto" style="max-width: 600px;">
                استكشف أحدث المقالات والنصائح حول أتمتة خدمة العملاء، الذكاء الاصطناعي، وكيفية زيادة مبيعات متجرك بسهولة.
            </p>
        </div>
    </section>

    <!-- Main Content قسم المقالات -->
    <section class="py-5">
        <div class="container py-3">
            
            <!-- المقال المميز الرئيسي (Featured Post) -->
            <div class="card glass-card text-white mb-5 border-secondary border-opacity-50 overflow-hidden">
                <div class="row g-0 align-items-center">
                    <div class="col-lg-6">
                        <div class="blog-img-holder p-4 text-center">
                            <i class="bi bi-robot text-gold display-1"></i>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card-body p-4 p-lg-5">
                            <span class="badge bg-gold text-dark mb-2">مقال مميز</span>
                            <h3 class="card-title fw-bold mb-3 text-white">كيف يرفع الذكاء الاصطناعي التكيفي مبيعات متجرك بنسبة 40%؟</h3>
                            <p class="card-text text-white-50 mb-4">
                                التعرف على كيفية استخدام تقنيات الشات بوت الحديثة للرد اللحظي على استفسارات العملاء ومتابعة السلات المتروكة تلقائياً دون تدخل بشري.
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-white-50"><i class="bi bi-calendar3 ms-1"></i> 30 يوليو 2026</small>
                                <a href="articlel.php" class="btn btn-outline-gold rounded-pill px-4">اقرأ المقال <i class="bi bi-arrow-left me-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- شبكة المقالات (Articles Grid) -->
            <div class="row g-4">
                
                <!-- مقال 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card h-100 border-secondary border-opacity-25 text-white">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="mb-3 text-gold">
                                <i class="bi bi-whatsapp fs-1"></i>
                            </div>
                            <span class="text-gold fs-7 mb-2">أتمتة المحادثات</span>
                            <h5 class="card-title fw-bold mb-3">دليل ربط واتساب الأعمال بمنصة ردود خلال 5 دقائق</h5>
                            <p class="card-text text-white-50 fs-7 mb-4 flex-grow-1">
                                شرح خطوة بخطوة لربط رقمك التجاري والاستفادة من الرد الفوري الموحد لجميع عملائك.
                            </p>
                            <div class="d-flex align-items-center justify-content-between border-top border-secondary border-opacity-25 pt-3">
                                <small class="text-white-50 fs-7"><i class="bi bi-clock me-1"></i> قراءة 4 دقائق</small>
                                <a href="articlel.php" class="text-gold text-decoration-none fw-bold fs-7">اقرأ المزيد <i class="bi bi-chevron-left"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- مقال 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card h-100 border-secondary border-opacity-25 text-white">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="mb-3 text-gold">
                                <i class="bi bi-headset fs-1"></i>
                            </div>
                            <span class="text-gold fs-7 mb-2">خدمة العملاء</span>
                            <h5 class="card-title fw-bold mb-3">5 أخطاء قاتلة في خدمة العملاء تكلفك فقدان المبيعات</h5>
                            <p class="card-text text-white-50 fs-7 mb-4 flex-grow-1">
                                التأخر في الرد والإجابات الإنشائية قد تبعد عنك العميل. تعرف على كيفية حل هذه المشاكل بذكاء.
                            </p>
                            <div class="d-flex align-items-center justify-content-between border-top border-secondary border-opacity-25 pt-3">
                                <small class="text-white-50 fs-7"><i class="bi bi-clock me-1"></i> قراءة 6 دقائق</small>
                                <a href="articlel.php" class="text-gold text-decoration-none fw-bold fs-7">اقرأ المزيد <i class="bi bi-chevron-left"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- مقال 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card h-100 border-secondary border-opacity-25 text-white">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="mb-3 text-gold">
                                <i class="bi bi-graph-up-arrow fs-1"></i>
                            </div>
                            <span class="text-gold fs-7 mb-2">تحليلات وأرقام</span>
                            <h5 class="card-title fw-bold mb-3">كيف تفهم سلوك عملائك من خلال لوحة تحليلات ردود؟</h5>
                            <p class="card-text text-white-50 fs-7 mb-4 flex-grow-1">
                                قراءة البيانات وتحليل أكثر الأسئلة تكراراً تساعدك في تطوير منتجاتك وتسريع عمليات البيع.
                            </p>
                            <div class="d-flex align-items-center justify-content-between border-top border-secondary border-opacity-25 pt-3">
                                <small class="text-white-50 fs-7"><i class="bi bi-clock me-1"></i> قراءة 5 دقائق</small>
                                <a href="articlel.php" class="text-gold text-decoration-none fw-bold fs-7">اقرأ المزيد <i class="bi bi-chevron-left"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer الفوتر -->
    <footer class="py-4 border-top border-secondary border-opacity-25 text-center text-white-50 fs-7">
        <div class="container">
            <p class="mb-0">جميع الحقوق محفوظة © 2026 منصة ردود (Rudood)</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

