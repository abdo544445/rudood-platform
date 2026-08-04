
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>منصة ردود - المميزات</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  <!-- Google Fonts (Cairo) -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    /* تنسيق البطاقات الزجاجية للصفحة */
    .glass-card {
      background: rgba(255, 255, 255, 0.03) !important;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(212, 175, 55, 0.15);
      box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
      transition: transform 0.3s ease, border-color 0.3s ease;
    }
    .glass-card:hover {
      transform: translateY(-5px);
      border-color: rgba(212, 175, 55, 0.5);
    }
  </style>
</head>
<body>

  <!-- شريط التنقل العلوي (Navbar) -->
  <nav class="navbar navbar-expand-lg navbar-rodood sticky-top">
    <div class="container">
      
      <!-- 1. الشعار (جهة اليمين) -->
      <a class="navbar-brand d-flex align-items-center me-3" href="{{ url('/index') }}">
        <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" class="nav-logo-img">
      </a>

      <!-- زر القائمة للشاشات الصغيرة -->
      <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarRodood" aria-controls="navbarRodood" aria-expanded="false" aria-label="Toggle navigation">
        <i class="bi bi-list fs-2 text-gold"></i>
      </button>

      <!-- محتوى الهيدر -->
      <div class="collapse navbar-collapse" id="navbarRodood">
        
        <!-- 2. روابط التنقل -->
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold align-items-center">
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/index') }}">الرئيسية</a></li>
          <li class="nav-item"><a class="nav-link active text-gold fw-bold" href="{{ url('/features') }}">المميزات</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/pricing') }}">التسعيرة</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/blog') }}">المدونة</a></li>
          
          <!-- قائمة أقسام المنصة المنسدلة -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white-50" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              أقسام المنصة
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 mt-2" aria-labelledby="servicesDropdown">
              <li><a class="dropdown-item py-2" href="{{ url('/auto') }}"><i class="bi bi-robot me-2 text-gold"></i>الرد الآلي</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/chat') }}"><i class="bi bi-chat-dots me-2 text-gold"></i>المحادثات</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/ai') }}"><i class="bi bi-cpu me-2 text-gold"></i>الذكاء الاصطناعي</a></li>
            </ul>
          </li>

          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/try') }}">تواصل معنا</a></li>
        </ul>

        <!-- 3. الأزرار -->
        <div class="d-flex align-items-center gap-2">
          <a href="{{ url('/login') }}" class="btn btn-outline-light rounded-pill px-4">تسجيل الدخول</a>
          <a href="{{ url('/register') }}" class="btn btn-gold rounded-pill px-3 fw-bold d-flex align-items-center gap-1">
            <i class="bi bi-headset"></i> طلب استشارة
          </a>
        </div>

      </div>
    </div>
  </nav>

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
            <a href="{{ url('/login') }}" class="btn btn-gold px-5 py-3 fs-5 fw-bold rounded-pill">جرب المنصة الآن</a>
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
