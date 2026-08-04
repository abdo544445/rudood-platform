<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تدريب الذكاء الاصطناعي | منصة ردود</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">

  <style>
    body { background-color: #0b0f19 !important; color: #ffffff !important; font-family: 'Cairo', sans-serif; min-height: 100vh; }
    .sidebar { width: 260px; background: rgba(15, 23, 42, 0.95) !important; backdrop-filter: blur(16px); border-left: 1px solid rgba(212, 175, 55, 0.2); min-height: 100vh; position: fixed; top: 0; right: 0; z-index: 1000; }
    .sidebar .nav-link { color: rgba(255, 255, 255, 0.7) !important; padding: 12px 18px; border-radius: 10px; margin: 4px 10px; transition: all 0.3s ease; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #000000 !important; background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%) !important; font-weight: bold; }
    .main-content { margin-right: 260px; padding: 30px; }
    .stat-card { background: rgba(255, 255, 255, 0.03) !important; backdrop-filter: blur(12px); border: 1px solid rgba(212, 175, 55, 0.2) !important; border-radius: 16px; }
    .text-gold { color: #d4af37 !important; }
    .upload-zone { border: 2px dashed rgba(212, 175, 55, 0.4); background: rgba(255, 255, 255, 0.02); border-radius: 16px; padding: 30px; text-align: center; transition: all 0.3s ease; cursor: pointer; }
    .upload-zone:hover { border-color: #d4af37; background: rgba(212, 175, 55, 0.05); }
    .file-item { background: rgba(15, 23, 42, 0.7) !important; border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 10px; padding: 12px 16px; margin-bottom: 10px; }
    .file-status-badge { background: rgba(46, 204, 113, 0.2) !important; color: #2ecc71 !important; border: 1px solid #2ecc71; padding: 4px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; }
    .custom-input { background: rgba(15, 23, 42, 0.8) !important; border: 1px solid rgba(212, 175, 55, 0.3) !important; color: #ffffff !important; border-radius: 10px; padding: 12px; }
    .custom-input:focus { border-color: #d4af37 !important; box-shadow: 0 0 10px rgba(212, 175, 55, 0.2) !important; }
    .custom-input::placeholder { color: rgba(255, 255, 255, 0.4) !important; }
    .btn-gold { background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%) !important; color: #000000 !important; border: none; font-weight: bold; }
    .rule-item { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(212, 175, 55, 0.15); border-radius: 12px; padding: 14px 18px; margin-bottom: 10px; }
  </style>
</head>
<body>

  <!-- الشريط الجانبي -->
  <aside class="sidebar d-flex flex-column justify-content-between py-3">
    <div>
      <div class="px-4 mb-4 text-center">
        <a href="{{ url('/index') }}">
          <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" style="max-height: 45px;">
        </a>
      </div>
      <ul class="nav nav-pills flex-column">
        <li class="nav-item"><a href="{{ url('/dashboard') }}" class="nav-link d-flex align-items-center gap-3"><i class="bi bi-grid-1x2-fill"></i> الرئيسية</a></li>
        <li class="nav-item"><a href="{{ url('/ai-manage') }}" class="nav-link active d-flex align-items-center gap-3"><i class="bi bi-cpu-fill"></i> تدريب الذكاء الاصطناعي</a></li>
        <li class="nav-item"><a href="{{ url('/live-chat') }}" class="nav-link d-flex align-items-center gap-3"><i class="bi bi-chat-dots-fill"></i> المحادثات المباشرة</a></li>
        <li class="nav-item"><a href="{{ url('/settings') }}" class="nav-link d-flex align-items-center gap-3"><i class="bi bi-gear-fill"></i> الإعدادات والقنوات</a></li>
      </ul>
    </div>
    <div class="px-3">
      <form action="{{ url('/logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
          <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
        </button>
      </form>
    </div>
  </aside>

  <!-- المحتوى الرئيسي -->
  <main class="main-content">

    <!-- الشريط العلوي -->
    <div class="mb-4 pb-3 border-bottom border-secondary border-opacity-25">
      <h3 class="fw-bold text-white mb-1"><i class="bi bi-cpu-fill text-gold me-2"></i>تدريب الذكاء الاصطناعي</h3>
      <p class="text-white-50 mb-0 fs-7">قم بتزويد مساعدك الذكي ببيانات متجرك ليعرف كيف يجيب عملاءك بدقة</p>
    </div>

    @if (session('status'))
    <div class="alert py-2 mb-4" style="background: rgba(46,204,113,0.15); border: 1px solid #2ecc71; color: #2ecc71; border-radius: 10px;">
      <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger mb-4 py-2">{{ $errors->first() }}</div>
    @endif

    <!-- البطاقتان الرئيسيتان -->
    <div class="row g-4 mb-5">

      <!-- القسم الأول: رفع المستندات -->
      <div class="col-lg-6">
        <div class="stat-card h-100 p-4 d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-file-earmark-arrow-up-fill text-gold fs-4"></i>
              <h5 class="fw-bold text-white mb-0">رفع المستندات والكتالوجات</h5>
            </div>
            <p class="text-white-50 fs-7 mb-4">ارفع ملفات PDF أو Word تحتوي على تفاصيل المنتجات، الأسعار، أو سياسة المتجر.</p>

            <form id="uploadDocForm" action="{{ url('/ai-manage/upload-doc') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="upload-zone mb-3" onclick="document.getElementById('docFileInput').click();">
                <i class="bi bi-cloud-arrow-up-fill text-gold display-5 d-block mb-2"></i>
                <p class="fw-bold text-white mb-1 fs-6">اضغط هنا لرفع الملف أو اسحبه إلى هنا</p>
                <span class="text-white-50 fs-8">يدعم صيغ (PDF, DOCX, TXT) بحد أقصى 15 ميجابايت</span>
                <input type="file" id="docFileInput" name="doc_file" class="d-none" accept=".pdf,.docx,.doc,.txt"
                  onchange="document.getElementById('fileNameDisplay').innerText = this.files[0] ? this.files[0].name : '';">
                <div id="fileNameDisplay" class="text-gold fw-bold mt-2 fs-7"></div>
              </div>
              <button type="submit" class="btn btn-gold w-100 py-2 mb-4">
                <i class="bi bi-cloud-upload me-1"></i> رفع الملف وتدريب البوت
              </button>
            </form>
          </div>

          <!-- قائمة الملفات المرفوعة من الـ DB -->
          <div>
            <h6 class="fw-bold text-white fs-7 mb-2">الملفات المدربة حالياً ({{ $docs->count() }}):</h6>
            @forelse ($docs as $doc)
            <div class="file-item d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                <div>
                  <span class="text-white fs-7 d-block">{{ $doc->file_name }}</span>
                  <small class="text-white-50">{{ $doc->created_at->diffForHumans() }}</small>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="file-status-badge"><i class="bi bi-check-circle-fill me-1"></i>مكتمل</span>
                <form action="{{ url('/ai-manage/doc/' . $doc->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0"
                    onclick="return confirm('هل أنت متأكد من حذف هذا الملف؟')">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </div>
            @empty
            <div class="text-white-50 fs-7 text-center py-3">
              <i class="bi bi-folder-x display-6 d-block mb-2 opacity-50"></i>
              لم يتم رفع أي ملفات بعد
            </div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- القسم الثاني: إضافة سؤال وجواب -->
      <div class="col-lg-6">
        <div class="stat-card h-100 p-4">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-question-square-fill text-gold fs-4"></i>
            <h5 class="fw-bold text-white mb-0">إضافة سؤال وجواب مباشر</h5>
          </div>
          <p class="text-white-50 fs-7 mb-4">أدخل الأسئلة المتكررة وإجاباتها النموذجية مباشرة للنظام.</p>

          <form id="faqForm" action="{{ url('/ai-manage/save-rule') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="faqQuestion" class="form-label text-white fs-7 fw-bold">السؤال المتوقع من العميل</label>
              <input type="text" id="faqQuestion" name="question" class="form-control custom-input"
                placeholder="مثال: ما هي أوقات التوصيل لديكم؟" required>
            </div>
            <div class="mb-4">
              <label for="faqAnswer" class="form-label text-white fs-7 fw-bold">الإجابة النموذجية للبوت</label>
              <textarea id="faqAnswer" name="answer" class="form-control custom-input" rows="5"
                placeholder="اكتب الإجابة الدقيقة التي سيقوم البوت بإرسالها للعميل..." required></textarea>
            </div>
            <button type="submit" class="btn btn-gold w-100 py-2">
              <i class="bi bi-plus-circle me-1"></i> حفظ السؤال وتحديث قاعدة المعرفة
            </button>
          </form>
        </div>
      </div>

    </div>

    <!-- قائمة القواعد المحفوظة -->
    <div>
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold text-white mb-0">
          <i class="bi bi-list-check text-gold me-2"></i>قاعدة المعرفة المحفوظة
          <span class="badge ms-2 rounded-pill" style="background: rgba(212,175,55,0.2); color: #d4af37; font-size: 0.8rem;">
            {{ $rules->count() }} قاعدة
          </span>
        </h4>
      </div>

      @forelse ($rules as $rule)
      <div class="rule-item d-flex align-items-start justify-content-between gap-3">
        <div class="flex-grow-1">
          <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-chat-right-quote-fill text-gold fs-7"></i>
            <span class="fw-bold text-white fs-7">{{ $rule->question }}</span>
          </div>
          <p class="text-white-50 fs-8 mb-0 ps-3" style="white-space: pre-line;">{{ Str::limit($rule->reply_template, 180) }}</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
          <span class="badge rounded-pill" style="background:rgba(46,204,113,0.15);color:#2ecc71;font-size:0.75rem;">
            {{ $rule->is_active ? 'مفعّل' : 'معطّل' }}
          </span>
          <form action="{{ url('/ai-manage/rule/' . $rule->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2"
              onclick="return confirm('حذف هذه القاعدة؟')">
              <i class="bi bi-trash"></i>
            </button>
          </form>
        </div>
      </div>
      @empty
      <div class="text-center text-white-50 py-5">
        <i class="bi bi-database-x display-4 d-block mb-3 opacity-40"></i>
        <p>قاعدة المعرفة فارغة. أضف أسئلة وأجوبة أعلاه أو ارفع مستنداً لتدريب البوت.</p>
      </div>
      @endforelse
    </div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
