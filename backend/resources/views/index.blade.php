<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>منصة ردود | المنصة الرائدة لأتمتة خدمة العملاء بالذكاء الاصطناعي</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Reem+Kufi:wght@700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  @include('layouts.partials.theme')

  <style>
    /* Ambient Particle Canvas */
    #ambientCanvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      pointer-events: none;
      z-index: 0;
    }

    .main-page-wrapper {
      position: relative;
      z-index: 1;
    }

    /* Animated Arabic 'ردود' Typography */
    .arabic-brand-container {
      display: inline-flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 12px;
      position: relative;
    }

    .arabic-brand-title {
      font-family: 'Reem Kufi', 'Cairo', sans-serif;
      font-size: 3.8rem;
      font-weight: 900;
      letter-spacing: -1px;
      background: linear-gradient(135deg, #fff7d6 0%, #d4af37 40%, #f3d082 70%, #aa820a 100%);
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: goldShine 4s linear infinite, floatWord 3s ease-in-out infinite alternate;
      display: inline-block;
      filter: drop-shadow(0 0 25px rgba(212, 175, 55, 0.45));
    }

    @keyframes goldShine {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    @keyframes floatWord {
      0% { transform: translateY(0px); }
      100% { transform: translateY(-6px); }
    }

    /* Live Demo Button with Pulse */
    .btn-live-demo {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid #ef4444;
      color: #fca5a5 !important;
      font-weight: 700;
      border-radius: 50px;
      padding: 8px 18px;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-live-demo:hover {
      background: #ef4444;
      color: #fff !important;
      box-shadow: 0 0 20px rgba(239, 68, 68, 0.6);
      transform: translateY(-2px);
    }
    .live-dot-pulse {
      width: 9px;
      height: 9px;
      background-color: #ef4444;
      border-radius: 50%;
      box-shadow: 0 0 0 rgba(239, 68, 68, 0.7);
      animation: livePulse 1.5s infinite;
    }
    @keyframes livePulse {
      0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.8); }
      70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
      100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    /* Hero Layout 2-Column */
    .hero-container {
      min-height: calc(100vh - 80px);
      display: flex;
      align-items: center;
      padding: 40px 0;
    }

    /* Interactive Hero Mockup Card */
    .hero-mockup-window {
      background: rgba(15, 23, 42, 0.75);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(212, 175, 55, 0.35);
      border-radius: 20px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7), 0 0 30px rgba(212, 175, 55, 0.15);
      overflow: hidden;
      transform: perspective(1000px) rotateY(-4deg) rotateX(2deg);
      transition: transform 0.4s ease, box-shadow 0.4s ease;
    }
    .hero-mockup-window:hover {
      transform: perspective(1000px) rotateY(0deg) rotateX(0deg) scale(1.01);
      box-shadow: 0 30px 70px rgba(0, 0, 0, 0.8), 0 0 45px rgba(212, 175, 55, 0.25);
    }
    .mock-header {
      background: rgba(11, 15, 25, 0.85);
      padding: 12px 18px;
      border-bottom: 1px solid rgba(212, 175, 55, 0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .mock-window-dots {
      display: flex;
      gap: 6px;
    }
    .mock-dot { width: 11px; height: 11px; border-radius: 50%; }
    .mock-dot-red { background: #ef4444; }
    .mock-dot-yellow { background: #eab308; }
    .mock-dot-green { background: #22c55e; }

    .mock-chat-body {
      height: 330px;
      padding: 18px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 12px;
      background: rgba(11, 15, 25, 0.5);
    }

    .mock-msg {
      max-width: 80%;
      padding: 10px 14px;
      border-radius: 14px;
      font-size: 0.88rem;
      line-height: 1.45;
    }
    .mock-msg-incoming {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
      align-self: flex-start;
      border-bottom-right-radius: 2px;
      color: #fff;
    }
    .mock-msg-outgoing {
      background: linear-gradient(135deg, #d4af37, #aa820a);
      color: #000;
      font-weight: 600;
      align-self: flex-end;
      border-bottom-left-radius: 2px;
    }
    .mock-time { font-size: 0.72rem; opacity: 0.75; margin-top: 3px; display: block; }
    
    .mock-typing {
      align-self: flex-end;
      background: rgba(59, 130, 246, 0.15);
      border: 1px solid rgba(59, 130, 246, 0.3);
      padding: 8px 14px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      color: #93c5fd;
    }

    /* Pop-in animation */
    .animate-pop-in {
      animation: popIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes popIn {
      0% { opacity: 0; transform: scale(0.9) translateY(10px); }
      100% { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* Stats Section Cards */
    .stat-box {
      background: rgba(15, 23, 42, 0.6);
      backdrop-filter: blur(14px);
      border: 1px solid rgba(212, 175, 55, 0.25);
      border-radius: 16px;
      padding: 22px 18px;
      text-align: center;
      transition: all 0.3s ease;
    }
    .stat-box:hover {
      border-color: #d4af37;
      transform: translateY(-4px);
      box-shadow: 0 10px 25px rgba(212, 175, 55, 0.2);
    }
    .stat-number {
      font-size: 2.3rem;
      font-weight: 900;
      color: #d4af37;
      line-height: 1.1;
      margin-bottom: 6px;
    }

    /* Floating Corner Live Showcase Floater */
    .corner-demo-floater {
      position: fixed;
      bottom: 24px;
      left: 24px;
      z-index: 1040;
      animation: floatBadge 3s ease-in-out infinite alternate;
    }
    @keyframes floatBadge {
      0% { transform: translateY(0px); }
      100% { transform: translateY(-8px); }
    }
  </style>
</head>
<body>

  <!-- Background Particle Mesh Canvas -->
  <canvas id="ambientCanvas"></canvas>

  <div class="main-page-wrapper">

    <!-- 1. شريط التنقل العلوي (Navbar) -->
    <nav class="navbar navbar-expand-lg navbar-rodood sticky-top">
      <div class="container">
        
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center me-3" href="{{ url('/index') }}">
          <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" class="nav-logo-img">
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarRodood">
          <i class="bi bi-list fs-2 text-gold"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarRodood">
          <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold align-items-center">
            <li class="nav-item"><a class="nav-link active" href="{{ url('/index') }}">الرئيسية</a></li>
            <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/features') }}">المميزات</a></li>
            <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/pricing') }}">التسعيرة</a></li>
            <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/blog') }}">المدونة</a></li>
            
            <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/how-it-works') }}">دليل التشغيل</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white-50" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">
                أقسام المنصة
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 mt-2">
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

          <!-- Action Buttons -->
          <div class="d-flex align-items-center gap-2">
            <!-- 🔴 Live Demo Showcase Button -->
            <a href="{{ url('/demo') }}" class="btn btn-live-demo">
              <span class="live-dot-pulse"></span>
              <span>استعراض حي للمنصة</span>
            </a>

            <a href="{{ url('/login') }}" class="btn btn-outline-light rounded-pill px-3 fs-8">تسجيل الدخول</a>
            <a href="{{ url('/register') }}" class="btn btn-gold rounded-pill px-3 fw-bold fs-8 d-flex align-items-center gap-1">
              <i class="bi bi-rocket-takeoff-fill"></i> ابدأ الآن
            </a>
          </div>
        </div>
      </div>
    </nav>

    <!-- 2. قسم الهيدر التفاعلي الرئيسي (Hero Section) -->
    <section class="hero-container">
      <div class="container">
        <div class="row align-items-center g-5">
          
          <!-- الجهة اليمنى: العناوين والشعار المتحرك -->
          <div class="col-lg-6 text-start">
            
            <!-- Floating Badge -->
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(212,175,55,0.12); border: 1px solid rgba(212,175,55,0.35);">
              <i class="bi bi-stars text-gold"></i>
              <span class="text-white fs-8 fw-bold">الجيل الجديد من الذكاء الاصطناعي لخدمة العملاء</span>
            </div>

            <!-- Animated Arabic 'ردود' Typography Element -->
            <div class="arabic-brand-container">
              <span class="arabic-brand-title">ردود</span>
              <span class="badge bg-gold text-dark fs-8 fw-bold rounded-pill px-2 py-1 align-self-start mt-2">AI 2.0</span>
            </div>

            <h1 class="hero-title mb-3 fw-black text-white" style="font-size: 2.6rem; line-height: 1.25;">
              تواصل أذكى، وأتمتة فورية<br>
              <span class="text-gold-gradient">وعملاء في قمة الرضا 24/7</span>
            </h1>

            <p class="hero-subtitle mb-4 text-white-50 fs-6" style="line-height: 1.7;">
              منصة ذكية متكاملة لأتمتة محادثات الواتساب، التيليجرام، والموقع بذكاء اصطناعي فائق يفهم لهجات العملاء، يجيب على الاستفسارات فورياً، ويضمن مضاعفة مبيعاتك دون انقطاع.
            </p>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-3 mb-4">
              <a href="{{ url('/register') }}" class="btn btn-gold px-4 py-3 rounded-pill fw-bold fs-7 d-flex align-items-center gap-2 shadow-lg">
                <span>ابدأ تجربتك المجانية</span>
                <i class="bi bi-arrow-left"></i>
              </a>

              <!-- 🔴 Live Showcase Button -->
              <a href="{{ url('/demo') }}" class="btn btn-live-demo px-4 py-3 fs-7">
                <span class="live-dot-pulse"></span>
                <span>🔴 استعراض حي للوحة التاجر (Live Demo)</span>
              </a>
            </div>

            <!-- Trust Badges -->
            <div class="d-flex align-items-center gap-3 pt-2 text-white-50 fs-8 border-top border-secondary border-opacity-25">
              <span><i class="bi bi-check-circle-fill text-gold me-1"></i> بدون بطاقة ائتمانية</span>
              <span><i class="bi bi-check-circle-fill text-gold me-1"></i> ربط مباشر خلال دقيقتين</span>
              <span><i class="bi bi-check-circle-fill text-gold me-1"></i> متوافق مع سلة وزد</span>
            </div>

          </div>

          <!-- الجهة اليسرى: نافذة المحاكاة الحية (Live Interactive Mockup) -->
          <div class="col-lg-6">
            <div class="hero-mockup-window tilt-effect">
              
              <!-- Mockup Header -->
              <div class="mock-header">
                <div class="mock-window-dots">
                  <span class="mock-dot mock-dot-red"></span>
                  <span class="mock-dot mock-dot-yellow"></span>
                  <span class="mock-dot mock-dot-green"></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge bg-gold text-dark fs-9 fw-bold px-2">متجر لافندر للعطور</span>
                  <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 fs-9">
                    <i class="bi bi-circle-fill me-1 fs-9"></i> البوت متصل وفوري
                  </span>
                </div>
                <i class="bi bi-robot text-gold fs-5"></i>
              </div>

              <!-- Simulated Messages -->
              <div class="mock-chat-body" id="heroMockMessages">
                <!-- Populated dynamically with continuous real-time scenario loop -->
              </div>

              <!-- Mockup Footer CTA -->
              <div class="p-3 border-top border-secondary border-opacity-25 bg-black bg-opacity-40 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-lightning-charge-fill text-gold"></i>
                  <span class="text-white-50 fs-8">معدل الاستجابة الفعلي: <strong>0.2 ثانية</strong></span>
                </div>
                <a href="{{ url('/demo') }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 fs-8">
                  تصفح اللوحة كاملة <i class="bi bi-box-arrow-up-left ms-1"></i>
                </a>
              </div>

            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- 3. شريط الإحصائيات والأرقام المتحركة (Animated Metric Counters) -->
    <section class="stats-section py-5 border-top border-bottom border-secondary border-opacity-25">
      <div class="container">
        <div class="row g-3 justify-content-center">
          
          <div class="col-6 col-md-3">
            <div class="stat-box">
              <div class="stat-number stat-counter" data-target="98.6" data-decimals="1" data-prefix="+" data-suffix="%">+0.0%</div>
              <div class="text-white fw-bold fs-7 mb-1">دقة الإجابات الآلية</div>
              <small class="text-white-50 fs-9">فهم سياقي عميق لقواعد المتجر</small>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="stat-box">
              <div class="stat-number stat-counter" data-target="2.5" data-decimals="1" data-suffix="M+">0.0M+</div>
              <div class="text-white fw-bold fs-7 mb-1">رسالة معالجة شهرياً</div>
              <small class="text-white-50 fs-9">عبر الواتساب وتيليجرام والموقع</small>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="stat-box">
              <div class="stat-number stat-counter" data-target="0.3" data-decimals="1" data-suffix="s">0.0s</div>
              <div class="text-white fw-bold fs-7 mb-1">معدل سرعة الاستجابة</div>
              <small class="text-white-50 fs-9">رد فوري بدون انتظار أي عميل</small>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="stat-box">
              <div class="stat-number text-gold">24/7</div>
              <div class="text-white fw-bold fs-7 mb-1">تواجد واستمرارية</div>
              <small class="text-white-50 fs-9">خدمة عملاء بلا توقف أو إجازات</small>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- 4. قنوات الربط الموحدة المدعومة -->
    <section class="py-5">
      <div class="container text-center">
        <h3 class="fw-bold text-white mb-2">تكامل موحد مع كافة قنواتك المفضلة</h3>
        <p class="text-white-50 fs-7 mb-4">اربط متجرك بضغطة زر وتولّ الرد على عملائك من نافذة موحدة</p>

        <div class="row g-3 justify-content-center">
          <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-25 tilt-effect">
              <i class="bi bi-whatsapp text-success fs-1 mb-2 d-block"></i>
              <h6 class="text-white fw-bold mb-1">WhatsApp Cloud API</h6>
              <small class="text-white-50 fs-8">أتمتة الرسائل ومبيعات الواتساب</small>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-25 tilt-effect">
              <i class="bi bi-telegram text-info fs-1 mb-2 d-block"></i>
              <h6 class="text-white fw-bold mb-1">Telegram Bots</h6>
              <small class="text-white-50 fs-8">بوت تيليجرام تفاعلي فائق السرعة</small>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-25 tilt-effect">
              <i class="bi bi-chat-square-text-fill text-gold fs-1 mb-2 d-block"></i>
              <h6 class="text-white fw-bold mb-1">Web Live Widget</h6>
              <small class="text-white-50 fs-8">ودجت دردشة عائم لموقعك ومتجرك</small>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-25 tilt-effect">
              <i class="bi bi-instagram text-danger fs-1 mb-2 d-block"></i>
              <h6 class="text-white fw-bold mb-1">Instagram Direct</h6>
              <small class="text-white-50 fs-8">الرد على الرسائل والتعليقات</small>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 5. قسم الدعوة للانتقال للعرض الحي (Live Demo CTA Banner) -->
    <section class="py-5 my-4">
      <div class="container">
        <div class="p-5 rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(30, 27, 75, 0.8), rgba(49, 16, 66, 0.9)); border: 1px solid rgba(212, 175, 55, 0.4); box-shadow: 0 20px 50px rgba(0,0,0,0.6);">
          <div class="row align-items-center">
            <div class="col-lg-8 text-start mb-3 mb-lg-0">
              <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5; font-size: 0.8rem; font-weight: 700;">
                <span class="live-dot-pulse"></span> استعراض حي متاح الآن بدون تسجيل
              </div>
              <h2 class="fw-bold text-white mb-2">شاهد كيف تبدو لوحة التاجر الحقيقية أثناء العمل!</h2>
              <p class="text-white-50 mb-0 fs-7">
                تصفح المحادثات، اختبر إيقاف البوت للتدخل البشري، جرب الردود الجاهزة عبر /، واطلع على بطاقة العميل والملاحظات التفاعلية.
              </p>
            </div>
            <div class="col-lg-4 text-lg-end">
              <a href="{{ url('/demo') }}" class="btn btn-live-demo px-4 py-3 fs-7 w-100 justify-content-center">
                <span class="live-dot-pulse"></span>
                <span>🔴 فتح الاستعراض الحي (Live Demo)</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 border-top border-secondary border-opacity-25 text-center text-white-50 fs-8">
      <div class="container d-flex flex-wrap justify-content-between align-items-center">
        <div>
          جميع الحقوق محفوظة © {{ date('Y') }} <strong>منصة ردود (Rudood)</strong> لتقنيات الذكاء الاصطناعي
        </div>
        <div class="d-flex gap-3">
          <a href="{{ url('/features') }}" class="text-white-50 text-decoration-none hover-gold">المميزات</a>
          <a href="{{ url('/pricing') }}" class="text-white-50 text-decoration-none hover-gold">الأسعار</a>
          <a href="{{ url('/demo') }}" class="text-gold text-decoration-none fw-bold">استعراض حي</a>
          <a href="{{ url('/blog') }}" class="text-white-50 text-decoration-none hover-gold">المدونة</a>
        </div>
      </div>
    </footer>

    <!-- Floating Corner Demo Floater Badge -->
    <div class="corner-demo-floater d-none d-md-block">
      <a href="{{ url('/demo') }}" class="btn btn-live-demo shadow-lg" style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(15px); border: 1px solid rgba(239, 68, 68, 0.8);">
        <span class="live-dot-pulse"></span>
        <span>🔴 تجربة لوحة التاجر الحية (Live Demo)</span>
      </a>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/landing-animations.js') }}"></script>
</body>
</html>
