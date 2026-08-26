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
  @include('layouts.partials.theme')

  <style>
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
  @include('layouts.partials.sidebar')

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

    @if (session('error'))
    <div class="alert py-2 mb-4" style="background: rgba(231,76,60,0.15); border: 1px solid #e74c3c; color: #e74c3c; border-radius: 10px;">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    </div>
    @endif

    @if (isset($errors) && $errors->any())
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
            <div class="file-item d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                <div>
                  <span class="text-white fs-7 d-block">{{ $doc->file_name }}</span>
                  <small class="text-white-50">{{ $doc->created_at->diffForHumans() }} | {{ count($doc->chunks ?? []) }} مقطع</small>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <!-- زر استخراج وتوليد الأسئلة والأجوبة بالذكاء الاصطناعي -->
                <form action="{{ route('ai.generate-faq', $doc->id) }}" method="POST" class="d-inline" onsubmit="handleGenerateFaqSubmit(this)">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 fs-9 fw-bold" title="استخراج 5 أسئلة وأجوبة شائعة من هذا الملف وإضافتها للقواعد الفورية">
                    <i class="bi bi-stars text-gold me-1"></i> استخراج أسئلة بالـ AI
                  </button>
                </form>

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
            <div class="mb-3">
              <label for="faqKeywords" class="form-label text-white fs-7 fw-bold">
                الكلمات المفتاحية للمطابقة الفورية <span class="text-white-50 fs-8 fw-normal">(مفصولة بفاصلة)</span>
              </label>
              <input type="text" id="faqKeywords" name="keywords" class="form-control custom-input"
                placeholder="مثال: توصيل, مدة, شحن, اوقات">
              <small class="text-white-50 fs-8">إذا تُركت فارغة، سيتم استخراج الكلمات الأساسية من السؤال تلقائياً.</small>
            </div>
            <div class="mb-4">
              <label for="faqAnswer" class="form-label text-white fs-7 fw-bold">الإجابة النموذجية للبوت</label>
              <textarea id="faqAnswer" name="answer" class="form-control custom-input" rows="4"
                placeholder="اكتب الإجابة الدقيقة التي سيقوم البوت بإرسالها للعميل..." required></textarea>
            </div>
            <button type="submit" class="btn btn-gold w-100 py-2">
              <i class="bi bi-plus-circle me-1"></i> حفظ السؤال وتحديث قاعدة المعرفة
            </button>
          </form>
        </div>
      </div>

    </div>

    <!-- بنر الانتقال إلى مختبر الذكاء الاصطناعي المتقدم -->
    <div class="stat-card p-4 mb-5" style="border: 1px solid rgba(212,175,55,0.35) !important; background: linear-gradient(135deg, rgba(212,175,55,0.1) 0%, rgba(15,23,42,0.8) 100%) !important;">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
          <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(212,175,55,0.2); border: 1px solid var(--gold); display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 1.5rem;">
            <i class="bi bi-robot"></i>
          </div>
          <div>
            <h5 class="fw-bold text-white mb-1">مختبر الذكاء الاصطناعي المتطور (AI Playground Workbench)</h5>
            <p class="text-white-50 mb-0 fs-8">اختبر ردود المساعد الذكي، عاين استرجاع المقاطع (RAG)، واضبط المعاملات ونبرة الرد في بيئة تفاعلية متكاملة.</p>
          </div>
        </div>
        <a href="{{ url('/playground') }}" class="btn btn-gold px-4 py-2 rounded-pill fw-bold d-flex align-items-center gap-2">
          <i class="bi bi-box-arrow-up-right"></i> فتح المختبر الكامل (Open Playground)
        </a>
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
          @if (!empty($rule->keywords) && is_array($rule->keywords))
          <div class="d-flex flex-wrap gap-1 ps-3 my-1">
            @foreach ($rule->keywords as $kw)
              <span class="badge bg-secondary bg-opacity-25 text-white-50 fs-9">{{ $kw }}</span>
            @endforeach
          </div>
          @endif
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
  <script>
    function handleGenerateFaqSubmit(form) {
      const btn = form.querySelector('button');
      if (btn) {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري التوليد...';
        btn.disabled = true;
      }
    }
  </script>
</body>
</html>
