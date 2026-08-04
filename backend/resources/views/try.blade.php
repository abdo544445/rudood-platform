<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة ردود - تواصل معنا</title>
    
    <!-- Bootstrap 5 RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts (Cairo) -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- ملف CSS الخاص بك -->
    <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #060913;
            background-image: 
                radial-gradient(circle at center, rgba(11, 20, 38, 0.4) 0%, rgba(6, 9, 19, 0.85) 100%),
                url('images/log22.png'); /* اسم صورة الخلفية لديكِ */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #ffffff;
            min-height: 100vh;
        }

        /* الهالة الذهبية الناعمة */
        .glow-effect {
            position: absolute;
            width: 350px;
            height: 350px;
            background: rgba(212, 175, 55, 0.1);
            filter: blur(140px);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.04) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(212, 175, 55, 0.2) !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border-radius: 16px;
            position: relative;
            z-index: 1;
        }

        .text-gold {
            color: #D4AF37 !important;
        }

        .form-control {
            background-color: rgba(11, 15, 25, 0.6) !important;
            border: 1px solid rgba(212, 175, 55, 0.25) !important;
            color: #fff !important;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: #D4AF37 !important;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.3) !important;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .btn-gold {
            background-color: #D4AF37 !important;
            color: #0b0f19 !important;
            border: none;
            font-weight: bold;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background-color: #f1c40f !important;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
            transform: translateY(-2px);
        }

        .contact-icon-box {
            width: 50px;
            height: 50px;
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #D4AF37;
        }
    </style>
</head>
<body>

   <nav class="navbar navbar-expand-lg navbar-rodood sticky-top">
  <div class="container">
    
    <!-- 1. الشعار (جهة اليمين) -->
    <a class="navbar-brand d-flex align-items-center me-3" href="{{ url('/index') }}">
      <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" class="nav-logo-img">
    </a>
    
    
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-3">
                    <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/index') }}">الرئيسية</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/features') }}">المميزات</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/ai') }}">الذكاء الاصطناعي</a></li>
                    <li class="nav-item"><a class="nav-link active text-gold fw-bold" href="{{ url('/try') }}">تواصل معنا</a></li>
                </ul>
                <div class="d-flex gap-2 ms-lg-4 mt-3 mt-lg-0">
                    <a href="{{ url('/login') }}" class="btn btn-outline-light px-4 rounded-3">تسجيل الدخول</a>
                    <a href="{{ url('/register') }}" class="btn btn-gold px-4 rounded-3">حساب جديد</a>
                </div>
            </div>
        </div>
    </nav>

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

                    <form action="#" method="POST" id="contactForm">
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
