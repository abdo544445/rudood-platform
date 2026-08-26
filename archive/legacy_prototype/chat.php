<?php
$pageTitle = "منصة ردود - إدارة المحادثات والعملاء (عرض تجريبي)";
$currentPage = "chat";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

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
          <form id="chatForm" action="api/send_message.php" method="POST" class="d-flex align-items-center gap-2">
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
          <form id="notesForm" action="api/save_note.php" method="POST">
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

