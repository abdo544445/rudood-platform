<?php
$pageTitle = "منصة ردود - تواصل معنا";
$currentPage = "try";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

    <!-- محتوى الصفحة -->
    <main class="container py-5">
        <div class="glow-effect top-50 start-50 translate-middle"></div>

        <!-- عنوان الصفحة -->
        <div class="text-center mb-5">
            <h1 class="fw-bold text-gold display-5">تواصل مع فريق ردود</h1>
            <p class="text-white-50 fs-5 mt-2">نحن هنا لمساعدتك في أتمتة خدمات عملائك والانتقال بأعمالك للمستوى التالي.</p>
        </div>

        <div class="row g-4 align-items-stretch">
            
            <!-- العمود الأيمن: معلومات التواصل -->
            <div class="col-lg-5">
                <div class="glass-card p-4 p-md-5 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h3 class="fw-bold text-white mb-4">معلومات الاتصال</h3>
                        <p class="text-white-50 mb-4">يسعدنا استقبال استفساراتك واقتراحاتك في أي وقت، فريق الدعم متواجد لخدمتك.</p>
                        
                        <!-- عنصر 1: البريد -->
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="contact-icon-box">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div>
                                <span class="d-block text-white-50 fs-7">البريد الإلكتروني</span>
                                <strong class="text-white">support@rodood.ai</strong>
                            </div>
                        </div>

                        <!-- عنصر 2: الواتساب -->
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="contact-icon-box">
                                <i class="bi bi-whatsapp"></i>
                            </div>
                            <div>
                                <span class="d-block text-white-50 fs-7">الدعم الفني عبر الواتساب</span>
                                <strong class="text-white" dir="ltr">+968 9000 0000</strong>
                            </div>
                        </div>

                        <!-- عنصر 3: أوقات العمل -->
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="contact-icon-box">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <span class="d-block text-white-50 fs-7">ساعات العمل</span>
                                <strong class="text-white">متاح 24/7 عبر الذكاء الاصطناعي</strong>
                            </div>
                        </div>
                    </div>

                    <!-- وسائل التواصل الاجتماعي -->
                    <div class="pt-4 border-top border-secondary border-opacity-25">
                        <span class="d-block text-white-50 mb-3 fs-7">تابعنا على شبكات التواصل</span>
                        <div class="d-flex gap-3 fs-5">
                            <a href="#" class="text-white-50 link-gold"><i class="bi bi-twitter-x"></i></a>
                            <a href="#" class="text-white-50 link-gold"><i class="bi bi-linkedin"></i></a>
                            <a href="#" class="text-white-50 link-gold"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- العمود الأيسر: نموذج المراسلة -->
            <div class="col-lg-7">
                <div class="glass-card p-4 p-md-5">
                    <h3 class="fw-bold text-white mb-4">أرسل لنا رسالة</h3>
                    
                    <!-- مكان عرض إشعار النجاح أو الخطأ -->
                    <div id="alertBox" class="alert alert-success d-none py-2 fs-7 mb-3" role="alert"></div>

                    <form action="api/contact.php" method="POST" id="contactForm">
                        <div class="row g-3">
                            <div class="col-md-6 text-start">
                                <label for="senderName" class="form-label text-white-50 fs-7">الاسم الكامل</label>
                                <input type="text" class="form-control" id="senderName" name="sender_name" placeholder="محمد أحمد" required>
                            </div>

                            <div class="col-md-6 text-start">
                                <label for="senderEmail" class="form-label text-white-50 fs-7">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="senderEmail" name="sender_email" placeholder="name@example.com" required>
                            </div>

                            <div class="col-12 text-start">
                                <label for="subject" class="form-label text-white-50 fs-7">عنوان الرسالة</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="استفسار عن خطط الأسعار" required>
                            </div>

                            <div class="col-12 text-start">
                                <label for="message" class="form-label text-white-50 fs-7">نص الرسالة</label>
                                <textarea class="form-control" id="message" name="message" rows="5" placeholder="اكتب استفسارك هنا..." required></textarea>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-gold px-5 rounded-3 w-100 w-md-auto">
                                    إرسال الرسالة <i class="bi bi-send ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

