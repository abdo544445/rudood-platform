<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>منصة ردود - إدارة المحادثات والعملاء (عرض تجريبي)</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  <!-- Google Fonts (Cairo) -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">


  <style>
    body {
      height: 100vh;
      display: flex;
      flex-direction: column;
      background-color: #0b0f19;
      color: #ffffff;
      font-family: 'Cairo', sans-serif;
    }

    /* الستايل الزجاجي للحاوية الرئيسية */
    .chat-container {
      flex: 1;
      overflow: hidden;
      background: rgba(255, 255, 255, 0.03);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(212, 175, 55, 0.2);
      border-radius: 20px;
      box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    }

    /* الشريط الجانبي للمحادثات */
    .chat-sidebar {
      border-left: 1px solid rgba(255, 255, 255, 0.1);
      height: 100%;
      display: flex;
      flex-direction: column;
      background: rgba(15, 23, 42, 0.4);
    }

    .chat-user-item {
      cursor: pointer;
      transition: all 0.2s ease;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .chat-user-item:hover {
      background-color: rgba(212, 175, 55, 0.08);
    }

    .chat-user-item.active {
      background-color: rgba(212, 175, 55, 0.15);
      border-right: 3px solid #d4af37;
    }

    /* منطقة المحادثة الرئيسية */
    .chat-main {
      height: 100%;
      display: flex;
      flex-direction: column;
      background: rgba(11, 15, 25, 0.6);
    }

    .chat-messages {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
    }

    /* فقاعات الرسائل */
    .message-bubble {
      max-width: 75%;
      padding: 12px 18px;
      border-radius: 16px;
      margin-bottom: 15px;
      font-size: 0.95rem;
      line-height: 1.6;
    }

    /* رسائل العميل الواردة */
    .message-incoming {
      background: rgba(255, 255, 255, 0.08);
      color: #f8fafc;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-bottom-right-radius: 2px;
      margin-left: auto;
    }

    /* رسائل البوت/المنصة الصادرة */
    .message-outgoing {
      background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%);
      color: #000000;
      font-weight: 600;
      border-bottom-left-radius: 2px;
      margin-right: auto;
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
    }

    /* ملف العميل الجانبي */
    .customer-profile {
      border-right: 1px solid rgba(255, 255, 255, 0.1);
      height: 100%;
      background: rgba(15, 23, 42, 0.4);
      overflow-y: auto;
    }

    .avatar-circle {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: rgba(212, 175, 55, 0.2);
      color: #d4af37;
      border: 1px solid #d4af37;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }

    /* تخصيص الحقول والأزرار Dark-Gold */
    .form-control-dark {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    .form-control-dark:focus {
      background: rgba(255, 255, 255, 0.1);
      border-color: #d4af37;
      color: #fff;
      box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
    }

    .text-gold {
      color: #d4af37 !important;
    }

    .btn-gold {
      background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%);
      color: #000;
      font-weight: 700;
      border: none;
    }

    .btn-gold:hover {
      background: linear-gradient(135deg, #e5be3b 0%, #c4970c 100%);
      color: #000;
    }

    .btn-outline-gold {
      border: 1px solid #d4af37;
      color: #d4af37;
    }

    .btn-outline-gold:hover {
      background-color: #d4af37;
      color: #000;
    }
  </style>
</head>
<body>

  <!-- Navbar الشريط العلوي الزجاجي -->
  <nav class="navbar navbar-expand-lg navbar-dark glass-nav border-bottom border-secondary border-opacity-25 py-2">
    <div class="container-fluid px-4">
      
      <a class="navbar-brand d-flex align-items-center me-3" href="{{ url('/index') }}">
      <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" class="nav-logo-img">
    </a>

      <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarRodood">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarRodood">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link px-3" href="{{ url('/index') }}">الرئيسية</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-3" href="index.html#features">المميزات</a>
          </li>
           <li class="nav-item">
            <a class="nav-link px-3" href="{{ url('/blog') }}">المدونة</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle px-3 active text-gold fw-bold" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              أقسام المنصة
            </a>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border border-secondary border-opacity-25 rounded-3 mt-2">
              <li><a class="dropdown-item py-2" href="{{ url('/auto') }}"><i class="bi bi-robot me-2 text-gold"></i>خدمات الرد الآلي</a></li>
              <li><a class="dropdown-item py-2 active text-gold fw-bold" href="{{ url('/chat') }}"><i class="bi bi-chat-dots me-2 text-gold"></i>إدارة المحادثات (العرض التجريبي)</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/ai') }}"><i class="bi bi-cpu me-2 text-gold"></i>الذكاء الاصطناعي 24/7</a></li>
            </ul>
          </li>
        </ul>
         

        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-gold text-dark px-3 py-2 rounded-pill d-none d-md-inline-block">
            <i class="bi bi-play-circle-fill me-1"></i> وضع العرض التجريبي
          </span>
          <a href="{{ url('/login') }}" class="btn btn-gold px-4 py-2 rounded-pill">ابدأ التجربة المجانية</a>
        </div>
      </div>

    </div>
  </nav>

  <!-- Main Container المحتوى الرئيسي -->
  <div class="container-fluid px-3 px-md-4 py-3 flex-grow-1 overflow-hidden">
    <div class="chat-container row g-0 h-100">
      
      <!-- القائمة الجانبية: قائمة المحادثات -->
      <div class="col-md-4 col-lg-3 chat-sidebar p-0">
        
        <div class="p-3 border-bottom border-secondary border-opacity-25">
          <div class="input-group">
            <span class="input-group-text bg-transparent border-secondary border-opacity-25 text-white-50"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control form-control-dark border-secondary border-opacity-25 ps-0" placeholder="بحث عن عميل أو محادثة...">
          </div>
          <div class="d-flex gap-2 mt-3">
            <button class="btn btn-sm btn-gold rounded-pill px-3">الكل</button>
            <button class="btn btn-sm btn-outline-secondary text-white-50 rounded-pill px-3">غير مقروءة</button>
            <button class="btn btn-sm btn-outline-secondary text-white-50 rounded-pill px-3">البوت الآلي</button>
          </div>
        </div>

        <div class="overflow-y-auto flex-grow-1">
          
          <!-- عميل 1 -->
          <div class="chat-user-item p-3 active d-flex align-items-center gap-3">
            <div class="avatar-circle">أم</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 fw-bold text-white text-truncate fs-7">أحمد محمد</h6>
                <small class="text-gold fs-8">10:42 ص</small>
              </div>
              <p class="mb-0 text-white-50 fs-7 text-truncate">أحتاج لمعرفة سعر الباقة الاحترافية...</p>
            </div>
            <span class="badge bg-gold text-dark rounded-pill">1</span>
          </div>

          <!-- عميل 2 -->
          <div class="chat-user-item p-3 d-flex align-items-center gap-3">
            <div class="avatar-circle border-info text-info bg-info bg-opacity-10">سع</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 fw-bold text-white text-truncate fs-7">سارة علي</h6>
                <small class="text-white-50 fs-8">أمس</small>
              </div>
              <p class="mb-0 text-white-50 fs-7 text-truncate">تمت الإجابة عبر البوت الآلي ✅</p>
            </div>
          </div>

          <!-- عميل 3 -->
          <div class="chat-user-item p-3 d-flex align-items-center gap-3">
            <div class="avatar-circle border-warning text-warning bg-warning bg-opacity-10">شـ</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 fw-bold text-white text-truncate fs-7">شركة التقنية المتقدمة</h6>
                <small class="text-white-50 fs-8">25 يوليو</small>
              </div>
              <p class="mb-0 text-white-50 fs-7 text-truncate">شكراً لكم على الخدمة السريعة</p>
            </div>
          </div>

        </div>
      </div>

      <!-- منطقة الشات الوسطى -->
      <div class="col-md-5 col-lg-6 chat-main p-0">
        
        <!-- الهيدر الخاص بالشات النشط -->
        <div class="p-3 bg-dark bg-opacity-50 border-bottom border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="avatar-circle">أم</div>
            <div>
              <h6 class="mb-0 fw-bold text-white">أحمد محمد</h6>
              <small class="text-success"><i class="bi bi-circle-fill fs-8 me-1"></i> متصل الآن عبر الواتساب</small>
            </div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-gold btn-sm rounded-pill fs-7"><i class="bi bi-headset me-1"></i> تحويل لموظف بشري</button>
            <button class="btn btn-outline-secondary text-white-50 btn-sm rounded-circle"><i class="bi bi-three-dots-vertical"></i></button>
          </div>
        </div>

        <!-- رسائل المحادثة -->
        <div class="chat-messages" id="chatMessages">
          <div class="text-center my-3">
            <span class="badge bg-dark border border-secondary border-opacity-25 text-white-50 px-3 py-1 fs-8">اليوم</span>
          </div>

          <!-- رسالة العميل -->
          <div class="message-bubble message-incoming">
            السلام عليكم، أريد الاستفسار عن تفاصيل الباقة الاحترافية لمنصة ردود وهل تشمل الربط بالواتساب؟
            <div class="text-start mt-1"><small class="text-white-50 fs-8">10:40 ص</small></div>
          </div>

          <!-- رد الذكاء الاصطناعي -->
          <div class="message-bubble message-outgoing">
            وعليكم السلام ورحمة الله! 🌸 نعم، الباقة الاحترافية تشمل الربط الكامل مع الواتساب، والرد الآلي 24/7 للعملاء بالإضافة إلى لوحة تحلیل البيانات.
            <div class="text-end mt-1"><small class="text-dark opacity-75 fs-8">10:41 ص • تم بواسطة ردود AI 🤖</small></div>
          </div>

          <!-- رسالة العميل 2 -->
          <div class="message-bubble message-incoming">
            ممتاز جداً! كيف أستطيع البدء بالتجربة المجانية؟
            <div class="text-start mt-1"><small class="text-white-50 fs-8">10:42 ص</small></div>
          </div>
        </div>

        <!-- حقل إدخال الرسالة -->
        <div class="p-3 bg-dark bg-opacity-50 border-top border-secondary border-opacity-25">
          <form id="chatForm" action="api/send_message.html" method="POST" class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary text-white-50 rounded-circle"><i class="bi bi-paperclip fs-5"></i></button>
            <input type="text" id="messageInput" name="message" class="form-control form-control-dark rounded-pill px-4 py-2 border-secondary border-opacity-25" placeholder="اكتب رسالتك أو ردك الآلي هنا..." required autocomplete="off">
            <button type="submit" class="btn btn-gold rounded-circle p-2 px-3"><i class="bi bi-send-fill"></i></button>
          </form>
        </div>

      </div>

      <!-- العمود الأيسر: تفاصيل وملاحظات العميل -->
      <div class="col-md-3 col-lg-3 customer-profile p-3 d-none d-md-block">
        <div class="text-center pb-3 border-bottom border-secondary border-opacity-25">
          <div class="avatar-circle mx-auto mb-2" style="width:64px; height:64px; font-size:1.5rem;">أم</div>
          <h6 class="fw-bold mb-1 text-white">أحمد محمد</h6>
          <span class="badge bg-gold text-dark fw-bold">عميل مهتم (Lead)</span>
        </div>

        <!-- بيانات التواصل -->
        <div class="py-3 border-bottom border-secondary border-opacity-25">
          <h6 class="fw-bold text-gold fs-7 mb-3">بيانات التواصل</h6>
          <p class="mb-2 text-white-50 fs-7"><i class="bi bi-telephone me-2 text-gold"></i> +968 9123 4567</p>
          <p class="mb-2 text-white-50 fs-7"><i class="bi bi-envelope me-2 text-gold"></i> ahmed@example.com</p>
          <p class="mb-0 text-white-50 fs-7"><i class="bi bi-geo-alt me-2 text-gold"></i> مسقط، عمان</p>
        </div>

        <!-- التصنيفات -->
        <div class="py-3 border-bottom border-secondary border-opacity-25">
          <h6 class="fw-bold text-gold fs-7 mb-3">التصنيفات (Tags)</h6>
          <div class="d-flex flex-wrap gap-1">
            <span class="badge bg-dark text-white-50 border border-secondary border-opacity-25 fs-8">عميل جديد</span>
            <span class="badge bg-dark text-white-50 border border-secondary border-opacity-25 fs-8">مهتم بالواتساب</span>
            <span class="badge bg-dark text-white-50 border border-secondary border-opacity-25 fs-8">استفسار مبيعات</span>
          </div>
        </div>

        <!-- ملاحظات الموظفين -->
        <div class="py-3">
          <h6 class="fw-bold text-gold fs-7 mb-2">ملاحظات الموظفين</h6>
          <form id="notesForm" action="api/save_note.html" method="POST">
            <textarea id="noteText" name="note" class="form-control form-control-dark border-secondary border-opacity-25 fs-7 mb-2" rows="3" placeholder="أضف ملاحظة خاصة بهذا العميل..."></textarea>
            <button type="submit" class="btn btn-sm btn-gold w-100 fs-7 rounded-pill">حفظ الملاحظة</button>
          </form>
        </div>
      </div>

    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
