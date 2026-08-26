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
  @yield('scripts')
</body>
</html>
