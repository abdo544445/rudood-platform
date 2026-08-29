<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>دليل التشغيل - كيف يعمل البوت لمتجرك | منصة ردود</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  <!-- Google Fonts (Cairo) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  @include('layouts.partials.theme')
  <style>
    /* Live Demo Button with Pulsing Radar */
    .btn-live-demo {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid #ef4444;
      color: #fca5a5 !important;
      font-size: 0.85rem;
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
    .hover-faq-btn {
      transition: all 0.25s ease;
    }
    .hover-faq-btn:hover {
      background: rgba(212, 175, 55, 0.12) !important;
      border-color: var(--gold) !important;
      color: #fff !important;
      transform: translateX(-4px);
    }
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
            <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/blog') }}">المدونة</a></li>
            <li class="nav-item"><a class="nav-link active text-gold" href="{{ url('/how-it-works') }}">دليل التشغيل</a></li>
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
            <!-- Live Demo Button -->
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

    <!-- 2. قسم العنوان الرئيسي الموحد (Hero Section) -->
    <section class="py-5 text-center position-relative">
      <div class="container py-4">
        <span class="badge bg-gold-subtle text-gold rounded-pill px-3 py-2 mb-3 border border-warning border-opacity-25 fs-6">
          <i class="bi bi-magic me-1"></i> دليل البدء والتشغيل الفوري لمتجرك
        </span>
        <h1 class="display-4 fw-extrabold mb-3 text-white">
          <span class="text-gold-gradient">كيف يعمل البوت الذكي في متجرك أو شركتك؟</span>
        </h1>
        <p class="lead text-white-50 mx-auto" style="max-width: 780px;">
          خطوات مدروسة وبسيطة لنقل خدمة العملاء والمبيعات في متجرك إلى الجيل القادم من الذكاء الاصطناعي. بعد اشتراكك واعتماد حسابك، إليك كيف يتم إعداد وتشغيل البوت ليعمل بدقة واحترافية كأفضل موظف مبيعات لديك.
        </p>
      </div>
    </section>

    <!-- 3. كروت مراحل التشغيل الأربعة (Steps Section) -->
    <section class="pb-5">
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
                <span class="feature-tag"><i class="bi bi-chat-heart" style="color: #f472b6;"></i> نبرة ودية احترافية</span>
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

    <!-- 4. تفاصيل العمل التفاعلي بعد التفعيل -->
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

    <!-- 5. قسم شات بوت الاستفسارات التفاعلي (Interactive Feature & Guide FAQ Bot) -->
    <section class="py-5 position-relative border-top border-secondary border-opacity-25" id="interactiveBotSection">
      <div class="container py-4">
        
        <!-- Section Heading -->
        <div class="text-center mb-5">
          <span class="badge bg-gold-subtle text-gold rounded-pill px-3 py-2 mb-3 border border-warning border-opacity-25 fs-6">
            <i class="bi bi-chat-square-quote-fill me-1"></i> استفسار فوري وتجربة حية
          </span>
          <h2 class="display-5 fw-extrabold text-white mb-3">
            اسأل <span class="text-gold-gradient">مساعد المنصة التفاعلي</span>
          </h2>
          <p class="text-white-50 lead mx-auto" style="max-width: 680px;">
            جرب الحوار مع البوت مباشرة واطرح أي سؤال حول إمكانيات منصة ردود، سرعة الربط، أتمتة الواتساب، أو خطط الأسعار!
          </p>
        </div>

        <div class="row g-4 align-items-stretch">
          
          <!-- Column 1: Interactive Question Hub & Categories -->
          <div class="col-12 col-lg-5">
            <div class="card card-custom p-4 h-100 border border-secondary border-opacity-30" style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(15px);">
              <div class="d-flex align-items-center gap-2 mb-3 text-gold">
                <i class="bi bi-patch-question-fill fs-5"></i>
                <h5 class="fw-bold text-white mb-0">أسئلة شائعة جاهزة للاختبار</h5>
              </div>
              <p class="text-white-50 fs-8 mb-4">
                اضغط على أي استفسار أدناه ليقوم البوت بالإجابة التوضيحية الفورية مع شرح التفاصيل والمزايا:
              </p>

              <!-- Category 1: Channels & Connections -->
              <div class="mb-3">
                <span class="fs-9 fw-bold text-info text-uppercase d-flex align-items-center gap-1 mb-2">
                  <i class="bi bi-diagram-3"></i> قنوات التواصل والربط
                </span>
                <div class="d-flex flex-column gap-2">
                  <button type="button" class="btn btn-outline-secondary text-start text-white-50 border-secondary border-opacity-40 rounded-3 p-2 fs-8 hover-faq-btn" onclick="sendPresetQuestion('whatsapp_integration')">
                    <i class="bi bi-whatsapp text-success me-2"></i> كيف أربط رقم واتساب التجاري بمتجري؟
                  </button>
                  <button type="button" class="btn btn-outline-secondary text-start text-white-50 border-secondary border-opacity-40 rounded-3 p-2 fs-8 hover-faq-btn" onclick="sendPresetQuestion('official_meta')">
                    <i class="bi bi-shield-check text-info me-2"></i> هل الربط رسمي مع Meta ومعتمد ضد الحظر؟
                  </button>
                </div>
              </div>

              <!-- Category 2: AI Knowledge & RAG -->
              <div class="mb-3">
                <span class="fs-9 fw-bold text-warning text-uppercase d-flex align-items-center gap-1 mb-2">
                  <i class="bi bi-cpu"></i> ذكاء البوت وقواعد المعرفة
                </span>
                <div class="d-flex flex-column gap-2">
                  <button type="button" class="btn btn-outline-secondary text-start text-white-50 border-secondary border-opacity-40 rounded-3 p-2 fs-8 hover-faq-btn" onclick="sendPresetQuestion('pdf_training')">
                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i> كيف يتعلم البوت من ملفات الـ PDF وقوائم المنتجات؟
                  </button>
                  <button type="button" class="btn btn-outline-secondary text-start text-white-50 border-secondary border-opacity-40 rounded-3 p-2 fs-8 hover-faq-btn" onclick="sendPresetQuestion('arabic_dialects')">
                    <i class="bi bi-translate text-gold me-2"></i> هل يفهم البوت اللهجات العامية السعودية والخليجية؟
                  </button>
                </div>
              </div>

              <!-- Category 3: Orders & Human Takeover -->
              <div class="mb-3">
                <span class="fs-9 fw-bold text-success text-uppercase d-flex align-items-center gap-1 mb-2">
                  <i class="bi bi-cart-check"></i> الطلبات والتدخل البشري
                </span>
                <div class="d-flex flex-column gap-2">
                  <button type="button" class="btn btn-outline-secondary text-start text-white-50 border-secondary border-opacity-40 rounded-3 p-2 fs-8 hover-faq-btn" onclick="sendPresetQuestion('order_tracking')">
                    <i class="bi bi-box-seam text-warning me-2"></i> كيف يتتبع البوت شحنات وأرقام طلبات العملاء؟
                  </button>
                  <button type="button" class="btn btn-outline-secondary text-start text-white-50 border-secondary border-opacity-40 rounded-3 p-2 fs-8 hover-faq-btn" onclick="sendPresetQuestion('human_takeover')">
                    <i class="bi bi-person-fill-gear text-pink me-2"></i> كيف يستلم موظف خدمة العملاء المحادثة من البوت؟
                  </button>
                </div>
              </div>

              <!-- Category 4: Pricing & Activation -->
              <div>
                <span class="fs-9 fw-bold text-pink text-uppercase d-flex align-items-center gap-1 mb-2">
                  <i class="bi bi-rocket-takeoff"></i> الاشتراك والتفعيل
                </span>
                <div class="d-flex flex-column gap-2">
                  <button type="button" class="btn btn-outline-secondary text-start text-white-50 border-secondary border-opacity-40 rounded-3 p-2 fs-8 hover-faq-btn" onclick="sendPresetQuestion('activation_time')">
                    <i class="bi bi-lightning-charge text-warning me-2"></i> كم يستغرق تفعيل حسابي بعد تقديم الطلب؟
                  </button>
                </div>
              </div>

            </div>
          </div>

          <!-- Column 2: Live Interactive Chat Dialog Window -->
          <div class="col-12 col-lg-7">
            <div class="card card-custom h-100 border border-secondary border-opacity-30 shadow-2xl d-flex flex-column overflow-hidden" style="background: rgba(11, 15, 25, 0.95);">
              
              <!-- Chat Header -->
              <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex align-items-center justify-content-between" style="background: rgba(15, 23, 42, 0.85);">
                <div class="d-flex align-items-center gap-3">
                  <div class="position-relative">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: linear-gradient(135deg, #d4af37, #aa820a); color: #000; font-size: 1.3rem;">
                      <i class="bi bi-robot"></i>
                    </div>
                    <span class="position-absolute bottom-0 start-0 p-1 bg-success border border-dark rounded-circle" style="width: 12px; height: 12px;"></span>
                  </div>
                  <div>
                    <div class="fw-bold text-white fs-7 d-flex align-items-center gap-2">
                      <span>مساعد منصة ردود الذكي</span>
                      <span class="badge bg-gold text-dark fs-9 fw-bold">AI Assistant</span>
                    </div>
                    <div class="text-success fs-9 d-flex align-items-center gap-1">
                      <span class="spinner-grow spinner-grow-sm" style="width: 8px; height: 8px;" role="status"></span>
                      متصل ومستعد لإجابة كافة استفساراتك
                    </div>
                  </div>
                </div>

                <div>
                  <button type="button" class="btn btn-sm btn-outline-secondary text-white-50 border-0 rounded-circle" title="إعادة تعيين المحادثة" onclick="resetFaqChat()">
                    <i class="bi bi-arrow-clockwise fs-6"></i>
                  </button>
                </div>
              </div>

              <!-- Chat Message Stream -->
              <div class="p-4 flex-grow-1 overflow-auto d-flex flex-column gap-3" id="faqChatMessages" style="max-height: 480px; min-height: 400px;">
                
                <!-- Initial Bot Message -->
                <div class="d-flex align-items-start gap-2">
                  <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(212, 175, 55, 0.2); color: var(--gold);">
                    <i class="bi bi-robot fs-7"></i>
                  </div>
                  <div class="p-3 rounded-4 bg-dark border border-secondary border-opacity-40 text-white fs-8" style="max-width: 85%; border-top-right-radius: 4px;">
                    <p class="mb-2 fw-bold text-gold">مرحباً بك! 👋 أنا مساعد منصة ردود الآلي.</p>
                    <p class="mb-2 text-white-50">أنا هنا لمساعدتك في فهم كل ما يتعلق بالمنصة وطريقة تشغيل البوت لمتجرك أو شركتك.</p>
                    <p class="mb-0 text-white-50">يمكنك النقر على أي من الأسئلة الجاهزة في القائمة، أو كتابة سؤالك مباشرة في المربع أدناه! 👇</p>
                  </div>
                </div>

              </div>

              <!-- Typing Indicator (Hidden by default) -->
              <div class="px-4 py-2 d-none" id="faqTypingIndicator">
                <div class="d-flex align-items-center gap-2 text-white-50 fs-8">
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(212, 175, 55, 0.15); color: var(--gold);">
                    <i class="bi bi-robot fs-8"></i>
                  </div>
                  <span class="badge bg-dark border border-secondary border-opacity-40 text-gold px-3 py-2">
                    <span class="spinner-grow spinner-grow-sm me-1 text-gold" style="width: 6px; height: 6px;"></span>
                    جاري التفكير وصياغة الرد...
                  </span>
                </div>
              </div>

              <!-- Chat Input Bar -->
              <div class="p-3 border-top border-secondary border-opacity-25" style="background: rgba(15, 23, 42, 0.9);">
                <form id="faqChatForm" onsubmit="handleFaqSubmit(event)" class="d-flex gap-2 align-items-center">
                  <input type="text" id="faqChatInput" class="form-control bg-dark border-secondary border-opacity-40 text-white fs-8 rounded-pill px-4 py-2" placeholder="اكتب سؤالك هنا عن المنصة (مثال: كيف أربط واتساب؟ أو ما هي الأسعار؟)..." autocomplete="off">
                  <button type="submit" class="btn btn-gold rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                    <i class="bi bi-send-fill fs-7"></i>
                  </button>
                </form>
              </div>

            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- 6. قسم الدعوة للانضمام (CTA Box) -->
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

    <!-- 7. التذييل (Footer) -->
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

  <!-- Interactive FAQ Bot Script -->
  <script>
    const FAQ_KNOWLEDGE = {
      whatsapp_integration: {
        q: "كيف أربط رقم واتساب التجاري بمتجري؟",
        a: `<div class="mb-2"><strong class="text-success"><i class="bi bi-whatsapp me-1"></i> خطوات ربط واتساب التجاري بضغطة زر:</strong></div>
            <ol class="ps-3 mb-3 text-white-50 lh-lg">
              <li>انتقل إلى تبويب <strong>قنوات التواصل (Channels)</strong> من لوحة التحكم.</li>
              <li>اختر <strong>WhatsApp Cloud API</strong> وسجل الدخول بحساب Meta للأعمال.</li>
              <li>أدخل معرف رقم الهاتف (Phone Number ID) ورمز الوصول الدائم (Access Token).</li>
              <li>فعل خيار <strong>Webhook</strong> لتبدأ منصة ردود باستقبال المحادثات والرد اللحظي فوراً.</li>
            </ol>
            <div class="p-2 rounded bg-dark border border-secondary border-opacity-40 text-gold fs-9">
              <i class="bi bi-info-circle me-1"></i> لا تحتاج لإبقاء هاتفك متصلاً بالإنترنت إطلاقاً، فالربط سحابي يعمل 24/7.
            </div>`
      },
      official_meta: {
        q: "هل الربط رسمي مع Meta ومعتمد ضد الحظر؟",
        a: `<div class="mb-2"><strong class="text-info"><i class="bi bi-shield-check me-1"></i> نعم، الربط رسمي ومعتمد 100%:</strong></div>
            <p class="mb-2 text-white-50">تعتمد منصة ردود على <strong>WhatsApp Business Cloud API الرسمية</strong> الصادرة مباشرة من شركة Meta، مما يضمن:</p>
            <ul class="ps-3 mb-2 text-white-50 lh-lg">
              <li>حماية كاملة لرقم هاتفك من الحظر (مقارنة بالطرق غير الرسمية).</li>
              <li>إمكانية توثيق الرقم بالعلامة الخضراء (Green Badge).</li>
              <li>سرعة فائقة في وصول الرسائل دون أي تأخير.</li>
            </ul>`
      },
      pdf_training: {
        q: "كيف يتعلم البوت من ملفات الـ PDF وقوائم المنتجات؟",
        a: `<div class="mb-2"><strong class="text-warning"><i class="bi bi-file-earmark-pdf me-1"></i> محرك البحث الدلالي (RAG Engine):</strong></div>
            <p class="mb-2 text-white-50">عند رفعك لأي ملف PDF أو Word أو نصي في صفحة <strong>إدارة المعرفة</strong>:</p>
            <ul class="ps-3 mb-2 text-white-50 lh-lg">
              <li>يقوم النظام بتقسيم الملف إلى مقاطع ذكية (Semantic Chunks) وحساب متجهات المعنى (Vector Embeddings).</li>
              <li>عندما يسأل العميل، يبحث البوت في أجزاء من الثانية عن الإجابة الدقيقة من واقع ملفاتك ويصيغها بأسلوب لبق.</li>
              <li>لن يختلق البوت أي معلومة غير موجودة في مستنداتك المعتمدة.</li>
            </ul>`
      },
      arabic_dialects: {
        q: "هل يفهم البوت اللهجات العامية السعودية والخليجية؟",
        a: `<div class="mb-2"><strong class="text-gold"><i class="bi bi-translate me-1"></i> نعم، دعم فائق للهجات العربية:</strong></div>
            <p class="mb-2 text-white-50">تم تدريب نماذج الذكاء الاصطناعي في منصة ردود على فهم مختلف اللهجات المحلية، مثل:</p>
            <div class="d-flex flex-wrap gap-2 mb-2">
              <span class="badge bg-dark border border-secondary text-info">السعودية (نجدي / حجازي / شرقاوي)</span>
              <span class="badge bg-dark border border-secondary text-success">الخليجية والكويتية</span>
              <span class="badge bg-dark border border-secondary text-warning">المصرية والشامية</span>
            </div>
            <p class="mb-0 text-white-50">مهما كانت طريقة كتابة العميل (عامية، اختصارات، أو فصحى)، سيفهم القصد ويلبي طلبه بدقة.</p>`
      },
      order_tracking: {
        q: "كيف يتتبع البوت شحنات وأرقام طلبات العملاء؟",
        a: `<div class="mb-2"><strong class="text-warning"><i class="bi bi-box-seam me-1"></i> استدعاء الأدوات الحية (Tool Calling):</strong></div>
            <p class="mb-2 text-white-50">يتعرف البوت تلقائياً على أنماط أرقام الشحنات والطلبات (مثل: #10492 أو رقم البوليصة):</p>
            <ol class="ps-3 mb-2 text-white-50 lh-lg">
              <li>يستعلم البوت لحظياً من نظام متجرك أو شركة الشحن عن حالة الشحنة.</li>
              <li>يقوم بتزويد العميل بموقع الشحنة الحالي، شركة الشحن، ورابط التتبع المباشر.</li>
              <li>يوفر على فريق الدعم مئات ساعات الاستعلام اليدوي يومياً.</li>
            </ol>`
      },
      human_takeover: {
        q: "كيف يستلم موظف خدمة العملاء المحادثة من البوت؟",
        a: `<div class="mb-2"><strong class="text-pink"><i class="bi bi-person-fill-gear me-1"></i> ميزة التدخل البشري الفوري (Human Takeover):</strong></div>
            <p class="mb-2 text-white-50">في لوحة المحادثات الموحدة <strong>(Live Chat)</strong>:</p>
            <ul class="ps-3 mb-2 text-white-50 lh-lg">
              <li>يستطيع الموظف النقر على زر <strong>إيقاف البوت ⏸️</strong> واستلام المحادثة والتحدث مباشرة مع العميل.</li>
              <li>يتوقف البوت عن الرد في هذه المحادثة فقط، بينما يستمر في خدمة بقية العملاء.</li>
              <li>عند انتهاء الموظف، يمكنه إعادة تفعيل البوت بنقرة واحدة.</li>
            </ul>`
      },
      activation_time: {
        q: "كم يستغرق تفعيل حسابي بعد تقديم الطلب؟",
        a: `<div class="mb-2"><strong class="text-success"><i class="bi bi-lightning-charge me-1"></i> تفعيل فوري وسريع:</strong></div>
            <p class="mb-2 text-white-50">بعد تقديم طلبك في صفحة التسعيرة:</p>
            <ul class="ps-3 mb-2 text-white-50 lh-lg">
              <li>يقوم فريق الدعم بالتحقق من البيانات واعتماد حسابك خلال أقل من <strong>ساعة واحدة</strong>.</li>
              <li>تصلك رسالة الترحيب مع بيانات تسجيل الدخول للوحة التحكم المخصصة لمتجرك.</li>
              <li>يمكنك البدء مباشرة في رفع ملفاتك وربط قنواتك وإطلاق البوت.</li>
            </ul>
            <div class="mt-2">
              <a href="{{ url('/pricing') }}" class="btn btn-gold btn-sm rounded-pill px-4 fw-bold">اختر باقتك وقدم طلبك الآن</a>
            </div>`
      }
    };

    function sendPresetQuestion(key) {
      const item = FAQ_KNOWLEDGE[key];
      if (!item) return;

      appendUserMessage(item.q);
      showTyping(true);

      setTimeout(() => {
        showTyping(false);
        appendBotMessage(item.a);
      }, 700);
    }

    function handleFaqSubmit(e) {
      e.preventDefault();
      const input = document.getElementById('faqChatInput');
      const text = input.value.trim();
      if (!text) return;

      input.value = '';
      appendUserMessage(text);
      showTyping(true);

      setTimeout(() => {
        showTyping(false);
        const reply = matchCustomQuestion(text);
        appendBotMessage(reply);
      }, 850);
    }

    function matchCustomQuestion(query) {
      const q = query.toLowerCase();

      if (q.includes('واتساب') || q.includes('whatsapp') || q.includes('ربط') || q.includes('رقم')) {
        return FAQ_KNOWLEDGE.whatsapp_integration.a;
      }
      if (q.includes('حظر') || q.includes('رسمي') || q.includes('meta') || q.includes('مؤسس')) {
        return FAQ_KNOWLEDGE.official_meta.a;
      }
      if (q.includes('pdf') || q.includes('ملف') || q.includes('معرفة') || q.includes('منتج') || q.includes('يتعلم') || q.includes('تدريب')) {
        return FAQ_KNOWLEDGE.pdf_training.a;
      }
      if (q.includes('لهج') || q.includes('سعودي') || q.includes('خليج') || q.includes('لغ') || q.includes('فهم')) {
        return FAQ_KNOWLEDGE.arabic_dialects.a;
      }
      if (q.includes('شحن') || q.includes('طلب') || q.includes('تتبع') || q.includes('وين طلبي') || q.includes('توصيل')) {
        return FAQ_KNOWLEDGE.order_tracking.a;
      }
      if (q.includes('موظف') || q.includes('بشري') || q.includes('تدخل') || q.includes('استلام') || q.includes('فريق')) {
        return FAQ_KNOWLEDGE.human_takeover.a;
      }
      if (q.includes('سعر') || q.includes('اسعار') || q.includes('باق') || q.includes('تفعيل') || q.includes('اشتراك') || q.includes('كم')) {
        return FAQ_KNOWLEDGE.activation_time.a;
      }

      // Default smart AI fallback reply
      return `<div class="mb-2"><strong class="text-gold"><i class="bi bi-info-circle me-1"></i> إجابة استفسارك:</strong></div>
              <p class="mb-2 text-white-50">شكراً لسؤالك! منصة <strong>ردود (Rudood)</strong> مصممة خصيصاً لأتمتة خدمة العملاء والمبيعات للمتاجر والشركات عبر واتساب وقنوات التواصل، مع دعم كامل للذكاء الاصطناعي، رفع مستندات المتجر، والتدخل البشري السلس.</p>
              <p class="mb-3 text-white-50">يمكنك أيضاً تجربة لوحة التحكم الحية أو مراجعة الأسعار والتفعيل عبر الروابط التالية:</p>
              <div class="d-flex flex-wrap gap-2">
                <a href="{{ url('/demo') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">🔴 استعراض حي للمنصة</a>
                <a href="{{ url('/pricing') }}" class="btn btn-gold btn-sm rounded-pill px-3 fw-bold">خطط الأسعار والتفعيل</a>
              </div>`;
    }

    function appendUserMessage(text) {
      const container = document.getElementById('faqChatMessages');
      const div = document.createElement('div');
      div.className = 'd-flex align-items-start gap-2 justify-content-end';
      div.innerHTML = `
        <div class="p-3 rounded-4 bg-primary bg-opacity-25 border border-primary border-opacity-30 text-white fs-8" style="max-width: 80%; border-top-left-radius: 4px;">
          ${text}
        </div>
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.2); color: #60a5fa;">
          <i class="bi bi-person-fill fs-7"></i>
        </div>
      `;
      container.appendChild(div);
      container.scrollTop = container.scrollHeight;
    }

    function appendBotMessage(html) {
      const container = document.getElementById('faqChatMessages');
      const div = document.createElement('div');
      div.className = 'd-flex align-items-start gap-2';
      div.innerHTML = `
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(212, 175, 55, 0.2); color: var(--gold);">
          <i class="bi bi-robot fs-7"></i>
        </div>
        <div class="p-3 rounded-4 bg-dark border border-secondary border-opacity-40 text-white fs-8 shadow-sm" style="max-width: 85%; border-top-right-radius: 4px;">
          ${html}
        </div>
      `;
      container.appendChild(div);
      container.scrollTop = container.scrollHeight;
    }

    function showTyping(show) {
      const indicator = document.getElementById('faqTypingIndicator');
      if (show) {
        indicator.classList.remove('d-none');
        const container = document.getElementById('faqChatMessages');
        container.scrollTop = container.scrollHeight;
      } else {
        indicator.classList.add('d-none');
      }
    }

    function resetFaqChat() {
      const container = document.getElementById('faqChatMessages');
      container.innerHTML = `
        <div class="d-flex align-items-start gap-2">
          <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(212, 175, 55, 0.2); color: var(--gold);">
            <i class="bi bi-robot fs-7"></i>
          </div>
          <div class="p-3 rounded-4 bg-dark border border-secondary border-opacity-40 text-white fs-8" style="max-width: 85%; border-top-right-radius: 4px;">
            <p class="mb-2 fw-bold text-gold">مرحباً بك! 👋 أنا مساعد منصة ردود الآلي.</p>
            <p class="mb-2 text-white-50">أنا هنا لمساعدتك في فهم كل ما يتعلق بالمنصة وطريقة تشغيل البوت لمتجرك أو شركتك.</p>
            <p class="mb-0 text-white-50">يمكنك النقر على أي من الأسئلة الجاهزة في القائمة، أو كتابة سؤالك مباشرة في المربع أدناه! 👇</p>
          </div>
        </div>
      `;
    }
  </script>
</body>
</html>
