 <?php @include('layout/header.php'); ?>


  <style>
    body {
      background-color: #0b0f19 !important;
      color: #ffffff !important;
      font-family: 'Cairo', sans-serif;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* الشريط الجانبي الرئيسي */
    .sidebar {
      width: 260px;
      background: rgba(15, 23, 42, 0.95) !important;
      backdrop-filter: blur(16px);
      border-left: 1px solid rgba(212, 175, 55, 0.2);
      min-height: 100vh;
      position: fixed;
      top: 0;
      right: 0;
      z-index: 1000;
    }

    .sidebar .nav-link {
      color: rgba(255, 255, 255, 0.7) !important;
      padding: 12px 18px;
      border-radius: 10px;
      margin: 4px 10px;
      transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover, .sidebar .nav-link.active {
      color: #000000 !important;
      background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%) !important;
      font-weight: bold;
    }

    /* المحتوى الرئيسي */
    .main-content {
      margin-right: 260px;
      padding: 20px 30px;
      height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* حاوية المحادثات الزجاجية */
    .chat-container {
      background: rgba(255, 255, 255, 0.03) !important;
      backdrop-filter: blur(12px);
      border: 1px solid rgba(212, 175, 55, 0.2) !important;
      border-radius: 16px;
      flex: 1;
      display: flex;
      overflow: hidden;
      margin-bottom: 10px;
    }

    /* قائمة الدردشات الجانبية */
    .chat-sidebar {
      width: 320px;
      border-left: 1px solid rgba(212, 175, 55, 0.15);
      display: flex;
      flex-direction: column;
      background: rgba(15, 23, 42, 0.4);
    }

    .chat-list {
      overflow-y: auto;
      flex: 1;
    }

    .chat-item {
      padding: 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .chat-item:hover, .chat-item.active {
      background: rgba(212, 175, 55, 0.1);
      border-right: 4px solid #d4af37;
    }

    /* منطقة الرسائل والمحادثة */
    .chat-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: rgba(11, 15, 25, 0.5);
    }

    .chat-header {
      padding: 15px 20px;
      border-bottom: 1px solid rgba(212, 175, 55, 0.15);
      background: rgba(15, 23, 42, 0.6);
    }

    .chat-messages {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    /* فقاعات الرسائل */
    .message {
      max-width: 65%;
      padding: 12px 16px;
      border-radius: 14px;
      font-size: 0.95rem;
      line-height: 1.5;
    }

    .message-incoming {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
      align-self: flex-start;
      border-bottom-right-radius: 2px;
    }

    .message-outgoing {
      background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%);
      color: #000000;
      font-weight: 600;
      align-self: flex-end;
      border-bottom-left-radius: 2px;
    }

    .message-time {
      font-size: 0.75rem;
      opacity: 0.7;
      margin-top: 4px;
      display: block;
    }

    /* شريط إدخال الرسالة */
    .chat-input-area {
      padding: 15px 20px;
      border-top: 1px solid rgba(212, 175, 55, 0.15);
      background: rgba(15, 23, 42, 0.8);
    }

    .custom-chat-input {
      background: rgba(15, 23, 42, 0.9) !important;
      border: 1px solid rgba(212, 175, 55, 0.3) !important;
      color: #ffffff !important;
      border-radius: 12px;
      padding: 10px 15px;
    }

    .custom-chat-input:focus {
      border-color: #d4af37 !important;
      box-shadow: 0 0 10px rgba(212, 175, 55, 0.2) !important;
    }

    .btn-gold {
      background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%) !important;
      color: #000000 !important;
      border: none;
      font-weight: bold;
    }

    .avatar {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: rgba(212, 175, 55, 0.2);
      border: 1px solid #d4af37;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #d4af37;
      font-weight: bold;
    }

    .badge-channel {
      font-size: 0.7rem;
      padding: 2px 8px;
      border-radius: 50px;
    }
  </style>

  <!-- المحتوى الرئيسي -->
  <main class="main-content">
    
    <!-- العنوان -->
    <div class="mb-3 d-flex justify-content-between align-items-center">
      <div>
        <h4 class="fw-bold text-white mb-0"><i class="bi bi-chat-dots-fill text-gold me-2"></i>المحادثات المباشرة</h4>
        <p class="text-white-50 mb-0 fs-7">متابعة واستلام استفسارات العملاء القادمة من مختلف القنوات في مكان واحد</p>
      </div>
      <span class="badge bg-success bg-opacity-25 text-success border border-success px-3 py-2 rounded-pill fs-7">
        <i class="bi bi-circle-fill me-1 fs-8"></i> البوت متصل ويفحص المحادثات
      </span>
    </div>

    <!-- حاوية المحادثات المباشرة -->
    <div class="chat-container">

      <!-- القائمة الجانبية للمحادثات -->
      <div class="chat-sidebar">
        <div class="p-3 border-bottom border-secondary border-opacity-25">
          <div class="input-group">
            <input type="text" class="form-control custom-chat-input fs-7" placeholder="بحث في المحادثات...">
          </div>
        </div>

        <div class="chat-list">
          <!-- محادثة 1 (نشطة) -->
          <div class="chat-item active d-flex align-items-center gap-3">
            <div class="avatar">أع</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 text-white fs-7 fw-bold text-truncate">أحمد علي</h6>
                <span class="text-white-50 fs-8">10:42 ص</span>
              </div>
              <p class="mb-0 text-white-50 fs-8 text-truncate">ما هي أوقات التوصيل لديكم لمدينة الرياض؟</p>
            </div>
          </div>

          <!-- محادثة 2 -->
          <div class="chat-item d-flex align-items-center gap-3">
            <div class="avatar">سـ</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 text-white fs-7 fw-bold text-truncate">سارة محمد</h6>
                <span class="text-white-50 fs-8">أمس</span>
              </div>
              <p class="mb-0 text-white-50 fs-8 text-truncate">شكراً لكم تم استلام الطلب بنجاح 👍</p>
            </div>
          </div>

          <!-- محادثة 3 -->
          <div class="chat-item d-flex align-items-center gap-3">
            <div class="avatar">مـ</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 text-white fs-7 fw-bold text-truncate">محمود الخالد</h6>
                <span class="text-white-50 fs-8">29 يوليو</span>
              </div>
              <p class="mb-0 text-white-50 fs-8 text-truncate">هل يتوفر لديكم الدفع عند الاستلام؟</p>
            </div>
          </div>
        </div>
      </div>

      <!-- النافذة الرئيسية للدردشة المختارة -->
      <div class="chat-main">
        <!-- هيدر المحادثة -->
        <div class="chat-header d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-3">
            <div class="avatar">أع</div>
            <div>
              <h6 class="mb-0 text-white fw-bold">أحمد علي</h6>
              <span class="badge bg-success badge-channel mt-1">واتساب WhatsApp</span>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-warning rounded-pill px-3 fs-7">
              <i class="bi bi-person-fill-gear me-1"></i> تحويل موظف (تدخل بشري)
            </button>
            <button class="btn btn-sm btn-outline-danger rounded-circle">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>

        <!-- منطقة إظهار الرسائل -->
        <div class="chat-messages">
          <!-- رسالة العميل -->
          <div class="message message-incoming">
            السلام عليكم، مرحباً بك. ما هي أوقات التوصيل المتاحة لديكم لمدينة الرياض؟
            <span class="message-time text-white-50">10:41 ص</span>
          </div>

          <!-- رد البوت التلقائي -->
          <div class="message message-outgoing">
            وعليكم السلام ورحمة الله! أهلاً بك أ. أحمد 🌺
            التوصيل داخل مدينة الرياض يستغرق من 24 إلى 48 ساعة كحد أقصى. هل تود أن أساعدك في إتمام أي طلب؟
            <span class="message-time text-dark">10:42 ص (رد تلقائي بواسطة الذكاء الاصطناعي)</span>
          </div>
        </div>

        <!-- مربع إرسال الرسائل -->
        <div class="chat-input-area">
          <form class="d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-outline-secondary text-white border-0">
              <i class="bi bi-paperclip fs-5"></i>
            </button>
            <input type="text" class="form-control custom-chat-input" placeholder="اكتب ردك المباشر هنا...">
            <button type="submit" class="btn btn-gold px-4 rounded-3 d-flex align-items-center gap-2">
              <span>إرسال</span>
              <i class="bi bi-send-fill"></i>
            </button>
          </form>
        </div>

      </div>

    </div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
 <?php @include('layout/footer.php'); ?>


