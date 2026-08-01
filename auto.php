<?php
$pageTitle = "خدمات الرد الآلي - منصة ردود";
$currentPage = "auto";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

  <!-- 2. قسم العرض الرئيسي (Hero Section) مع بطاقة شات منسقة -->
  <section class="py-5 position-relative overflow-hidden">
    <div class="container py-4">
      <div class="row align-items-center g-4">
        
        <!-- العمود الأيمن: العناوين -->
        <div class="col-lg-6 text-start">
          <span class="badge bg-gold-subtle text-gold border border-gold border-opacity-25 px-3 py-2 rounded-pill mb-3 fw-bold">
            <i class="bi bi-robot me-1"></i> أتمتة الردود الذكية
          </span>
          <h1 class="display-5 fw-extrabold text-white mb-3">خدمات الرد الآلي الذكية في منصة ردود</h1>
          <p class="lead text-white-50 mb-4">
            تجاوب مع استفسارات العملاء تلقائياً باستخدام تقنيات فهم اللغة الطبيعية. خدمة مستمرة وسريعة دون توقف.
          </p>
          <div class="d-flex gap-3">
            <a href="chat.php" class="btn btn-gold px-4 py-3 rounded-pill fw-bold">تجربة الرد الآلي</a>
            
          </div>
        </div>

        <!-- العمود الأيسر: بطاقة المحاكاة المنسقة (Chat Card) -->
        <div class="col-lg-6">
          <div class="chat-sim-card p-4 rounded-4">
            
            <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom border-secondary border-opacity-25">
              <div class="avatar-circle">R</div>
              <div>
                <h6 class="mb-0 fw-bold text-white">مساعد ردود الذكي</h6>
                <small class="text-gold fs-8"><i class="bi bi-circle-fill text-success me-1"></i> متصل الآن</small>
              </div>
            </div>

            <div class="d-flex flex-column gap-3">
              <!-- سؤال العميل -->
              <div class="chat-bubble-incoming p-3 text-start">
                مرحباً، ما هي أوقات العمل لديكم وكيف أستطيع الاشتراك؟
              </div>

              <!-- رد البوت -->
              <div class="chat-bubble-outgoing p-3 text-start">
                أهلاً بك! 🌸 نحن نعمل على مدار 24 ساعة طوال أيام الأسبوع. يمكنك الاشتراك مباشرة عبر الضغط على زر "جرب المنصة الآن" واختيار الباقة المناسبة لك.
              </div>

              <!-- سؤال العميل 2 -->
              <div class="chat-bubble-incoming p-3 text-start">
                هل يمكن للذكاء الاصطناعي الإجابة عن أسئلة منتجاتي؟
              </div>

              <!-- رد البوت 2 -->
              <div class="chat-bubble-outgoing p-3 text-start">
                بالتأكيد! بمجرد رفع كتالوج منتجاتك أو ملف الأسئلة الشائعة، سيتعلم مساعد ردود التفاعل والإجابة بدقة عالية. ✨
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

