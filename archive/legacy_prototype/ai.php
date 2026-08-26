<?php
$pageTitle = "الذكاء الاصطناعي 24/7 - منصة ردود";
$currentPage = "ai";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

  <!-- Hero Section -->
  <section class="py-5 text-center position-relative">
    <div class="container py-5">
      <span class="badge bg-gold-subtle text-gold border border-gold border-opacity-25 px-3 py-2 rounded-pill mb-3 fw-bold">
        <i class="bi bi-cpu me-1"></i> تقنية فهم اللغة الطبيعية
      </span>
      <h1 class="display-4 fw-extrabold text-white mb-3">ذكاء اصطناعي يتحدث بلغة عملائك 24/7</h1>
      <p class="lead text-white-50 mx-auto mb-4" style="max-width: 750px;">
        درب مساعدك الذكي عبر رفع ملفات المنتجات والكتالوجات. يفهم الاستفسارات المعقدة باللهجة المحلية ويقدم إجابات دقيقة دون تدخل بشري.
      </p>
      <div class="d-flex justify-content-center gap-3">
        <a href="chat.php" class="btn btn-gold text-dark fw-bold rounded-pill px-4 py-3">تجربة المساعد الذكي</a>
        <a href="login.php" class="btn btn-outline-light rounded-pill px-4 py-3">درب بوتك الآن</a>
      </div>
    </div>
  </section>

  <!-- بطاقات المميزات المميزة -->
  <section class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold text-gold">قدرات الذكاء الاصطناعي في "ردود"</h2>
        <p class="text-white-50">حلول متقدمة لجعل خدمة العملاء أكثر كفاءة وسرعة</p>
      </div>

      <div class="row g-4">
        <!-- بطاقة 1 -->
        <div class="col-md-6 col-lg-3">
          <div class="chat-sim-card p-4 h-100 text-center">
            <div class="text-gold display-5 mb-3"><i class="bi bi-translate"></i></div>
            <h5 class="fw-bold text-white mb-2">فهم اللهجات المحلية</h5>
            <p class="text-white-50 fs-7 mb-0">يتعرف على مصطلحات العملاء المختلفة والأخطاء الإملائية ويفهم المعنى المقصود بدقة.</p>
          </div>
        </div>

        <!-- بطاقة 2 -->
        <div class="col-md-6 col-lg-3">
          <div class="chat-sim-card p-4 h-100 text-center">
            <div class="text-gold display-5 mb-3"><i class="bi bi-file-earmark-pdf"></i></div>
            <h5 class="fw-bold text-white mb-2">التعلم من المستندات</h5>
            <p class="text-white-50 fs-7 mb-0">يمكنك رفع ملفات PDF، Excel، أو روابط الموقع، وسيتعلم الذكاء الاصطناعي منها فوراً.</p>
          </div>
        </div>

        <!-- بطاقة 3 -->
        <div class="col-md-6 col-lg-3">
          <div class="chat-sim-card p-4 h-100 text-center">
            <div class="text-gold display-5 mb-3"><i class="bi bi-emoji-smile"></i></div>
            <h5 class="fw-bold text-white mb-2">تحليل مشاعر العملاء</h5>
            <p class="text-white-50 fs-7 mb-0">يكتشف نبرة العميل (غالٍ، مستعجل، غاضب) ويوجه المحادثة للأسلوب الأمثل أو ينبه فريقك.</p>
          </div>
        </div>

        <!-- بطاقة 4 -->
        <div class="col-md-6 col-lg-3">
          <div class="chat-sim-card p-4 h-100 text-center">
            <div class="text-gold display-5 mb-3"><i class="bi bi-cart-check"></i></div>
            <h5 class="fw-bold text-white mb-2">توصيات مبيعات ذكية</h5>
            <p class="text-white-50 fs-7 mb-0">يقترح المنتجات المناسبة بناءً على طلب العميل لمساعدتك في زيادات إجمالي المبيعات.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-4 border-top border-secondary border-opacity-25 text-center text-white-50 fs-7">
    <div class="container">
      <p class="mb-0">جميع الحقوق محفوظة © 2026 منصة ردود (Rudood)</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

