<?php
$pageTitle = "لوحة التحكم | منصة ردود";
$currentPage = "dash";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

  <!-- الشريط الجانبي -->
  <aside class="sidebar d-flex flex-column justify-content-between py-3">
    <div>
      <div class="px-4 mb-4 text-center">
        <a href="index.php">
          <img src="images/img.png" alt="شعار منصة ردود" style="max-height: 45px;">
        </a>
      </div>

      <ul class="nav nav-pills flex-column">
        <li class="nav-item">
          <a href="dash.php" class="nav-link active d-flex align-items-center gap-3">
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
          <a href="settings.php" class="nav-link d-flex align-items-center gap-3">
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
    
    <!-- الشريط العلوي -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
      <div>
        <h3 class="fw-bold text-white mb-1">أهلاً بك، متجر الأمجاد 👋</h3>
        <p class="text-white-50 mb-0 fs-7">إليك ملخص أداء مساعدك الذكي لهذا اليوم</p>
      </div>
      <div>
        <span class="status-badge">
          <i class="bi bi-circle-fill me-1 fs-9"></i> البوت متصل الآن
        </span>
      </div>
    </div>

    <!-- كروت الإحصائيات -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
          <div class="icon-box-dash"><i class="bi bi-chat-left-text-fill"></i></div>
          <div>
            <span class="text-white-50 d-block fs-7">إجمالي المحادثات</span>
            <h3 class="fw-bold text-white mb-0">1,248</h3>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
          <div class="icon-box-dash"><i class="bi bi-robot"></i></div>
          <div>
            <span class="text-white-50 d-block fs-7">ردود الذكاء الاصطناعي</span>
            <h3 class="fw-bold text-white mb-0">94%</h3>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
          <div class="icon-box-dash"><i class="bi bi-people-fill"></i></div>
          <div>
            <span class="text-white-50 d-block fs-7">العملاء النشطون</span>
            <h3 class="fw-bold text-white mb-0">312</h3>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
          <div class="icon-box-dash"><i class="bi bi-clock-history"></i></div>
          <div>
            <span class="text-white-50 d-block fs-7">متوسط سرعة الرد</span>
            <h3 class="fw-bold text-white mb-0">1.2 ثانية</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- الجدول الداكن المعدل -->
    <div class="mt-5">
      <h4 class="fw-bold text-white mb-3"><i class="bi bi-clock text-gold me-2"></i>آخر المحادثات التفاعلية</h4>
      <div class="rounded-4 overflow-hidden border border-warning border-opacity-25">
        <table class="custom-dark-table">
          <thead>
            <tr>
              <th>اسم العميل</th>
              <th>القناة</th>
              <th>آخر استفسار</th>
              <th>حالة الرد</th>
              <th>التاريخ والوقت</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-bold">محمد أحمد</td>
              <td><i class="bi bi-whatsapp text-success me-1"></i> واتساب</td>
              <td>هل الباقة الاحترافية تدعم الربط مع المتجر؟</td>
              <td><span class="badge-auto">تم الرد آلياً</span></td>
              <td class="text-white-50">قبل 5 دقائق</td>
            </tr>
            <tr>
              <td class="fw-bold">سارة علي</td>
              <td><i class="bi bi-instagram text-danger me-1"></i> إنستغرام</td>
              <td>كم يستغرق وقت تفعيل الخدمة؟</td>
              <td><span class="badge-auto">تم الرد آلياً</span></td>
              <td class="text-white-50">قبل 12 دقيقة</td>
            </tr>
            <tr>
              <td class="fw-bold">شركة الحلول المتقدمة</td>
              <td><i class="bi bi-globe text-info me-1"></i> ودجت الموقع</td>
              <td>أريد التحدث مع موظف دعم بشري</td>
              <td><span class="badge-human">محول للموظف</span></td>
              <td class="text-white-50">قبل 25 دقيقة</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

