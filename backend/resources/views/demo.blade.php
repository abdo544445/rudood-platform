<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>استعراض حي للوحة تحكم التاجر | منصة ردود</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  @include('layouts.partials.theme')

  <style>
    body { min-height: 100vh; overflow: hidden; background: #0b0f19; font-family: 'Cairo', sans-serif; }
    
    /* Top Live Demo Showcase Banner */
    .demo-top-banner {
      background: linear-gradient(90deg, #1e1b4b, #311042, #1e1b4b);
      border-bottom: 1px solid rgba(212, 175, 55, 0.4);
      padding: 8px 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      z-index: 1000;
    }
    
    .demo-badge-pulse {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(239, 68, 68, 0.2);
      border: 1px solid #ef4444;
      color: #fca5a5;
      padding: 3px 10px;
      border-radius: 50px;
      font-size: 0.78rem;
      font-weight: 700;
      animation: pulseAlert 2s infinite;
    }
    
    @keyframes pulseAlert {
      0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
      50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
    }

    .demo-layout-container {
      height: calc(100vh - 54px);
      display: flex;
      flex-direction: column;
      padding: 12px 18px;
    }

    .demo-kpi-bar {
      display: flex;
      gap: 12px;
      margin-bottom: 10px;
      flex-wrap: wrap;
    }
    .demo-kpi-card {
      background: rgba(255, 255, 255, 0.04);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(212, 175, 55, 0.2);
      border-radius: 12px;
      padding: 8px 14px;
      display: flex;
      align-items: center;
      gap: 10px;
      flex: 1;
      min-width: 160px;
    }

    .chat-container {
      background: rgba(255,255,255,0.035) !important;
      backdrop-filter: blur(14px);
      border: 1px solid rgba(212,175,55,0.2) !important;
      border-radius: 14px;
      flex: 1;
      display: flex;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    /* 3-Column Layout */
    .chat-sidebar { width: 300px; border-left: 1px solid rgba(212,175,55,0.15); display: flex; flex-direction: column; background: rgba(15,23,42,0.5); flex-shrink: 0; }
    .chat-list { overflow-y: auto; flex: 1; }
    .chat-item { padding: 11px 14px; border-bottom: 1px solid rgba(255,255,255,0.04); cursor: pointer; transition: all 0.2s; text-decoration: none; display: block; }
    .chat-item:hover, .chat-item.active { background: rgba(212,175,55,0.12); border-right: 3px solid #d4af37; }

    .chat-main { flex: 1; display: flex; flex-direction: column; background: rgba(11,15,25,0.65); min-width: 0; }
    .chat-header { padding: 12px 18px; border-bottom: 1px solid rgba(212,175,55,0.15); background: rgba(15,23,42,0.7); }
    .chat-messages { flex: 1; padding: 16px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; }
    
    .message { max-width: 72%; padding: 10px 14px; border-radius: 12px; font-size: 0.9rem; line-height: 1.45; position: relative; }
    .message-incoming { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); align-self: flex-start; border-bottom-right-radius: 2px; color: #fff; }
    .message-outgoing { background: linear-gradient(135deg,#d4af37,#aa820a); color: #000; font-weight: 600; align-self: flex-end; border-bottom-left-radius: 2px; }
    .message-bot { background: rgba(59,130,246,0.18); border: 1px solid rgba(59,130,246,0.35); align-self: flex-end; border-bottom-left-radius: 2px; color: #bfdbfe; }
    .message-time { font-size: 0.72rem; opacity: 0.75; margin-top: 3px; display: block; }

    .chat-input-area { padding: 10px 16px; border-top: 1px solid rgba(212,175,55,0.15); background: rgba(15,23,42,0.9); position: relative; }
    .custom-chat-input { background: rgba(15,23,42,0.95) !important; border: 1px solid rgba(212,175,55,0.3) !important; color: #fff !important; border-radius: 10px; padding: 8px 12px; font-size: 0.88rem; }

    .chat-crm-sidebar { width: 290px; border-right: 1px solid rgba(212,175,55,0.15); background: rgba(15,23,42,0.55); display: flex; flex-direction: column; overflow-y: auto; padding: 14px; flex-shrink: 0; }
    .avatar { width: 36px; height: 36px; border-radius: 50%; background: rgba(212,175,55,0.2); border: 1px solid #d4af37; display: flex; align-items: center; justify-content: center; color: #d4af37; font-weight: bold; font-size: 0.88rem; }
    .avatar-lg { width: 52px; height: 52px; font-size: 1.2rem; }

    .btn-gold { background: linear-gradient(135deg,#d4af37,#aa820a) !important; color: #000 !important; font-weight: 700; border: none; }
    .btn-gold:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(212,175,55,0.35); }

    /* Typing Dots */
    .typing-dots { display: inline-flex; align-items: center; gap: 3px; margin-right: 6px; }
    .typing-dots span { width: 5px; height: 5px; background: #d4af37; border-radius: 50%; animation: blink 1.2s infinite ease-in-out both; }
    .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
    .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
    @keyframes blink { 0%, 80%, 100% { opacity: 0.2; transform: scale(0.8); } 40% { opacity: 1; transform: scale(1.2); } }
  </style>
</head>
<body>

  <!-- 1. Top Persistent Live Demo Showcase Bar -->
  <header class="demo-top-banner">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ url('/index') }}" class="text-gold text-decoration-none fw-bold fs-7 d-flex align-items-center gap-1">
        <i class="bi bi-arrow-right"></i> العودة للرئيسية
      </a>
      <div class="demo-badge-pulse">
        <i class="bi bi-broadcast"></i> استعراض حي تفاعلي (Live Interactive Demo)
      </div>
      <span class="text-white-50 fs-8 d-none d-md-inline">
        تصفح لوحة تحكم المتجر الحقيقية وجرب التفاعل مع المحادثات والذكاء الاصطناعي بلا قيود
      </span>
    </div>

    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-gold bg-opacity-20 text-gold border border-warning border-opacity-25 px-2 py-1 fs-8">
        🏢 متجر لافندر للعطور | باقة Enterprise
      </span>
      <a href="{{ url('/register') }}" class="btn btn-sm btn-gold rounded-pill px-3 py-1 fs-8 d-flex align-items-center gap-1">
        <i class="bi bi-rocket-takeoff-fill"></i> إنشاء حساب حقيقي
      </a>
    </div>
  </header>

  <!-- 2. Main Live Demo Dashboard Canvas -->
  <main class="demo-layout-container">

    <!-- KPI Summary Cards -->
    <div class="demo-kpi-bar">
      <div class="demo-kpi-card">
        <div class="avatar bg-primary bg-opacity-20 text-primary border-primary"><i class="bi bi-chat-dots-fill fs-6"></i></div>
        <div>
          <div class="fs-9 text-white-50">إجمالي المحادثات</div>
          <div class="fs-7 fw-bold text-white">1,482 محادثة</div>
        </div>
      </div>
      <div class="demo-kpi-card">
        <div class="avatar bg-success bg-opacity-20 text-success border-success"><i class="bi bi-robot fs-6"></i></div>
        <div>
          <div class="fs-9 text-white-50">دقة الإجابات الآلية</div>
          <div class="fs-7 fw-bold text-success">98.6% دقة</div>
        </div>
      </div>
      <div class="demo-kpi-card">
        <div class="avatar bg-warning bg-opacity-20 text-warning border-warning"><i class="bi bi-lightning-charge-fill fs-6"></i></div>
        <div>
          <div class="fs-9 text-white-50">معدل سرعة الرد</div>
          <div class="fs-7 fw-bold text-warning">0.3 ثانية</div>
        </div>
      </div>
      <div class="demo-kpi-card">
        <div class="avatar bg-info bg-opacity-20 text-info border-info"><i class="bi bi-people-fill fs-6"></i></div>
        <div>
          <div class="fs-9 text-white-50">العملاء النشطين</div>
          <div class="fs-7 fw-bold text-info">342 عميل</div>
        </div>
      </div>
    </div>

    <!-- 3. 3-Column Split Pane Live Chat -->
    <div class="chat-container">

      <!-- Column 1: Conversations List -->
      <div class="chat-sidebar">
        <div class="p-2 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
          <input type="text" id="demoConvSearch" class="form-control custom-chat-input fs-8" placeholder="🔍 بحث في محادثات المتجر...">
        </div>
        <div class="chat-list" id="demoConvList">
          
          <!-- Conversation 1: WhatsApp -->
          <div class="chat-item active d-flex align-items-center gap-2" onclick="switchDemoChat(0)" id="demoItem0">
            <div class="avatar bg-success bg-opacity-20 text-success border-success">س</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 text-white fs-8 fw-bold">سارة العتيبي</h6>
                <span class="text-white-50 fs-9">الآن</span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <p class="mb-0 text-white-50 fs-9 text-truncate" style="max-width:140px;">هل يتوفر عطر مسك الختام؟</p>
                <span class="badge bg-success bg-opacity-25 text-success fs-9 px-1">واتساب</span>
              </div>
            </div>
          </div>

          <!-- Conversation 2: Telegram -->
          <div class="chat-item d-flex align-items-center gap-2" onclick="switchDemoChat(1)" id="demoItem1">
            <div class="avatar bg-info bg-opacity-20 text-info border-info">ف</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 text-white fs-8 fw-bold">فهد الشمري</h6>
                <span class="text-white-50 fs-9">منذ 5 د</span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <p class="mb-0 text-white-50 fs-9 text-truncate" style="max-width:140px;">أبغى كود الخصم للطلب الجديد</p>
                <span class="badge bg-info bg-opacity-25 text-info fs-9 px-1">تيليجرام</span>
              </div>
            </div>
          </div>

          <!-- Conversation 3: Instagram Direct -->
          <div class="chat-item d-flex align-items-center gap-2" onclick="switchDemoChat(2)" id="demoItem2">
            <div class="avatar bg-danger bg-opacity-20 text-danger border-danger">ر</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 text-white fs-8 fw-bold">ريم الدوسري</h6>
                <span class="text-white-50 fs-9">منذ 15 د</span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <p class="mb-0 text-white-50 fs-9 text-truncate" style="max-width:140px;">استفسار عن بوست العطور الصيفية</p>
                <span class="badge bg-danger bg-opacity-25 text-danger fs-9 px-1">إنستغرام</span>
              </div>
            </div>
          </div>

          <!-- Conversation 4: Web Live Widget -->
          <div class="chat-item d-flex align-items-center gap-2" onclick="switchDemoChat(3)" id="demoItem3">
            <div class="avatar bg-warning bg-opacity-20 text-gold border-warning">ن</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 text-white fs-8 fw-bold">نورة القحطاني</h6>
                <span class="text-white-50 fs-9">منذ 35 د</span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <p class="mb-0 text-white-50 fs-9 text-truncate" style="max-width:140px;">سياسة الاستبدال خلال كم يوم؟</p>
                <span class="badge bg-warning bg-opacity-25 text-gold fs-9 px-1">ودجت المتجر</span>
              </div>
            </div>
          </div>

          <!-- Conversation 5: Urgent Escalation -->
          <div class="chat-item d-flex align-items-center gap-2" onclick="switchDemoChat(4)" id="demoItem4">
            <div class="avatar bg-secondary bg-opacity-20 text-white-50 border-secondary">خ</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 text-white fs-8 fw-bold">خالد الحربي</h6>
                <span class="text-white-50 fs-9">منذ 1 س</span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <p class="mb-0 text-danger fs-9 text-truncate" style="max-width:140px;">تأخرت الشحنة وأطلب استرداد</p>
                <span class="badge bg-danger fs-9 px-1">تصعيد فوري 🚨</span>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Column 2: Center Chat Thread & Interactive Simulation -->
      <div class="chat-main">
        <!-- Header -->
        <div class="chat-header d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <div class="avatar" id="activeAvatar">س</div>
            <div>
              <div class="d-flex align-items-center gap-2">
                <h6 class="mb-0 text-white fw-bold fs-7" id="activeName">سارة العتيبي</h6>
                <span class="badge bg-success bg-opacity-25 text-success fs-9" id="activePlatform">WhatsApp</span>
                <span class="badge bg-success text-white fs-9" id="activeSentiment">😊 إيجابي</span>
              </div>
              <span class="text-white-50 fs-9" id="activePhone">+966 55 123 4567</span>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fs-8 fw-bold" id="demoToggleBotBtn" onclick="toggleDemoBot()">
              <i class="bi bi-pause-circle-fill me-1"></i> إيقاف البوت (تدخل بشري)
            </button>
          </div>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" id="demoChatMessages">
          <!-- Populated by JS -->
        </div>

        <!-- Quick Canned Replies Chips Bar -->
        <div class="px-3 pt-2 pb-1 border-top border-secondary border-opacity-25 d-flex gap-1 overflow-x-auto align-items-center" style="background: rgba(15,23,42,0.75);">
          <span class="text-gold fs-9 fw-bold me-1 flex-shrink-0"><i class="bi bi-lightning-fill"></i> ردود سريعة:</span>
          <button type="button" class="btn btn-sm btn-dark border border-secondary text-white-50 fs-9 py-0 px-2 rounded-pill flex-shrink-0" onclick="insertCanned('/iban')">/iban (الآيبان)</button>
          <button type="button" class="btn btn-sm btn-dark border border-secondary text-white-50 fs-9 py-0 px-2 rounded-pill flex-shrink-0" onclick="insertCanned('/shipping')">/shipping (مدة الشحن)</button>
          <button type="button" class="btn btn-sm btn-dark border border-secondary text-white-50 fs-9 py-0 px-2 rounded-pill flex-shrink-0" onclick="insertCanned('/discount')">/discount (كوبون 15%)</button>
          <button type="button" class="btn btn-sm btn-dark border border-secondary text-white-50 fs-9 py-0 px-2 rounded-pill flex-shrink-0" onclick="insertCanned('/return')">/return (سياسة الإرجاع)</button>
        </div>

        <!-- Input Area -->
        <div class="chat-input-area">
          <form id="demoSendForm" onsubmit="handleDemoSend(event)" class="d-flex gap-2 align-items-center m-0">
            <input type="text" id="demoMessageInput" class="form-control custom-chat-input flex-grow-1"
                   placeholder="اكتب رسالة تجريبية لتجربة رد الذكاء الاصطناعي الفوري..." autocomplete="off">
            <button type="submit" class="btn btn-gold px-3 py-1 rounded-3 d-flex align-items-center gap-1 fs-8">
              <span>إرسال</span>
              <i class="bi bi-send-fill"></i>
            </button>
          </form>
        </div>
      </div>

      <!-- Column 3: Customer Mini CRM Sidebar -->
      <div class="chat-crm-sidebar">
        <div class="text-center pb-3 border-bottom border-secondary border-opacity-25 mb-3">
          <div class="avatar avatar-lg mx-auto mb-2" id="crmAvatar">س</div>
          <h6 class="text-white fw-bold mb-1 fs-7" id="crmName">سارة العتيبي</h6>
          <span class="text-white-50 fs-9 d-block" id="crmPhone">+966 55 123 4567</span>
          <span class="badge bg-secondary bg-opacity-25 text-white-50 fs-9 mt-1">تاريخ التسجيل: 2026-08-10</span>
        </div>

        <!-- Sentiment Meter -->
        <div class="mb-3">
          <label class="form-label text-gold fs-9 mb-1"><i class="bi bi-activity me-1"></i>تحليل المشاعر التلقائي</label>
          <div class="p-2 rounded-3 border border-secondary border-opacity-25 bg-black bg-opacity-40 fs-8" id="crmSentimentBox">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-white-50">حالة المشاعر:</span>
              <span class="fw-bold text-success" id="crmSentimentVal">😊 راضية / إيجابي</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-white-50">درجة التصعيد:</span>
              <span class="badge bg-success fs-9" id="crmEscalationBadge">طبيعية (بدون تصعيد)</span>
            </div>
          </div>
        </div>

        <!-- Tags -->
        <div class="mb-3">
          <label class="form-label text-gold fs-9 mb-1"><i class="bi bi-tags-fill me-1"></i>وسوم العميل</label>
          <div class="d-flex flex-wrap gap-1" id="crmTagsContainer">
            <span class="badge bg-dark border border-secondary text-white fs-9">VIP</span>
            <span class="badge bg-dark border border-secondary text-white fs-9">عطور_شرقية</span>
            <span class="badge bg-dark border border-secondary text-white fs-9">عميل_دائم</span>
          </div>
        </div>

        <!-- Agent Private Notes -->
        <div class="mb-3 flex-grow-1 d-flex flex-column">
          <label class="form-label text-gold fs-9 mb-1"><i class="bi bi-journal-text me-1"></i>ملاحظات الموظفين الخاصة</label>
          <textarea id="crmNotes" class="form-control custom-chat-input fs-8 flex-grow-1" style="min-height:90px;"
                    placeholder="ملاحظات سرية لا تظهر للعميل...">العميلة تفضل العطور برائحة اللافندر والمسك، أرسلنا لها عينة تجريبية مع الطلب السابق.</textarea>
        </div>

        <button type="button" class="btn btn-sm btn-outline-warning w-100 rounded-pill py-1 fs-8" onclick="showSavedAlert()">
          <i class="bi bi-check2-circle me-1"></i> حفظ الملاحظات (تفاعلي)
        </button>
      </div>

    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const demoChats = [
      {
        name: "سارة العتيبي",
        avatar: "س",
        phone: "+966 55 123 4567",
        platform: "WhatsApp",
        sentiment: "positive",
        sentimentLabel: "😊 راضية / إيجابي",
        escalation: false,
        tags: ["VIP", "عطور_شرقية", "عميل_دائم"],
        notes: "العميلة تفضل العطور برائحة اللافندر والمسك، أرسلنا لها عينة تجريبية مع الطلب السابق.",
        messages: [
          { sender: "customer", text: "مساء الخير، هل يتوفر عطر مسك الختام بحجم 100 مل؟", time: "10:14" },
          { sender: "bot", text: "مساء النور والسرور سارة! 🌸 نعم متوفر «مسك الختام» الفاخر بحجم 100 مل بسعر 240 ريال شامل الضريبة، مع توصيل مجاني لباب بيتك.", time: "10:14" },
          { sender: "customer", text: "ممتاز، هل عندكم كود خصم للطلب الحالي؟", time: "10:15" },
          { sender: "bot", text: "يسعدنا جداً! يمكنك استخدام كود الخصم (VIP15) للحصول على خصم فوري 15% عند الدفع.", time: "10:15" }
        ]
      },
      {
        name: "فهد الشمري",
        avatar: "ف",
        phone: "+966 50 987 6543",
        platform: "Telegram",
        sentiment: "neutral",
        sentimentLabel: "😐 محايد / استفسار",
        escalation: false,
        tags: ["مشتري_جديد", "تيليجرام"],
        notes: "استفسر عن طرق الدفع المتاحة وأوقات عمل المعارض في حائل.",
        messages: [
          { sender: "customer", text: "السلام عليكم، كيف أقدر أتتبع شحنتي برقم الطلب #88210؟", time: "09:40" },
          { sender: "bot", text: "وعليكم السلام فهد! 📦 شحنتك رقم #88210 تم تسليمها لسمسا وجاري توصيلها اليوم. رابط التتبع المباشر: https://smsa.com/track/88210", time: "09:40" }
        ]
      },
      {
        name: "ريم الدوسري",
        avatar: "ر",
        phone: "@reem_perfumes (Instagram Direct)",
        platform: "Instagram Direct",
        sentiment: "positive",
        sentimentLabel: "😊 إيجابي / مهتمة بالشراء",
        escalation: false,
        tags: ["إنستغرام_دايركت", "متابعة_تعليق", "عطور_صيفية"],
        notes: "علقت على بوست الإنستغرام بخصوص كولكشن الصيف، تم إرسال البروشور وكود الخصم.",
        messages: [
          { sender: "customer", text: "أهلاً، كتبت لكم تعليق على بوست كولكشن الصيف وأبغى أعرف الأسعار وطريقة الشراء؟", time: "08:00" },
          { sender: "bot", text: "يا أهلاً وسهلاً ريم! 🌸 يسعدنا اهتمامك بكولكشن الصيف. المجموعة مكونة من 3 عطور فاخرة بسعر 350 ريال شامل التوصيل، وكود الخصم الحصري لمتابعي إنستغرام هو (INSTA20).", time: "08:00" }
        ]
      },
      {
        name: "نورة القحطاني",
        avatar: "ن",
        phone: "زائر عبر ودجت المتجر",
        platform: "Web Widget",
        sentiment: "positive",
        sentimentLabel: "😊 إيجابي",
        escalation: false,
        tags: ["عطور_نسائية", "طلب_هدية", "ودجت_الموقع"],
        notes: "طلبت تغليف هدية فاخر مع كرت إهداء خاص.",
        messages: [
          { sender: "customer", text: "مرحبا، هل الاستبدال مجاني إذا ما ناسبني العطر؟", time: "07:30" },
          { sender: "bot", text: "مرحباً نورة! نعم بالتأكيد، نوفر مع كل عطر عينة تجريبية مجانية، إذا لم يناسبك العطر يمكنك استرجاعه واسترداد كامل المبلغ مجاناً خلال 14 يوماً.", time: "07:30" }
        ]
      },
      {
        name: "خالد الحربي",
        avatar: "خ",
        phone: "+966 54 321 0987",
        platform: "WhatsApp (تصعيد)",
        sentiment: "urgent",
        sentimentLabel: "🚨 غاضب / تصعيد فوري",
        escalation: true,
        tags: ["متصعدة", "استرداد_مبلغ", "أولوية_قصوى"],
        notes: "تأخرت شحنته 4 أيام، تم تحويله للإدارة لتعويضه بقسيمة شراء 50 ريال والاعتذار.",
        messages: [
          { sender: "customer", text: "تأخرت الشحنة 4 أيام وإذا ما وصلت اليوم راح اشتكي لوزارة التجارة وأطالب باسترداد فوري لفلوسي!", time: "08:12" },
          { sender: "bot", text: "أهلاً بك أستاذ خالد، نعتذر منك بشدة عن هذا التأخير الخارج عن إرادتنا! 🛑 تم إيقاف الرد الآلي وتصعيد طلبك للإدارة العليا للمتابعة الفورية.", time: "08:12" }
        ]
      }
    ];

    let activeIndex = 0;
    let isBotPaused = false;

    function renderActiveChat() {
      const chat = demoChats[activeIndex];
      document.getElementById('activeName').textContent = chat.name;
      document.getElementById('activeAvatar').textContent = chat.avatar;
      document.getElementById('activePhone').textContent = chat.phone;
      
      const platBadge = document.getElementById('activePlatform');
      platBadge.textContent = chat.platform;
      if (chat.platform.includes('WhatsApp')) {
        platBadge.className = 'badge bg-success bg-opacity-25 text-success fs-9';
      } else if (chat.platform.includes('Telegram')) {
        platBadge.className = 'badge bg-info bg-opacity-25 text-info fs-9';
      } else if (chat.platform.includes('Instagram')) {
        platBadge.className = 'badge bg-danger bg-opacity-25 text-danger fs-9';
      } else {
        platBadge.className = 'badge bg-warning bg-opacity-25 text-gold fs-9';
      }
      
      const sentBadge = document.getElementById('activeSentiment');
      if (chat.sentiment === 'urgent') {
        sentBadge.className = 'badge bg-danger text-white fs-9';
        sentBadge.textContent = '🚨 متصعدة';
      } else if (chat.sentiment === 'positive') {
        sentBadge.className = 'badge bg-success text-white fs-9';
        sentBadge.textContent = '😊 إيجابي';
      } else {
        sentBadge.className = 'badge bg-secondary text-white fs-9';
        sentBadge.textContent = '😐 استفسار';
      }

      // CRM updates
      document.getElementById('crmName').textContent = chat.name;
      document.getElementById('crmAvatar').textContent = chat.avatar;
      document.getElementById('crmPhone').textContent = chat.phone;
      document.getElementById('crmSentimentVal').textContent = chat.sentimentLabel;
      document.getElementById('crmEscalationBadge').textContent = chat.escalation ? 'متصعدة للإدارة 🔥' : 'طبيعية (بدون تصعيد)';
      document.getElementById('crmEscalationBadge').className = chat.escalation ? 'badge bg-danger fs-9' : 'badge bg-success fs-9';
      document.getElementById('crmNotes').value = chat.notes;

      // Tags
      const tagsContainer = document.getElementById('crmTagsContainer');
      tagsContainer.innerHTML = chat.tags.map(t => `<span class="badge bg-dark border border-secondary text-white fs-9">${t}</span>`).join('');

      // Messages
      const msgArea = document.getElementById('demoChatMessages');
      msgArea.innerHTML = chat.messages.map(m => `
        <div class="message ${m.sender === 'customer' ? 'message-incoming' : (m.sender === 'bot' ? 'message-bot' : 'message-outgoing')}">
          ${m.text}
          <span class="message-time ${m.sender === 'agent' ? 'text-dark' : 'text-white-50'}">
            ${m.time} ${m.sender === 'bot' ? '— رد آلي بالذكاء الاصطناعي 🤖' : (m.sender === 'agent' ? '— أنت (الموظف) ✓' : '')}
          </span>
        </div>
      `).join('');

      msgArea.scrollTop = msgArea.scrollHeight;
    }

    function switchDemoChat(index) {
      activeIndex = index;
      document.querySelectorAll('.chat-item').forEach((el, idx) => {
        el.classList.toggle('active', idx === index);
      });
      renderActiveChat();
    }

    function toggleDemoBot() {
      isBotPaused = !isBotPaused;
      const btn = document.getElementById('demoToggleBotBtn');
      if (isBotPaused) {
        btn.className = 'btn btn-sm btn-outline-success rounded-pill px-3 fs-8 fw-bold';
        btn.innerHTML = '<i class="bi bi-play-circle-fill me-1"></i> استئناف ردود البوت';
      } else {
        btn.className = 'btn btn-sm btn-outline-danger rounded-pill px-3 fs-8 fw-bold';
        btn.innerHTML = '<i class="bi bi-pause-circle-fill me-1"></i> إيقاف البوت (تدخل بشري)';
      }
    }

    function insertCanned(shortcut) {
      const input = document.getElementById('demoMessageInput');
      const cannedMap = {
        '/iban': 'رقم حسابنا الآيبان في مصرف الراجحي: SA4480000123456789012345 باسم مؤسسة لافندر للعطور.',
        '/shipping': 'الشحن سريع وفوري: داخل الرياض خلال 24 ساعة، وباقي مناطق المملكة من 2 إلى 3 أيام عمل عبر سمسا وأرامكس.',
        '/discount': 'يسرنا تقديم كود خصم خاص (WELCOME15) يمنحك خصم 15% على إجمالي سلتك اليوم!',
        '/return': 'الاسترجاع والاستبدال مجاني تماماً خلال 14 يوماً من الشراء عبر بوليصة إرجاع مجانية.'
      };
      input.value = cannedMap[shortcut] || shortcut;
      input.focus();
    }

    function handleDemoSend(e) {
      e.preventDefault();
      const input = document.getElementById('demoMessageInput');
      const text = input.value.trim();
      if (!text) return;

      const chat = demoChats[activeIndex];
      const nowTime = new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });

      // Append Agent/Customer message
      chat.messages.push({ sender: 'agent', text: text, time: nowTime });
      input.value = '';
      renderActiveChat();

      // If bot is not paused, simulate customer reply & instant bot answer
      if (!isBotPaused) {
        setTimeout(() => {
          chat.messages.push({
            sender: 'bot',
            text: '🤖 [ذكاء اصطناعي]: شكراً لتواصلك! قمنا بمعالجة استفسارك وسيقوم النظام بأتمتة الطلب فوراً.',
            time: new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })
          });
          renderActiveChat();
        }, 800);
      }
    }

    function showSavedAlert() {
      alert('✓ تم حفظ الملاحظات والوسوم بنجاح في العرض الحي!');
    }

    // Initial render
    renderActiveChat();
  </script>
</body>
</html>
