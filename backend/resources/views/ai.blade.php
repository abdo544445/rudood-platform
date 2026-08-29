
  <!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الذكاء الاصطناعي 24/7 - منصة ردود</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  <!-- Google Fonts (Cairo) -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  @include('layouts.partials.theme')
</head>
  <style>
    .nav-logo-img { height: 42px; width: auto; object-fit: contain; }
    .glass-navbar {
      background: rgba(11, 15, 25, 0.75) !important;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    }
  </style>
</head>
<body class="bg-dark text-white font-cairo">

  <!-- Navbar الهيدر -->
  <nav class="navbar navbar-expand-lg navbar-dark glass-navbar sticky-top py-2">
    <div class="container-fluid px-4">
      <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/index') }}">
        <img src="{{ asset('images/img.png') }}" alt="شعار ردود" class="nav-logo-img">
        <span class="fw-bold fs-3 text-gold"></span>
      </a>

      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAiPage">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarAiPage">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
          <li class="nav-item"><a class="nav-link px-3 text-white-50" href="{{ url('/index') }}">الرئيسية</a></li>
          <li class="nav-item"><a class="nav-link px-3 text-white-50" href="{{ url('/features') }}">المميزات</a></li>
          <li class="nav-item"><a class="nav-link px-3 text-white-50" href="{{ url('/pricing') }}">التسعيرة</a></li>
          <li class="nav-item"><a class="nav-link px-3 text-white-50" href="{{ url('/blog') }}">المدونة</a></li>
          <li class="nav-item"><a class="nav-link px-3 text-white-50" href="{{ url('/how-it-works') }}">دليل التشغيل</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle px-3 active text-gold fw-bold" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              أقسام المنصة
            </a>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border border-secondary border-opacity-25 rounded-3 mt-2">
              <li><a class="dropdown-item py-2" href="{{ url('/auto') }}"><i class="bi bi-robot me-2 text-gold"></i>الرد الآلي (استعراض حي)</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/chat') }}"><i class="bi bi-chat-dots me-2 text-gold"></i>المحادثات (استعراض حي)</a></li>
              <li><a class="dropdown-item py-2 active text-gold fw-bold" href="{{ url('/ai') }}"><i class="bi bi-cpu me-2 text-gold"></i>الذكاء الاصطناعي (استعراض حي)</a></li>
              <li><hr class="dropdown-divider border-secondary opacity-25"></li>
              <li><a class="dropdown-item py-2 text-gold fw-bold" href="{{ url('/how-it-works') }}"><i class="bi bi-book-half me-2 text-gold"></i>دليل تشغيل البوت</a></li>
              <li><a class="dropdown-item py-2 text-danger fw-bold" href="{{ url('/demo') }}"><i class="bi bi-broadcast me-2 text-danger"></i>استعراض حي شامل</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link px-3 text-white-50" href="{{ url('/contact') }}">تواصل معنا</a></li>
        </ul>

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

  <!-- Hero Section -->
  <section class="py-5 text-center position-relative">
    <div class="container py-5">
      <span class="badge bg-gold-subtle text-gold border border-gold border-opacity-25 px-3 py-2 rounded-pill mb-3 fw-bold">
        <i class="bi bi-cpu me-1"></i> تقنية فهم اللغة الطبيعية
      </span>
      <h1 class="display-4 fw-extrabold text-white mb-3">ذكاء اصطناعي يتحدث بلغة عملائك 24/7</h1>
      <p class="lead text-white-50 mx-auto mb-4" style="max-width: 750px;">
        درب مساعدك الذكي عبر رفع ملفات المنتجات والكتالوجات. يفهم الاستفسارات المعقدة باللهجة المحلية ويقدم إجابات دقيقة دون تدخل بشري.
      </p>
      <div class="d-flex justify-content-center gap-3">
        <a href="{{ url('/chat') }}" class="btn btn-gold text-dark fw-bold rounded-pill px-4 py-3">تجربة المساعد الذكي</a>
        <a href="{{ url('/login') }}" class="btn btn-outline-light rounded-pill px-4 py-3">درب بوتك الآن</a>
      </div>
    </div>
  </section>

  <!-- بطاقات المميزات المميزة -->
  <section class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold text-gold">قدرات الذكاء الاصطناعي في "ردود"</h2>
        <p class="text-white-50">حلول متقدمة لجعل خدمة العملاء أكثر كفاءة وسرعة</p>
      </div>

      <div class="row g-4">
        <!-- بطاقة 1 -->
        <div class="col-md-6 col-lg-3">
          <div class="chat-sim-card p-4 h-100 text-center">
            <div class="text-gold display-5 mb-3"><i class="bi bi-translate"></i></div>
            <h5 class="fw-bold text-white mb-2">فهم اللهجات المحلية</h5>
            <p class="text-white-50 fs-7 mb-0">يتعرف على مصطلحات العملاء المختلفة والأخطاء الإملائية ويفهم المعنى المقصود بدقة.</p>
          </div>
        </div>

        <!-- بطاقة 2 -->
        <div class="col-md-6 col-lg-3">
          <div class="chat-sim-card p-4 h-100 text-center">
            <div class="text-gold display-5 mb-3"><i class="bi bi-file-earmark-pdf"></i></div>
            <h5 class="fw-bold text-white mb-2">التعلم من المستندات</h5>
            <p class="text-white-50 fs-7 mb-0">يمكنك رفع ملفات PDF، Excel، أو روابط الموقع، وسيتعلم الذكاء الاصطناعي منها فوراً.</p>
          </div>
        </div>

        <!-- بطاقة 3 -->
        <div class="col-md-6 col-lg-3">
          <div class="chat-sim-card p-4 h-100 text-center">
            <div class="text-gold display-5 mb-3"><i class="bi bi-emoji-smile"></i></div>
            <h5 class="fw-bold text-white mb-2">تحليل مشاعر العملاء</h5>
            <p class="text-white-50 fs-7 mb-0">يكتشف نبرة العميل (غالٍ، مستعجل، غاضب) ويوجه المحادثة للأسلوب الأمثل أو ينبه فريقك.</p>
          </div>
        </div>

        <!-- بطاقة 4 -->
        <div class="col-md-6 col-lg-3">
          <div class="chat-sim-card p-4 h-100 text-center">
            <div class="text-gold display-5 mb-3"><i class="bi bi-cart-check"></i></div>
            <h5 class="fw-bold text-white mb-2">توصيات مبيعات ذكية</h5>
            <p class="text-white-50 fs-7 mb-0">يقترح المنتجات المناسبة بناءً على طلب العميل لمساعدتك في زيادات إجمالي المبيعات.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-4 border-top border-secondary border-opacity-25 text-center text-white-50 fs-7">
    <div class="container">
      <p class="mb-0">جميع الحقوق محفوظة © 2026 منصة ردود (Rudood)</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
