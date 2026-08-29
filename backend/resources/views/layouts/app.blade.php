<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'لوحة التحكم | منصة ردود')</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Reem+Kufi:wght@700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">

  @include('layouts.partials.theme')
  @yield('styles')
</head>
<body>

  <!-- شريط الموبايل العلوي -->
  <div class="mobile-top-bar">
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-dark border border-warning border-opacity-40 text-gold btn-sm rounded-3 py-1 px-2.5" id="mobileSidebarToggle" aria-label="القائمة الجانبية">
        <i class="bi bi-list fs-5"></i>
      </button>
      <a href="{{ url('/') }}" class="d-inline-flex align-items-center">
        <img src="{{ asset('images/img.png') }}" alt="منصة ردود" style="height: 32px;">
      </a>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-dark border border-secondary border-opacity-40 text-gold btn-sm rounded-circle p-1" data-bs-toggle="modal" data-bs-target="#commandPaletteModal" style="width:34px; height:34px;" title="بحث سريع (⌘K)">
        <i class="bi bi-search fs-7"></i>
      </button>
      <span class="badge bg-dark border border-warning border-opacity-30 text-white fs-9 px-2 py-1">
        {{ auth()->user()->workspace->company_name ?? 'متجري' }}
      </span>
    </div>
  </div>

  <!-- ستارة الخلفية للشاشات الصغيرة -->
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <!-- الشريط الجانبي -->
  @include('layouts.partials.sidebar')

  <!-- المحتوى الرئيسي -->
  <main class="main-content">
    @if(session()->has('impersonated_by_admin'))
      <div class="alert alert-warning d-flex justify-content-between align-items-center py-2 px-3 mb-4 rounded-3 border border-warning" style="background: rgba(212,175,55,0.15); color: #d4af37;">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-shield-lock-fill fs-5"></i>
          <span>أنت تتصفح حالياً بصفتك: <strong>{{ auth()->user()->name }}</strong> (مساحة: {{ auth()->user()->workspace->company_name ?? '' }})</span>
        </div>
        <form action="{{ route('impersonate.leave') }}" method="POST" class="m-0">
          @csrf
          <button type="submit" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold">
            <i class="bi bi-box-arrow-left me-1"></i> العودة للوحة الإدارة العليا (Super Admin)
          </button>
        </form>
      </div>
    @endif
    @yield('content')
  </main>

  <!-- Global Command Palette (Cmd + K) -->
  @include('layouts.partials.command-palette')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const toggleBtn = document.getElementById('mobileSidebarToggle');
      const sidebar = document.querySelector('.sidebar');
      const backdrop = document.getElementById('sidebarBackdrop');

      if (toggleBtn && sidebar && backdrop) {
        toggleBtn.addEventListener('click', () => {
          sidebar.classList.toggle('show');
          backdrop.classList.toggle('show');
        });

        backdrop.addEventListener('click', () => {
          sidebar.classList.remove('show');
          backdrop.classList.remove('show');
        });
      }
    });
  </script>
  @yield('scripts')
</body>
</html>
