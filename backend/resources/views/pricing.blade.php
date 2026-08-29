<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>خطط الأسعار  - منصة ردود</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  <!-- Google Fonts (Cairo) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  @include('layouts.partials.theme')
</head>
<body>

  <!-- الهيدر / شريط التنقل -->
  <nav class="navbar navbar-expand-lg navbar-rodood sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2 m-0" href="{{ url('/index') }}">
        <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" class="nav-logo-img">
      </a>
      
      <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <i class="bi bi-list fs-2"></i>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/index') }}">الرئيسية</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/features') }}">المميزات</a></li>
          <li class="nav-item"><a class="nav-link active text-gold" href="{{ url('/pricing') }}">التسعيرة</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/blog') }}">المدونة</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/how-it-works') }}">دليل التشغيل</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white-50" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">أقسام المنصة</a>
            <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 mt-2" aria-labelledby="servicesDropdown">
              <li><a class="dropdown-item py-2" href="{{ url('/auto') }}"><i class="bi bi-robot me-2 text-gold"></i>الرد الآلي (استعراض حي)</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/chat') }}"><i class="bi bi-chat-dots me-2 text-gold"></i>المحادثات (استعراض حي)</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/ai') }}"><i class="bi bi-cpu me-2 text-gold"></i>الذكاء الاصطناعي (استعراض حي)</a></li>
              <li><hr class="dropdown-divider border-secondary opacity-25"></li>
              <li><a class="dropdown-item py-2 text-gold fw-bold" href="{{ url('/how-it-works') }}"><i class="bi bi-book-half me-2 text-gold"></i>دليل تشغيل البوت</a></li>
              <li><a class="dropdown-item py-2 text-danger fw-bold" href="{{ url('/demo') }}"><i class="bi bi-broadcast me-2 text-danger"></i>استعراض حي شامل</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/contact') }}">تواصل معنا</a></li>
        </ul>
        <div class="d-flex align-items-center gap-2">
          <a href="{{ url('/demo') }}" class="btn btn-outline-danger rounded-pill px-3 fs-8 fw-bold d-flex align-items-center gap-1">
            <i class="bi bi-broadcast text-danger"></i> استعراض حي
          </a>
          <a href="{{ url('/login') }}" class="btn btn-outline-light rounded-pill px-3 fs-8">تسجيل الدخول</a>
          <a href="{{ url('/register') }}" class="btn btn-gold rounded-pill px-3 fw-bold fs-8">ابدأ الآن</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- قسم العنوان الرئيسي للأسعار -->
  <section class="py-5 text-center position-relative">
    <div class="container py-4">
      <span class="badge bg-gold-subtle text-gold rounded-pill px-3 py-2 mb-3 border border-warning border-opacity-25 fs-6">خطط مرنة تناسب الجميع</span>
      <h1 class="display-4 fw-extrabold mb-3 text-white">اختر الباقة المناسبة <span class="text-gold-gradient">لنمو أعمالك</span></h1>
      <p class="lead text-white-50 mx-auto" style="max-width: 600px;">
        خطط أسعار شفافة وبدون تكاليف خفية. يمكنك الترقية أو الإلغاء في أي وقت بسهولة.
      </p>
    </div>
  </section>

  <!-- كروت باقات الأسعار -->
  <section class="pb-5">
    <div class="container">
      <div class="row g-4 justify-content-center align-items-stretch">
        
        <!-- الباقة الأولى: المبتدئين -->
        <div class="col-lg-4 col-md-6">
          <div class="pricing-card h-100 p-4 rounded-4 position-relative d-flex flex-column text-white">
            <h3 class="fw-bold fs-4 mb-2 text-white">الباقة الأساسية</h3>
            <p class="text-white-50 small mb-4">مثالية للمتاجر الناشئة والمشاريع الصغيرة.</p>
            
            <div class="pricing-price mb-4">
              <span class="display-4 fw-extrabold text-white">900</span>
              <span class="text-white-50" >رس / شهرياً</span>
            </div>

            <ul class="list-unstyled d-flex flex-column gap-3 mb-4 flex-grow-1 text-white">
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> حتى 1,000 رد تلقائي شهرياً</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> ربط قناة واحدة (واتساب أو انستغرام)</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> لوحة تحليلات بسيطة</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> دعم عبر البريد الإلكتروني</li>
              <li class="d-flex align-items-center gap-2 text-white-50 opacity-50"><i class="bi bi-x-circle fs-5"></i> تخصيص الذكاء الاصطناعي المتقدم</li>
            </ul>

            <a href="{{ url('/register') }}" class="btn btn-outline-light rounded-pill py-2.5 fw-bold w-100">ابدأ الآن</a>
          </div>
        </div>

        <!-- الباقة الثانية: الاحترافية (الأكثر طلباً) -->
        <div class="col-lg-4 col-md-6">
          <div class="pricing-card featured h-100 p-4 rounded-4 position-relative d-flex flex-column border-gold text-white">
            <div class="popular-badge">الأكثر شعبية</div>
            <h3 class="fw-bold fs-4 mb-2 text-gold">الباقة الاحترافية</h3>
            <p class="text-white-50 small mb-4">الخيار الأمثل للشركات المتنامية والمتوسطة.</p>
            
            <div class="pricing-price mb-4">
              <span class="display-4 fw-extrabold text-white">2000</span>
              <span class="text-white-50">رس / شهرياً</span>
            </div>

            <ul class="list-unstyled d-flex flex-column gap-3 mb-4 flex-grow-1 text-white">
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> ردود غير محدودة</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> ربط كافة القنوات (واتساب، انستغرام، الموقع)</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> تدريب الذكاء الاصطناعي على بياناتك الخاصة</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> تقارير وتحليلات ذكية شاملة</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> دعم فني مباشر 24/7</li>
            </ul>

            <a href="{{ url('/register') }}" class="btn btn-gold rounded-pill py-2.5 fw-bold w-100 shadow-gold">اشترك الآن</a>
          </div>
        </div>

        <!-- الباقة الثالثة: الشركات -->
        <div class="col-lg-4 col-md-6">
          <div class="pricing-card h-100 p-4 rounded-4 position-relative d-flex flex-column text-white">
            <h3 class="fw-bold fs-4 mb-2 text-white">باقة المؤسسات</h3>
            <p class="text-white-50 small mb-4">للمؤسسات الكبيرة التي تحتاج حلولاً مخصصة.</p>
            
            <div class="pricing-price mb-4">
              <span class="fs-2 fw-extrabold text-white">سعر مخصص</span>
            </div>

            <ul class="list-unstyled d-flex flex-column gap-3 mb-4 flex-grow-1 text-white">
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> جميع مميزات الباقة الاحترافية</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> سيرفرات خاصة وأمان متقدم (Enterprise API)</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> مدير حساب خاص لخدمتك</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> اتفاقية مستوى الخدمة (SLA 99.9%)</li>
              <li class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-gold fs-5"></i> تخصيص برمجي كامل للهوية</li>
            </ul>

            <a href="{{ url('/try') }}" class="btn btn-outline-light rounded-pill py-2.5 fw-bold w-100">تواصل مع المبيعات</a>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- قسم الأسئلة الشائعة -->
  <section class="py-5 border-top border-white border-opacity-10">
    <div class="container" style="max-width: 800px;">
      <h2 class="text-center fw-bold mb-5 text-white">الأسئلة الشائعة حول <span class="text-gold">الأسعار</span></h2>
      
      <div class="accordion accordion-flush custom-accordion" id="faqAccordion">
        
        <div class="accordion-item mb-3 rounded-3 overflow-hidden border border-white border-opacity-10">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed bg-transparent text-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
              هل يمكنني تجربة المنصة مجاناً قبل الاشتراك؟
            </button>
          </h2>
          <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-white-50">
              نعم، نوفر تجربة مجانية لمدة 7 أيام بكامل المميزات لتجربة كيف يعمل الذكاء الاصطناعي مع عملاؤك قبل اتخاذ قرار الاشتراك.
            </div>
          </div>
        </div>

        <div class="accordion-item mb-3 rounded-3 overflow-hidden border border-white border-opacity-10">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed bg-transparent text-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
              هل يمكنني تغيير باقتي في أي وقت؟
            </button>
          </h2>
          <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-white-50">
              بالتأكيد! يمكنك الترقية إلى باقة أعلى أو تخفيض الباقة بسهولة من خلال لوحة التحكم الخاصة بك وفي أي وقت.
            </div>
          </div>
        </div>

        <div class="accordion-item mb-3 rounded-3 overflow-hidden border border-white border-opacity-10">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed bg-transparent text-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
              هل توجد أي رسوم خفية أو إعدادات إضافية؟
            </button>
          </h2>
          <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-white-50">
              لا توجد أي رسوم خفية أبداً. السعر الموضح في الباقة هو كل ما تدفعه شاملاً التحديثات والدعم الفني.
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- الفوتر / أسفل الصفحة -->
  <footer class="py-4 border-top border-white border-opacity-10 text-center text-white-50 fs-7">
    <div class="container">
      <p class="m-0">جميع الحقوق محفوظة © 2026 منصة ردود (Rudood)</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
