<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>منصة ردود - تم استلام طلب اشتراكك | بانتظار اعتماد مدير النظام</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  @include('layouts.partials.theme')
  <style>
    .pending-container {
      max-width: 720px;
      margin: 3rem auto;
    }
    .status-badge-glow {
      animation: pulse-border 2s infinite ease-in-out;
    }
    @keyframes pulse-border {
      0% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4); }
      70% { box-shadow: 0 0 0 15px rgba(212, 175, 55, 0); }
      100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); }
    }
    .step-track-item {
      position: relative;
      padding-right: 2.5rem;
      margin-bottom: 1.5rem;
    }
    .step-track-item::before {
      content: '';
      position: absolute;
      right: 11px;
      top: 24px;
      bottom: -16px;
      width: 2px;
      background: rgba(255, 255, 255, 0.1);
    }
    .step-track-item:last-child::before { display: none; }
    .step-bullet {
      position: absolute;
      right: 0;
      top: 0;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: bold;
    }
    .step-bullet.done { background: #10b981; color: #fff; }
    .step-bullet.active { background: #d4af37; color: #070a12; }
    .step-bullet.pending { background: rgba(255, 255, 255, 0.1); color: #94a3b8; }
  </style>
</head>
<body style="background-color: #070a12; color: #f8fafc; font-family: 'Cairo', sans-serif;">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-rodood">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="{{ url('/index') }}">
        <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" class="nav-logo-img">
      </a>
      <a href="{{ url('/login') }}" class="btn btn-outline-light rounded-pill px-4 btn-sm fw-bold">تسجيل الدخول</a>
    </div>
  </nav>

  <div class="container pending-container py-4">
    <div class="card card-custom p-4 p-md-5 border border-warning border-opacity-30 shadow-24 position-relative overflow-hidden" style="background: linear-gradient(145deg, rgba(21, 26, 48, 0.95) 0%, rgba(13, 17, 33, 0.98) 100%);">
      
      <div class="text-center mb-4">
        <div class="d-inline-flex p-3 rounded-circle mb-3 status-badge-glow" style="background: rgba(212, 175, 55, 0.15); color: #d4af37; border: 1px solid rgba(212, 175, 55, 0.3);">
          <i class="bi bi-clock-history fs-1"></i>
        </div>
        <h2 class="fw-black text-white mb-2">تم تسجيل طلب اشتراكك بنجاح! 🎉</h2>
        <p class="text-white-50 fs-8 mx-auto" style="max-width: 520px;">
          طلبك الآن قيد المراجعة لدى فريق إدارة منصة ردود. سيتم التواصل معك مباشرة للاتفاق واعتماد بريدك وتفعيل مساحة عمل متجرك.
        </p>
      </div>

      @if(session('info'))
      <div class="alert alert-warning border border-warning border-opacity-40 bg-dark text-gold fs-8 mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
        <div>{{ session('info') }}</div>
      </div>
      @endif

      @if(session('success'))
      <div class="alert alert-success border border-success border-opacity-40 bg-dark text-success fs-8 mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
        <div>{{ session('success') }}</div>
      </div>
      @endif

      @if(session('request_email') || request('email'))
      <div class="p-3 rounded-3 mb-4 border border-secondary border-opacity-25 text-center" style="background: rgba(255, 255, 255, 0.03);">
        <span class="text-white-50 fs-8">البريد الإلكتروني المسجل للطلب: </span>
        <strong class="text-gold">{{ session('request_email') ?? request('email') }}</strong>
      </div>
      @endif

      <!-- الخطوات والمراحل -->
      <div class="mb-4 p-4 rounded-4 border border-secondary border-opacity-20" style="background: rgba(7, 10, 18, 0.5);">
        <h6 class="fw-bold text-white mb-3"><i class="bi bi-list-stars text-gold me-2"></i>مراحل تفعيل حسابك:</h6>
        
        <div class="step-track-item">
          <div class="step-bullet done"><i class="bi bi-check"></i></div>
          <div class="fw-bold text-white fs-8">1. تقديم بيانات الاشتراك</div>
          <div class="text-white-50 fs-9">تم استلام بياناتك وتخزينها في قائمة الطلبات المعتمدة.</div>
        </div>

        <div class="step-track-item">
          <div class="step-bullet active"><i class="bi bi-hourglass-split"></i></div>
          <div class="fw-bold text-gold fs-8">2. التواصل والاتفاق مع مدير النظام (المرحلة الحالية)</div>
          <div class="text-white-50 fs-9">يقوم المشرف بمراجعة الباقة المحددة ومتطلبات متجرك والاتفاق معك.</div>
        </div>

        <div class="step-track-item">
          <div class="step-bullet pending">3</div>
          <div class="fw-bold text-white fs-8">3. موافقة المدير واعتماد البريد الإلكتروني</div>
          <div class="text-white-50 fs-9">يضيف المشرف العام بريدك بالنظام ليتم التعرف عليك تلقائياً وتفعيل الحساب.</div>
        </div>

        <div class="step-track-item">
          <div class="step-bullet pending">4</div>
          <div class="fw-bold text-white fs-8">4. استلام رسالة الترحيب والبدء بتزويد البوت بآلية عملك</div>
          <div class="text-white-50 fs-9">تصلك رسالة الترحيب: "أهلاً بكم في منصة ردود! زور صفحتك وزود البوت ببيانات وآلية عمل متجرك".</div>
        </div>
      </div>

      <!-- وسائل التواصل المباشر مع مدير النظام -->
      <div class="mb-4">
        <h6 class="fw-bold text-white mb-3 text-center"><i class="bi bi-chat-dots text-info me-2"></i>تواصل مباشرة مع مدير النظام لتسريع التفعيل:</h6>
        
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <a href="https://wa.me/966500000000?text={{ urlencode('مرحباً، أود تسريع تفعيل حسابي في منصة ردود لمتجري.') }}" target="_blank" class="btn btn-success w-100 py-3 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
              <i class="bi bi-whatsapp fs-5"></i>
              <span>محادثة واتساب مع الإدارة</span>
            </a>
          </div>

          <div class="col-12 col-md-6">
            <a href="tel:+966500000000" class="btn btn-outline-light w-100 py-3 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
              <i class="bi bi-telephone-fill text-gold"></i>
              <span>الاتصال الهاتفي المباشر</span>
            </a>
          </div>
        </div>
      </div>

      <div class="text-center pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
        <a href="{{ url('/how-it-works') }}" class="text-white-50 fs-8 text-decoration-none hover-gold">
          <i class="bi bi-info-circle me-1"></i> معرفة كيف يعمل البوت لمتجرك
        </a>
        <a href="{{ url('/login') }}" class="btn btn-sm btn-gold rounded-pill px-4 fw-bold">
          <i class="bi bi-box-arrow-in-left me-1"></i> تسجيل الدخول (بعد الاعتماد)
        </a>
      </div>

    </div>
  </div>

  <footer class="py-4 border-top border-secondary border-opacity-25 text-center text-white-50 fs-8">
    <div class="container">
      <p class="mb-0">منصة ردود (Rudood Platform) - أتمتة خدمة العملاء الذكية عبر القنوات المتعددة.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
