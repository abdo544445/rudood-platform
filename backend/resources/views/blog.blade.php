<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>المدونة - منصة ردود</title>
  
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

          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/contact') }}">تواصل معنا</a></li>
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

  <!-- Header قسم العنوان الرئيسي -->
  <section class="py-5 text-center position-relative border-bottom border-white border-opacity-10">
    <div class="container py-4">
      <span class="badge bg-gold-subtle text-gold border border-warning border-opacity-25 rounded-pill px-3 py-2 mb-3 fs-6">
        <i class="bi bi-journal-text ms-1"></i> مركز المعرفة والأخبار
      </span>
      <h1 class="fw-bold display-5 text-white mb-3">مدونة <span class="text-gold-gradient">منصة ردود</span></h1>
      <p class="text-white-50 lead mx-auto" style="max-width: 600px;">
        استكشف أحدث المقالات والنصائح حول أتمتة خدمة العملاء، الذكاء الاصطناعي، وكيفية زيادة مبيعات متجرك بسهولة.
      </p>
    </div>
  </section>

  <!-- Main Content قسم المقالات -->
  <section class="py-5">
    <div class="container py-3">
      
      @if(isset($featured_article) && $featured_article)
      <!-- المقال المميز الرئيسي (Featured Post) -->
      <div class="card pricing-card featured text-white mb-5 border-gold overflow-hidden rounded-4">
        <div class="row g-0 align-items-center">
          <div class="col-lg-5">
            <div class="blog-img-holder p-5 text-center bg-dark bg-opacity-40">
              <i class="bi {{ $featured_article->icon ?? 'bi-robot' }} text-gold display-1"></i>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="card-body p-4 p-lg-5">
              <span class="badge bg-gold text-dark fw-bold mb-3 px-3 py-2">مقال مميز</span>
              <h2 class="card-title fw-bold mb-3 text-white h3">{{ $featured_article->title }}</h2>
              <p class="card-text text-white-50 mb-4 lh-lg">
                {{ $featured_article->summary }}
              </p>
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <small class="text-white-50"><i class="bi bi-calendar3 text-gold ms-1"></i> {{ $featured_article->published_at ? $featured_article->published_at->format('Y-m-d') : '' }}</small>
                <a href="{{ route('blog.show', $featured_article->slug) }}" class="btn btn-gold rounded-pill px-4 fw-bold">اقرأ المقال <i class="bi bi-arrow-left me-1"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif

      <!-- شبكة المقالات (Articles Grid) -->
      <div class="row g-4">
        @forelse($articles as $art)
        <div class="col-md-6 col-lg-4">
          <div class="card pricing-card h-100 rounded-4 text-white">
            <div class="card-body p-4 d-flex flex-column">
              <div class="mb-3 text-gold">
                <i class="bi {{ $art->icon ?? 'bi-journal-text' }} fs-1"></i>
              </div>
              <span class="text-gold fs-7 mb-2 fw-semibold">{{ $art->category }}</span>
              <h4 class="card-title fw-bold mb-3 fs-5 text-white">{{ $art->title }}</h4>
              <p class="card-text text-white-50 fs-7 mb-4 flex-grow-1 lh-lg">
                {{ $art->summary }}
              </p>
              <div class="d-flex align-items-center justify-content-between border-top border-white border-opacity-10 pt-3">
                <small class="text-white-50 fs-7"><i class="bi bi-clock text-gold me-1"></i> {{ $art->read_time }}</small>
                <a href="{{ route('blog.show', $art->slug) }}" class="text-gold text-decoration-none fw-bold fs-7">اقرأ المزيد <i class="bi bi-chevron-left"></i></a>
              </div>
            </div>
          </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 text-white-50">
          <i class="bi bi-journal-x fs-1 d-block mb-3 text-gold"></i>
          لا توجد مقالات أخرى في المدونة حالياً.
        </div>
        @endforelse
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
