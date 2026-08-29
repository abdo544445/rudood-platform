<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>خدمات الرد الآلي - منصة ردود</title>
  
    <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  <!-- Google Fonts (Cairo) -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  @include('layouts.partials.theme')

  <style>
    /* تنسيقات إضافية خاصة بهيدر وشعار الصفحة */
    .nav-logo-img {
      height: 42px;
      width: auto;
      object-fit: contain;
    }

    .glass-navbar {
      background: rgba(11, 15, 25, 0.75) !important;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    }
  </style>
</head>
<body class="bg-dark text-white font-cairo">

  <!-- 1. الهيدر والشعار (Navbar) التناسق المضبوط -->
  <nav class="navbar navbar-expand-lg navbar-dark glass-navbar sticky-top py-2">
    <div class="container-fluid px-4">
      
      <!-- الشعار واسم المنصة -->
      <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/index') }}">
        <img src="{{ asset('images/img.png') }}" alt="شعار ردود" class="nav-logo-img">
        <span class="fw-bold fs-3 text-gold"></span>
      </a>

      <!-- زر القائمة للهواتف -->
      <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAutoPage">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- روابط التنقل وأزرار الحساب -->
      <div class="collapse navbar-collapse" id="navbarAutoPage">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link px-3 text-white-50" href="{{ url('/index') }}">الرئيسية</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-3 text-white-50" href="{{ url('/features') }}">المميزات</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-3 text-white-50" href="{{ url('/pricing') }}">التسعيرة</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-3 text-white-50" href="{{ url('/blog') }}">المدونة</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-3 text-white-50" href="{{ url('/how-it-works') }}">دليل التشغيل</a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle px-3 active text-gold fw-bold" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              أقسام المنصة
            </a>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border border-secondary border-opacity-25 rounded-3 mt-2">
              <li><a class="dropdown-item py-2 active text-gold fw-bold" href="{{ url('/auto') }}"><i class="bi bi-robot me-2 text-gold"></i>الرد الآلي (استعراض حي)</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/chat') }}"><i class="bi bi-chat-dots me-2 text-gold"></i>المحادثات (استعراض حي)</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/ai') }}"><i class="bi bi-cpu me-2 text-gold"></i>الذكاء الاصطناعي (استعراض حي)</a></li>
              <li><hr class="dropdown-divider border-secondary opacity-25"></li>
              <li><a class="dropdown-item py-2 text-gold fw-bold" href="{{ url('/how-it-works') }}"><i class="bi bi-book-half me-2 text-gold"></i>دليل تشغيل البوت</a></li>
              <li><a class="dropdown-item py-2 text-danger fw-bold" href="{{ url('/demo') }}"><i class="bi bi-broadcast me-2 text-danger"></i>استعراض حي شامل</a></li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link px-3 text-white-50" href="{{ url('/contact') }}">تواصل معنا</a>
          </li>
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

  <!-- 2. قسم العرض الرئيسي (Hero Section) مع بطاقة شات منسقة -->
  <section class="py-5 position-relative overflow-hidden">
    <div class="container py-4">
      <div class="row align-items-center g-4">
        
        <!-- العمود الأيمن: العناوين -->
        <div class="col-lg-6 text-start">
          <span class="badge bg-gold-subtle text-gold border border-gold border-opacity-25 px-3 py-2 rounded-pill mb-3 fw-bold">
            <i class="bi bi-robot me-1"></i> أتمتة الردود الذكية
          </span>
          <h1 class="display-5 fw-extrabold text-white mb-3">خدمات الرد الآلي الذكية في منصة ردود</h1>
          <p class="lead text-white-50 mb-4">
            تجاوب مع استفسارات العملاء تلقائياً باستخدام تقنيات فهم اللغة الطبيعية. خدمة مستمرة وسريعة دون توقف.
          </p>
          <div class="d-flex gap-3">
            <a href="{{ url('/chat') }}" class="btn btn-gold px-4 py-3 rounded-pill fw-bold">تجربة الرد الآلي</a>
            
          </div>
        </div>

        <!-- العمود الأيسر: بطاقة المحاكاة المنسقة (Chat Card) -->
        <div class="col-lg-6">
          <div class="chat-sim-card p-4 rounded-4">
            
            <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom border-secondary border-opacity-25">
              <div class="avatar-circle">R</div>
              <div>
                <h6 class="mb-0 fw-bold text-white">مساعد ردود الذكي</h6>
                <small class="text-gold fs-8"><i class="bi bi-circle-fill text-success me-1"></i> متصل الآن</small>
              </div>
            </div>

            <div class="d-flex flex-column gap-3">
              <!-- سؤال العميل -->
              <div class="chat-bubble-incoming p-3 text-start">
                مرحباً، ما هي أوقات العمل لديكم وكيف أستطيع الاشتراك؟
              </div>

              <!-- رد البوت -->
              <div class="chat-bubble-outgoing p-3 text-start">
                أهلاً بك! 🌸 نحن نعمل على مدار 24 ساعة طوال أيام الأسبوع. يمكنك الاشتراك مباشرة عبر الضغط على زر "جرب المنصة الآن" واختيار الباقة المناسبة لك.
              </div>

              <!-- سؤال العميل 2 -->
              <div class="chat-bubble-incoming p-3 text-start">
                هل يمكن للذكاء الاصطناعي الإجابة عن أسئلة منتجاتي؟
              </div>

              <!-- رد البوت 2 -->
              <div class="chat-bubble-outgoing p-3 text-start">
                بالتأكيد! بمجرد رفع كتالوج منتجاتك أو ملف الأسئلة الشائعة، سيتعلم مساعد ردود التفاعل والإجابة بدقة عالية. ✨
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
