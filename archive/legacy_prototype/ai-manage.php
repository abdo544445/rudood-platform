<?php
$pageTitle = "تدريب الذكاء الاصطناعي | منصة ردود";
$currentPage = "ai-manage";
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
          <a href="dash.php" class="nav-link d-flex align-items-center gap-3">
            <i class="bi bi-grid-1x2-fill"></i> الرئيسية
          </a>
        </li>
        <li class="nav-item">
          <a href="ai-manage.php" class="nav-link active d-flex align-items-center gap-3">
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
    <div class="mb-4 pb-3 border-bottom border-secondary border-opacity-25">
      <h3 class="fw-bold text-white mb-1"><i class="bi bi-cpu-fill text-gold me-2"></i>تدريب الذكاء الاصطناعي</h3>
      <p class="text-white-50 mb-0 fs-7">قم بتزويد مساعدك الذكي ببيانات متجرك ليعرف كيف يجيب عملاءك بدقة</p>
    </div>

    <!-- بطاقتين بجانب بعضهما -->
    <div class="row g-4">

      <!-- القسم الأول: رفع المستندات والكتالوجات -->
      <div class="col-lg-6">
        <div class="stat-card h-100 p-4 d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-file-earmark-arrow-up-fill text-gold fs-4"></i>
              <h5 class="fw-bold text-white mb-0">رفع المستندات والكتالوجات</h5>
            </div>
            <p class="text-white-50 fs-7 mb-4">ارفع ملفات PDF أو Word تحتوي على تفاصيل المنتجات، الأسعار، أو سياسة المتجر.</p>

            <!-- منطقة الرفع -->
            <form id="uploadDocForm" action="api/upload_doc.php" method="POST" enctype="multipart/form-data">
              <div class="upload-zone mb-3 position-relative" style="cursor: pointer;" onclick="document.getElementById('docFileInput').click();">
                <i class="bi bi-cloud-arrow-up-fill text-gold display-5 d-block mb-2"></i>
                <p class="fw-bold text-white mb-1 fs-6">اضغط هنا لرفع الملف أو اسحبه إلى هنا</p>
                <span class="text-white-50 fs-8">يدعم صيغ (PDF, DOCX, TXT) بحد أقصى 15 ميجابايت</span>
                <input type="file" id="docFileInput" name="doc_file" class="d-none" accept=".pdf,.docx,.txt" onchange="document.getElementById('fileNameDisplay').innerText = this.files[0] ? this.files[0].name : '';">
                <div id="fileNameDisplay" class="text-gold fw-bold mt-2 fs-7"></div>
              </div>

              <button type="submit" class="btn btn-gold w-100 py-2.5 mb-4">رفع الملف وتدريب البوت</button>
            </form>
          </div>

          <!-- قائمة الملفات المدربة -->
          <div>
            <h6 class="fw-bold text-white fs-7 mb-2">الملفات المدربة حالياً:</h6>
            <div class="file-item d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                <span class="text-white fs-7">كتالوج_المنتجات_2026.pdf</span>
              </div>
              <span class="file-status-badge">
                <i class="bi bi-check-circle-fill me-1"></i> مكتمل
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- القسم الثاني: إضافة سؤال وجواب مباشر -->
      <div class="col-lg-6">
        <div class="stat-card h-100 p-4">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-question-square-fill text-gold fs-4"></i>
            <h5 class="fw-bold text-white mb-0">إضافة سؤال وجواب مباشر</h5>
          </div>
          <p class="text-white-50 fs-7 mb-4">أدخل الأسئلة المتكررة وإجاباتها النموذجية مباشرة للنظام.</p>

          <form id="faqForm" action="api/add_faq.php" method="POST" class="d-flex flex-column justify-content-between" style="height: calc(100% - 70px);">
            <div>
              <div class="mb-3">
                <label for="faqQuestion" class="form-label text-white fs-7 fw-bold">السؤال المتوقع من العميل</label>
                <input type="text" id="faqQuestion" name="question" class="form-control custom-input" placeholder="مثال: ما هي أوقات التوصيل لديكم؟" required>
              </div>

              <div class="mb-4">
                <label for="faqAnswer" class="form-label text-white fs-7 fw-bold">الإجابة النموذجية للبوت</label>
                <textarea id="faqAnswer" name="answer" class="form-control custom-input" rows="5" placeholder="اكتب الإجابة الدقيقة التي سيقوم البوت بإرسالها للعميل..." required></textarea>
              </div>
            </div>

            <button type="submit" class="btn btn-gold w-100 py-2.5">
              <i class="bi bi-plus-circle me-1"></i> حفظ السؤال وتحديث قاعدة المعرفة
            </button>
          </form>
        </div>
      </div>

    </div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

