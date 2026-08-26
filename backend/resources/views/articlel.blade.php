<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $article->title }} - منصة ردود</title>
  
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

  <!-- Navbar الشريط العلوي -->
  <nav class="navbar navbar-expand-lg navbar-rodood sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center me-3" href="{{ url('/index') }}">
        <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" class="nav-logo-img">
      </a>
      <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarRodood">
        <i class="bi bi-list fs-2 text-gold"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarRodood">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold align-items-center">
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/index') }}">الرئيسية</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/features') }}">المميزات</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/pricing') }}">التسعيرة</a></li>
          <li class="nav-item"><a class="nav-link active text-gold" href="{{ url('/blog') }}">المدونة</a></li>
          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white-50" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">أقسام المنصة</a>
            <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 mt-2" aria-labelledby="servicesDropdown">
              <li><a class="dropdown-item py-2" href="{{ url('/auto') }}"><i class="bi bi-robot me-2 text-gold"></i>الرد الآلي</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/chat') }}"><i class="bi bi-chat-dots me-2 text-gold"></i>المحادثات</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/ai') }}"><i class="bi bi-cpu me-2 text-gold"></i>الذكاء الاصطناعي</a></li>
            </ul>
          </li>

          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/try') }}">تواصل معنا</a></li>
        </ul>
        <div class="d-flex align-items-center gap-2">
          <a href="{{ url('/login') }}" class="btn btn-outline-light rounded-pill px-4">تسجيل الدخول</a>
          <a href="{{ url('/register') }}" class="btn btn-gold rounded-pill px-3 fw-bold d-flex align-items-center gap-1">
            <i class="bi bi-headset"></i> طلب استشارة
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Article Header عنوان المقال ومعلوماته -->
  <section class="py-5 text-center position-relative border-bottom border-white border-opacity-10">
    <div class="container py-3">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <a href="{{ url('/blog') }}" class="text-gold text-decoration-none fs-7 mb-3 d-inline-block fw-bold">
            <i class="bi bi-arrow-right me-1"></i> العودة لجميع المقالات
          </a>
          <h1 class="fw-bold display-6 text-white mb-4">{{ $article->title }}</h1>
          <div class="d-flex align-items-center justify-content-center gap-4 text-white-50 fs-7 flex-wrap">
            <span><i class="bi bi-calendar3 text-gold me-1"></i> {{ $article->published_at ? $article->published_at->format('Y-m-d') : '' }}</span>
            <span><i class="bi bi-clock text-gold me-1"></i> {{ $article->read_time }}</span>
            <span><i class="bi bi-tag text-gold me-1"></i> {{ $article->category }}</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Article Content محتوى المقال -->
  <section class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card pricing-card border-white border-opacity-10 p-4 p-md-5 text-white rounded-4">
            
            <!-- آيقونة/صورة تعبيرية للمقال -->
            <div class="text-center py-4 mb-4 rounded-3 bg-dark bg-opacity-50 border border-white border-opacity-10">
              <i class="bi {{ $article->icon ?? 'bi-robot' }} text-gold display-1"></i>
            </div>

            <!-- نص المقال -->
            <div class="lh-lg text-white-50 fs-6 article-body">
              {!! $article->content !!}
            </div>

            <!-- أزرار المشاركة والعودة -->
            <div class="border-top border-white border-opacity-10 pt-4 mt-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
              <a href="{{ url('/blog') }}" class="btn btn-outline-light rounded-pill px-4 fs-7">
                <i class="bi bi-arrow-right me-1"></i> العودة لجميع المقالات
              </a>
              <a href="{{ url('/login') }}" class="btn btn-gold text-dark fw-bold rounded-pill px-4 fs-7 shadow-gold">
                ابدأ التجربة المجانية
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer الفوتر -->
  <footer class="py-4 border-top border-white border-opacity-10 text-center text-white-50 fs-7">
    <div class="container">
      <p class="mb-0">جميع الحقوق محفوظة © 2026 منصة ردود (Rudood)</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
