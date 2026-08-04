<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'لوحة التحكم | منصة ردود')</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">

  @yield('styles')
</head>
<body>

  <!-- الشريط الجانبي -->
  <aside class="sidebar d-flex flex-column justify-content-between py-3">
    <div>
      <div class="px-4 mb-4 text-center">
        <a href="{{ url('/') }}">
          <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" style="max-height: 45px;">
        </a>
      </div>

      <ul class="nav nav-pills flex-column">
        <li class="nav-item">
          <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }} d-flex align-items-center gap-3">
            <i class="bi bi-grid-1x2-fill"></i> الرئيسية
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/ai-manage') }}" class="nav-link {{ request()->is('ai-manage') ? 'active' : '' }} d-flex align-items-center gap-3">
            <i class="bi bi-cpu-fill"></i> تدريب الذكاء الاصطناعي
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/live-chat') }}" class="nav-link {{ request()->is('live-chat') ? 'active' : '' }} d-flex align-items-center gap-3">
            <i class="bi bi-chat-dots-fill"></i> المحادثات المباشرة
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/settings') }}" class="nav-link {{ request()->is('settings') ? 'active' : '' }} d-flex align-items-center gap-3">
            <i class="bi bi-gear-fill"></i> الإعدادات والقنوات
          </a>
        </li>
      </ul>
    </div>

    <div class="px-3">
      <form action="{{ url('/logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
          <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
        </button>
      </form>
    </div>
  </aside>

  <!-- المحتوى الرئيسي -->
  <main class="main-content">
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  @yield('scripts')
</body>
</html>
