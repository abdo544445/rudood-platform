<?php
$pageTitle = "الإعدادات والقنوات | منصة ردود";
$currentPage = "settings";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

  <!-- الشريط الجانبي (Sidebar) -->
  <aside class="sidebar d-flex flex-column justify-content-between py-3">
    <div>
      <div class="px-4 mb-4 text-center">
        <a href="index.php">
          <img src="images/img.png" alt="شعار منصة ردود" style="max-height: 45px;">
        </a>
      </div>

      <ul class="nav nav-pills flex-column">
        <li class="nav-item">
          <a href="dash.php" class="nav-link d-flex align-items-center gap-3">
            <i class="bi bi-grid-1x2-fill"></i> الرئيسية
          </a>
        </li>
        <li class="nav-item">
          <a href="ai-manage.php" class="nav-link d-flex align-items-center gap-3">
            <i class="bi bi-cpu-fill"></i> تدريب الذكاء الاصطناعي
          </a>
        </li>
        <li class="nav-item">
          <a href="live-chat.php" class="nav-link d-flex align-items-center gap-3">
            <i class="bi bi-chat-dots-fill"></i> المحادثات المباشرة
          </a>
        </li>
        <li class="nav-item">
          <a href="settings.php" class="nav-link active d-flex align-items-center gap-3">
            <i class="bi bi-gear-fill"></i> الإعدادات والقنوات
          </a>
        </li>
      </ul>
    </div>

    <div class="px-3">
      <a href="login.php" class="btn btn-outline-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
      </a>
    </div>
  </aside>

  <!-- المحتوى الرئيسي -->
  <main class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
      <div>
        <h3 class="fw-bold text-white mb-1"><i class="bi bi-gear text-gold me-2"></i>الإعدادات وتخصيص البوت</h3>
        <p class="text-white-50 mb-0 fs-7">التحكم بسياسات الرد ونبرة المحادثة وربط القنوات</p>
      </div>
    </div>

    <div class="row g-4">
      
      <!-- إعدادات المساعد الذكي -->
      <div class="col-lg-7">
        <div class="glass-card">
          <h4 class="fw-bold text-white mb-4"><i class="bi bi-robot text-gold me-2"></i>تخصيص سلوك البوت</h4>
          
          <form action="api/save_settings.php" method="POST" id="botSettingsForm">
            <div class="mb-3">
              <label for="botName" class="form-label text-white-50 fs-7">اسم البوت (المساعد الذكي)</label>
              <input type="text" id="botName" name="bot_name" class="form-control form-control-dark" value="مساعد ردود الذكي" required>
            </div>

            <div class="mb-3">
              <label for="botTone" class="form-label text-white-50 fs-7">نبرة الحديث والتفاعل</label>
              <select id="botTone" name="bot_tone" class="form-select form-select-dark">
                <option value="formal">احترافية ورسمية</option>
                <option value="friendly" selected>ودودة ومرحبة</option>
                <option value="sales">تسويقية ومحفزة للشراء</option>
              </select>
            </div>

            <div class="mb-4">
              <label for="welcomeMsg" class="form-label text-white-50 fs-7">رسالة الترحيب الآلية الأوليّة</label>
              <textarea id="welcomeMsg" name="welcome_message" class="form-control form-control-dark" rows="3" required>أهلاً بك في متجرنا! 👋 أنا مساعدك الذكي، كيف يمكنني خدمتك اليوم؟</textarea>
            </div>

            <button type="submit" class="btn btn-gold px-4 rounded-pill">حفظ التغييرات</button>
          </form>
        </div>
      </div>

      <!-- ربط قنوات التواصل -->
      <div class="col-lg-5">
        <div class="glass-card">
          <h4 class="fw-bold text-white mb-4"><i class="bi bi-diagram-3 text-gold me-2"></i>ربط القنوات</h4>
          
          <!-- قناة الواتساب -->
          <div class="d-flex align-items-center justify-content-between p-3 mb-3 bg-dark bg-opacity-50 rounded-3 border border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-whatsapp text-success fs-3"></i>
              <div>
                <h6 class="fw-bold text-white mb-0">واتساب للأعمال</h6>
                <small class="text-success fs-8">متصل وجاهز</small>
              </div>
            </div>
            <button class="btn btn-sm btn-outline-light rounded-pill px-3">إعادة الربط</button>
          </div>

          <!-- قناة الانستغرام -->
          <div class="d-flex align-items-center justify-content-between p-3 mb-3 bg-dark bg-opacity-50 rounded-3 border border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-instagram text-danger fs-3"></i>
              <div>
                <h6 class="fw-bold text-white mb-0">إنستغرام</h6>
                <small class="text-white-50 fs-8">غير متصل</small>
              </div>
            </div>
            <button class="btn btn-sm btn-gold rounded-pill px-3">ربط الآن</button>
          </div>

        </div>
      </div>

    </div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

