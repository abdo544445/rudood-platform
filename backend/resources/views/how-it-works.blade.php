<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>منصة ردود - كيف يعمل البوت لمتجرك | دليل البدء والتشغيل بعد الاشتراك</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  @include('layouts.partials.theme')
  <style>
    .step-card {
      background: linear-gradient(145deg, rgba(21, 26, 48, 0.8) 0%, rgba(13, 17, 33, 0.95) 100%);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 20px;
      padding: 2.2rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    .step-card:hover {
      transform: translateY(-6px);
      border-color: rgba(212, 175, 55, 0.4);
      box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.5), 0 0 20px rgba(212, 175, 55, 0.15);
    }
    .step-number {
      width: 56px;
      height: 56px;
      border-radius: 16px;
      background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%);
      color: #070a12;
      font-size: 1.5rem;
      font-weight: 900;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
      margin-bottom: 1.5rem;
    }
    .step-icon-badge {
      position: absolute;
      top: 1.5rem;
      left: 1.5rem;
      font-size: 2.5rem;
      color: rgba(255, 255, 255, 0.05);
      transition: color 0.3s ease;
    }
    .step-card:hover .step-icon-badge {
      color: rgba(212, 175, 55, 0.2);
    }
    .feature-tag {
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
      padding: 0.35rem 0.85rem;
      border-radius: 50px;
      font-size: 0.8rem;
      color: #cbd5e1;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
    }
    .timeline-connector {
      position: relative;
    }
    .timeline-connector::after {
      content: '';
      position: absolute;
      top: 28px;
      right: calc(50% + 28px);
      width: calc(100% - 56px);
      height: 2px;
      background: linear-gradient(90deg, #d4af37, rgba(212, 175, 55, 0.2));
      z-index: 0;
    }
    @media (max-width: 991px) {
      .timeline-connector::after { display: none; }
    }
  </style>
</head>
<body style="background-color: #070a12; color: #f8fafc; font-family: 'Cairo', sans-serif;">

  <!-- Navbar -->
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
          <li class="nav-item"><a class="nav-link active text-gold" href="{{ url('/how-it-works') }}">كيف يعمل البوت</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/pricing') }}">التسعيرة</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/blog') }}">المدونة</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white-50" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">أقسام المنصة</a>
            <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 mt-2" aria-labelledby="servicesDropdown">
              <li><a class="dropdown-item py-2" href="{{ url('/auto') }}"><i class="bi bi-robot me-2 text-gold"></i>الرد الآلي (استعراض حي)</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/chat') }}"><i class="bi bi-chat-dots me-2 text-gold"></i>المحادثات (استعراض حي)</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/ai') }}"><i class="bi bi-cpu me-2 text-gold"></i>الذكاء الاصطناعي (استعراض حي)</a></li>
              <li><hr class="dropdown-divider border-secondary opacity-25"></li>
              <li><a class="dropdown-item py-2 text-danger fw-bold" href="{{ url('/demo') }}"><i class="bi bi-broadcast me-2 text-danger"></i>استعراض حي شامل</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/contact') }}">تواصل معنا</a></li>
        </ul>
        <div class="d-flex align-items-center gap-3">
          <a href="{{ url('/login') }}" class="btn btn-outline-light rounded-pill px-4 btn-sm fw-bold">تسجيل الدخول</a>
          <a href="{{ url('/pricing') }}" class="btn btn-gold rounded-pill px-4 btn-sm fw-bold">طلب اشتراك جديد</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Header -->
  <section class="py-5 text-center position-relative overflow-hidden" style="background: radial-gradient(circle at center top, rgba(212, 175, 55, 0.12) 0%, transparent 70%);">
    <div class="container py-4">
      <span class="badge px-3 py-2 rounded-pill mb-3" style="background: rgba(212, 175, 55, 0.15); color: #d4af37; border: 1px solid rgba(212, 175, 55, 0.3);">
        <i class="bi bi-magic me-1"></i> دليل البدء والتشغيل الفوري لمتجرك
      </span>
      <h1 class="display-5 fw-black text-white mb-3">كيف يعمل البوت الذكي في متجرك أو شركتك؟</h1>
      <p class="lead text-white-50 mx-auto" style="max-width: 780px;">
        خطوات مدروسة وبسيطة لنقل خدمة العملاء والمبيعات في متجرك إلى الجيل القادم من الذكاء الاصطناعي. بعد اشتراكك واعتماد حسابك، إليك كيف يتم إعداد وتشغيل البوت ليعمل بدقة واحترافية كأفضل موظف مبيعات لديك.
      </p>
    </div>
  </section>

  <!-- Steps Section -->
  <section class="py-5">
    <div class="container">
      <div class="row g-4">
        
        <!-- Step 1 -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="step-card h-100">
            <i class="bi bi-diagram-3 step-icon-badge"></i>
            <div class="step-number">01</div>
            <h4 class="fw-bold text-white mb-2">ربط قنوات التواصل</h4>
            <p class="text-white-50 fs-8 mb-4">
              اربط حسابك التجاري في واتساب (Cloud API)، تيليجرام، إنستغرام، أو ودجت المحادثة الحية في موقعك بضغطة زر واحدة.
            </p>
            <div class="d-flex flex-wrap gap-2">
              <span class="feature-tag"><i class="bi bi-whatsapp text-success"></i> WhatsApp API</span>
              <span class="feature-tag"><i class="bi bi-instagram text-danger"></i> Instagram</span>
              <span class="feature-tag"><i class="bi bi-telegram text-info"></i> Telegram</span>
            </div>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="step-card h-100">
            <i class="bi bi-database-check step-icon-badge"></i>
            <div class="step-number">02</div>
            <h4 class="fw-bold text-white mb-2">تزويد البوت بالمعرفة</h4>
            <p class="text-white-50 fs-8 mb-4">
              ارفع ملفات متجرك (PDF، قوائم المنتجات، سياسات الاسترجاع، الشحن، وطرق الدفع) ليقوم محرك RAG الدلالي باستيعابها فورياً.
            </p>
            <div class="d-flex flex-wrap gap-2">
              <span class="feature-tag"><i class="bi bi-file-earmark-pdf text-danger"></i> رفع ملفات PDF</span>
              <span class="feature-tag"><i class="bi bi-lightning-charge text-warning"></i> قواعد ردود تلقائية</span>
            </div>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="step-card h-100">
            <i class="bi bi-sliders2 step-icon-badge"></i>
            <div class="step-number">03</div>
            <h4 class="fw-bold text-white mb-2">تخصيص النبرة والتعليمات</h4>
            <p class="text-white-50 fs-8 mb-4">
              اختر أسلوب حوار البوت (ودي، رسمي، تسويقي مرح)، وحدد التعليمات الموجهة له وجرب تفاعله المباشر في مختبر الـ Playground.
            </p>
            <div class="d-flex flex-wrap gap-2">
              <span class="feature-tag"><i class="bi bi-chat-heart text-pink"></i> نبرة ودية احترافية</span>
              <span class="feature-tag"><i class="bi bi-play-circle text-gold"></i> اختبار في المختبر</span>
            </div>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="step-card h-100">
            <i class="bi bi-graph-up-arrow step-icon-badge"></i>
            <div class="step-number">04</div>
            <h4 class="fw-bold text-white mb-2">انطلاق الردود ومضاعفة المبيعات</h4>
            <p class="text-white-50 fs-8 mb-4">
              يبدأ البوت بالرد على آلاف الاستفسارات في أقل من ثانية، وإرسال الكتالوج التفاعلي، وتتبع الطلبات، مع إمكانية تدخل الموظف البشري.
            </p>
            <div class="d-flex flex-wrap gap-2">
              <span class="feature-tag"><i class="bi bi-speedometer2 text-info"></i> سرعة 0.8 ثانية</span>
              <span class="feature-tag"><i class="bi bi-currency-dollar text-success"></i> تتبع عائد المبيعات</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Interactive Workflow Details -->
  <section class="py-5" style="background: rgba(13, 17, 33, 0.6);">
    <div class="container py-4">
      <div class="row align-items-center g-5">
        <div class="col-12 col-lg-6">
          <span class="text-gold fw-bold fs-7 mb-2 d-block"><i class="bi bi-shield-check me-1"></i> تجربة عمل متكاملة بعد الاشتراك</span>
          <h2 class="display-6 fw-bold text-white mb-4">كيف تستفيد من البوت بعد تفعيل حسابك؟</h2>
          
          <div class="d-flex gap-3 mb-4">
            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(212, 175, 55, 0.15); color: #d4af37; flex-shrink: 0;">
              <i class="bi bi-chat-left-dots fs-5"></i>
            </div>
            <div>
              <h5 class="fw-bold text-white mb-1">الرد التلقائي اللحظي على استفسارات الأسعار والمخزون</h5>
              <p class="text-white-50 fs-8 mb-0">يتعرف البوت على أسئلة العميل ويفهم اللهجات المختلفة، ويقدم إجابات دقيقة من واقع مستنداتك دون تأخير.</p>
            </div>
          </div>

          <div class="d-flex gap-3 mb-4">
            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(14, 165, 233, 0.15); color: #0ea5e9; flex-shrink: 0;">
              <i class="bi bi-card-checklist fs-5"></i>
            </div>
            <div>
              <h5 class="fw-bold text-white mb-1">إرسال بطاقات المنتجات والقوائم التفاعلية في واتساب</h5>
              <p class="text-white-50 fs-8 mb-0">تحويل العميل من سائل إلى مشترٍ عبر إرسال كروت المنتجات الجذابة وأزرار الطلب السريع داخل محادثة واتساب.</p>
            </div>
          </div>

          <div class="d-flex gap-3 mb-4">
            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.15); color: #10b981; flex-shrink: 0;">
              <i class="bi bi-box-seam fs-5"></i>
            </div>
            <div>
              <h5 class="fw-bold text-white mb-1">تتبع الشحنات والطلبات التلقائي (Tool Calling)</h5>
              <p class="text-white-50 fs-8 mb-0">عندما يسأل العميل "وين طلبي رقم #10492؟" يقوم البوت بالاستعلام عن حالة الطلب وإعطاء رابط التتبع وموعد التوصيل المتوقع.</p>
            </div>
          </div>

          <div class="d-flex gap-3">
            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.15); color: #ef4444; flex-shrink: 0;">
              <i class="bi bi-person-check fs-5"></i>
            </div>
            <div>
              <h5 class="fw-bold text-white mb-1">التدخل البشري السلس (Human Takeover)</h5>
              <p class="text-white-50 fs-8 mb-0">يمكن لموظفي خدمة العملاء استلام المحادثة في أي وقت وإيقاف البوت مؤقتاً، مع إمكانية استئنافه بنقرة زر.</p>
            </div>
          </div>

        </div>

        <div class="col-12 col-lg-6">
          <div class="card card-custom p-4 border border-secondary border-opacity-30 shadow-lg" style="background: #0f172a;">
            <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-secondary border-opacity-25 mb-3">
              <div class="d-flex align-items-center gap-2">
                <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                <span class="fw-bold text-white fs-8">محاكاة حوار البوت مع العميل</span>
              </div>
              <span class="badge bg-dark border border-secondary border-opacity-50 text-gold fs-9">مساعد متجرك الذكي</span>
            </div>

            <!-- Chat Simulation -->
            <div class="d-flex flex-column gap-3 fs-8">
              <div class="align-self-end bg-primary bg-opacity-25 text-white p-3 rounded-4" style="max-width: 80%; border-bottom-left-radius: 4px;">
                <div class="fw-bold text-info mb-1 fs-9">العميل:</div>
                السلام عليكم، عندكم سماعات لاسلكية عازلة للصوت؟ وهل فيه توصيل سريع؟
              </div>

              <div class="align-self-start bg-dark border border-secondary border-opacity-30 text-white p-3 rounded-4" style="max-width: 85%; border-bottom-right-radius: 4px;">
                <div class="fw-bold text-gold mb-1 fs-9">مساعد المتجر:</div>
                وعليكم السلام ورحمة الله وبركاته! 🎧 نعم بكل سرور، يتوفر لدينا سماعات النخبة اللاسلكية مع عزل ضوضاء فائق بسعر 199 ريال شامل الضريبة وضمان سنتين. والتوصيل سريع خلال 24 ساعة!
                <div class="mt-2 pt-2 border-top border-secondary border-opacity-25 d-flex gap-2">
                  <span class="badge bg-gold text-dark fw-bold">شراء الآن (199 ر.س)</span>
                  <span class="badge bg-dark border border-secondary text-white-50">تتبع طلب سابق</span>
                </div>
              </div>

              <div class="align-self-end bg-primary bg-opacity-25 text-white p-3 rounded-4" style="max-width: 80%; border-bottom-left-radius: 4px;">
                <div class="fw-bold text-info mb-1 fs-9">العميل:</div>
                ممتاز، هل تدعمون الدفع عند الاستلام أو تابي؟
              </div>

              <div class="align-self-start bg-dark border border-secondary border-opacity-30 text-white p-3 rounded-4" style="max-width: 85%; border-bottom-right-radius: 4px;">
                <div class="fw-bold text-gold mb-1 fs-9">مساعد المتجر:</div>
                نوفر جميع وسائل الدفع الآمنة: مدى، فيزا، Apple Pay، بالإضافة لتقسيط تابي وتمارا على 4 دفعات بدون أي فوائد! 💳
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- CTA Box -->
  <section class="py-5 text-center">
    <div class="container py-4">
      <div class="card card-custom p-5 mx-auto border border-warning border-opacity-30 position-relative overflow-hidden" style="max-width: 850px; background: linear-gradient(135deg, rgba(212, 175, 55, 0.08) 0%, rgba(15, 23, 42, 0.95) 100%);">
        <h2 class="display-6 fw-bold text-white mb-3">جاهز لإطلاق مساعد متجرك الذكي؟</h2>
        <p class="text-white-50 mb-4 mx-auto" style="max-width: 600px;">
          قدم طلب اشتراكك الآن، وسيقوم فريق إدارة المنصة بالتواصل معك وتفعيل حسابك وتجهيز البوت لمتجرك خلال وقت قياسي.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <a href="{{ url('/pricing') }}" class="btn btn-gold rounded-pill px-5 py-3 fw-bold fs-7">
            <i class="bi bi-rocket-takeoff me-2"></i> طلب اشتراك وتفعيل المتجر
          </a>
          <a href="{{ url('/demo') }}" class="btn btn-outline-light rounded-pill px-5 py-3 fw-bold fs-7">
            <i class="bi bi-play-circle me-2"></i> تجربة العرض الحي (Demo)
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-4 border-top border-secondary border-opacity-25 text-center text-white-50 fs-8">
    <div class="container">
      <p class="mb-0">جميع الحقوق محفوظة © {{ date('Y') }} منصة ردود (Rudood Platform) لأتمتة خدمة العملاء بالذكاء الاصطناعي.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
