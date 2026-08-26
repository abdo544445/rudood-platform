<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>منصة ردود - المميزات | أتمتة خدمة العملاء بالذكاء الاصطناعي</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  <!-- Google Fonts (Cairo) -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  @include('layouts.partials.theme')
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
          <li class="nav-item"><a class="nav-link active text-gold" href="{{ url('/features') }}">المميزات</a></li>
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

          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/contact') }}">تواصل معنا</a></li>
        </ul>

        <!-- 3. الأزرار -->
        <div class="d-flex align-items-center gap-2">
          <a href="{{ url('/demo') }}" class="btn btn-outline-danger rounded-pill px-3 fs-8 fw-bold d-flex align-items-center gap-1">
            <i class="bi bi-broadcast text-danger"></i> استعراض حي
          </a>
          <a href="{{ url('/login') }}" class="btn btn-outline-light rounded-pill px-3 fs-8">تسجيل الدخول</a>
          <a href="{{ url('/register') }}" class="btn btn-gold rounded-pill px-3 fw-bold fs-8 d-flex align-items-center gap-1">
            <i class="bi bi-headset"></i> ابدأ الآن
          </a>
        </div>

      </div>
    </div>
  </nav>

  <!-- قسم العنوان الرئيسي للصفحة -->
  <section class="py-5 text-center position-relative">
    <div class="container py-4">
      <span class="badge bg-gold-subtle text-gold rounded-pill px-3 py-2 mb-3 border border-warning border-opacity-25 fs-6">حلول متكاملة وأدوات ذكية</span>
      <h1 class="display-4 fw-extrabold mb-3 text-white">مميزات <span class="text-gold-gradient">منصة ردود</span></h1>
      <p class="lead text-white-50 mx-auto" style="max-width: 650px;">
        كافة الإمكانيات والتقنيات التي تحتاجها لتطوير خدمة العملاء، أتمتة المحادثات، والارتقاء بأعمالك باحترافية تامة.
      </p>
    </div>
  </section>

  <!-- قسم شبكة المميزات -->
  <main class="pb-5">
    <div class="container">
      <div class="row g-4 justify-content-center align-items-stretch">
        
        <!-- كارت 1 -->
        <div class="col-lg-4 col-md-6">
          <div class="pricing-card h-100 p-4 rounded-4 position-relative d-flex flex-column text-white">
            <div class="mb-3 text-gold fs-1">
              <i class="bi bi-robot"></i>
            </div>
            <h3 class="fw-bold fs-4 mb-2 text-white">ردود آلية ذكية 24/7</h3>
            <p class="text-white-50 small flex-grow-1 lh-lg">
              تقديم إجابات فورية ودقيقة لاستفسارات العملاء على مدار الساعة دون الحاجة للانتظار أو التدخل البشري.
            </p>
            <div class="pt-3 border-top border-white border-opacity-10 d-flex align-items-center gap-2 text-gold fs-7">
              <i class="bi bi-check-circle-fill"></i> استجابة لحظية متواصلة
            </div>
          </div>
        </div>

        <!-- كارت 2 -->
        <div class="col-lg-4 col-md-6">
          <div class="pricing-card h-100 p-4 rounded-4 position-relative d-flex flex-column text-white">
            <div class="mb-3 text-gold fs-1">
              <i class="bi bi-chat-dots"></i>
            </div>
            <h3 class="fw-bold fs-4 mb-2 text-white">إدارة المحادثات الموحدة</h3>
            <p class="text-white-50 small flex-grow-1 lh-lg">
              تجميع واستقبال محادثات العملاء من مختلف القنوات والمنصات (واتساب، انستغرام، الموقع) في لوحة تحكم واحدة.
            </p>
            <div class="pt-3 border-top border-white border-opacity-10 d-flex align-items-center gap-2 text-gold fs-7">
              <i class="bi bi-check-circle-fill"></i> ربط موحد للقنوات
            </div>
          </div>
        </div>

        <!-- كارت 3 -->
        <div class="col-lg-4 col-md-6">
          <div class="pricing-card h-100 p-4 rounded-4 position-relative d-flex flex-column text-white">
            <div class="mb-3 text-gold fs-1">
              <i class="bi bi-graph-up-arrow"></i>
            </div>
            <h3 class="fw-bold fs-4 mb-2 text-white">تحليلات وتقارير أداء</h3>
            <p class="text-white-50 small flex-grow-1 lh-lg">
              متابعة إحصائيات الدعم الفني، معدل سرعة الاستجابة، ونسبة حل المشكلات عبر تقارير تفاعلية شاملة.
            </p>
            <div class="pt-3 border-top border-white border-opacity-10 d-flex align-items-center gap-2 text-gold fs-7">
              <i class="bi bi-check-circle-fill"></i> تقارير ذكية دقيقة
            </div>
          </div>
        </div>

        <!-- كارت 4 -->
        <div class="col-lg-4 col-md-6">
          <div class="pricing-card h-100 p-4 rounded-4 position-relative d-flex flex-column text-white">
            <div class="mb-3 text-gold fs-1">
              <i class="bi bi-cpu"></i>
            </div>
            <h3 class="fw-bold fs-4 mb-2 text-white">تدريب المساعد الذكي</h3>
            <p class="text-white-50 small flex-grow-1 lh-lg">
              إمكانية رفع ملفاتك (PDF / Word) وتدريب الذكاء الاصطناعي على بيانات شركتك للإجابة وفق سياق عملك تماماً.
            </p>
            <div class="pt-3 border-top border-white border-opacity-10 d-flex align-items-center gap-2 text-gold fs-7">
              <i class="bi bi-check-circle-fill"></i> تخصيص كامل للمحتوى
            </div>
          </div>
        </div>

        <!-- كارت 5 -->
        <div class="col-lg-4 col-md-6">
          <div class="pricing-card h-100 p-4 rounded-4 position-relative d-flex flex-column text-white">
            <div class="mb-3 text-gold fs-1">
              <i class="bi bi-shield-lock"></i>
            </div>
            <h3 class="fw-bold fs-4 mb-2 text-white">أمان وحماية البيانات</h3>
            <p class="text-white-50 small flex-grow-1 lh-lg">
              تشفير كامل للبيانات والمحادثات للحفاظ على خصوصية عملاء المنصة والشركات وفق أرفع معايير الأمان.
            </p>
            <div class="pt-3 border-top border-white border-opacity-10 d-flex align-items-center gap-2 text-gold fs-7">
              <i class="bi bi-check-circle-fill"></i> تشفير تام للبيانات
            </div>
          </div>
        </div>

        <!-- كارت 6 -->
        <div class="col-lg-4 col-md-6">
          <div class="pricing-card h-100 p-4 rounded-4 position-relative d-flex flex-column text-white">
            <div class="mb-3 text-gold fs-1">
              <i class="bi bi-headset"></i>
            </div>
            <h3 class="fw-bold fs-4 mb-2 text-white">تحويل سلس للدعم البشري</h3>
            <p class="text-white-50 small flex-grow-1 lh-lg">
              تحويل التذاكر أو المحادثات المعقدة بسلاسة إلى موظفي الدعم البشري عند الحاجة مع توفير سجل المحادثة الكامل.
            </p>
            <div class="pt-3 border-top border-white border-opacity-10 d-flex align-items-center gap-2 text-gold fs-7">
              <i class="bi bi-check-circle-fill"></i> تسليم ذكي للموظفين
            </div>
          </div>
        </div>

        <!-- كارت 7: ودجت المحادثة للموقع -->
        <div class="col-lg-4 col-md-6">
          <div class="pricing-card h-100 p-4 rounded-4 position-relative d-flex flex-column text-white">
            <div class="mb-3 text-gold fs-1">
              <i class="bi bi-globe2"></i>
            </div>
            <h3 class="fw-bold fs-4 mb-2 text-white">ودجت محادثة عائم للموقع</h3>
            <p class="text-white-50 small flex-grow-1 lh-lg">
              ودجت ذكي متكامل تضيفه لمتجرك في سلة، زد، شوبيفاي، أو موقعك بسطر كود واحد فقط، مع تخصيص كامل للألوان ورسائل الترحيب.
            </p>
            <div class="pt-3 border-top border-white border-opacity-10 d-flex align-items-center gap-2 text-gold fs-7">
              <i class="bi bi-check-circle-fill"></i> تضمين فوري بسطر واحد
            </div>
          </div>
        </div>

        <!-- كارت 8: إنستغرام دايركت والتعليقات -->
        <div class="col-lg-4 col-md-6">
          <div class="pricing-card h-100 p-4 rounded-4 position-relative d-flex flex-column text-white">
            <div class="mb-3 text-gold fs-1">
              <i class="bi bi-instagram"></i>
            </div>
            <h3 class="fw-bold fs-4 mb-2 text-white">إنستغرام دايركت والتعليقات</h3>
            <p class="text-white-50 small flex-grow-1 lh-lg">
              الرد الفوري على رسائل الخاص بالذكاء الاصطناعي، وأتمتة الرد على التعليقات ومراسلة العملاء في الخاص تلقائياً.
            </p>
            <div class="pt-3 border-top border-white border-opacity-10 d-flex align-items-center gap-2 text-gold fs-7">
              <i class="bi bi-check-circle-fill"></i> أتمتة شاملة للمنشورات والخاص
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- قسم الدعوة للإجراء الختامي (CTA) -->
  <section class="py-5 border-top border-white border-opacity-10">
    <div class="container text-center">
      <div class="pricing-card featured p-5 rounded-4 mx-auto" style="max-width: 800px;">
        <h2 class="fw-bold text-white mb-3">جاهز لتطوير خدمة عملائك؟</h2>
        <p class="text-white-50 mb-4 fs-5">ابدأ الآن واجعل منصة ردود تجعل دعم العملاء لديك أكثر سهولة وسرعة.</p>
        <a href="{{ url('/login') }}" class="btn btn-gold px-5 py-3 fs-5 fw-bold rounded-pill shadow-gold">جرب المنصة الآن</a>
      </div>
    </div>
  </section>

  <!-- الفوتر / أسفل الصفحة -->
  <footer class="py-4 border-top border-white border-opacity-10 text-center text-white-50 fs-7">
    <div class="container">
      <p class="m-0">جميع الحقوق محفوظة © 2026 منصة ردود (Rudood)</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
