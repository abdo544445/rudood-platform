@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

  <!-- 1. Header & Live Channels Status Strip -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h3 class="fw-bold text-white mb-1 d-flex align-items-center gap-2">
        <i class="bi bi-diagram-3-fill text-gold"></i>
        <span>ربط القنوات والتكاملات (Omni-Channel Hub)</span>
      </h3>
      <p class="text-white-50 fs-8 mb-0">
        أدر كافة قنوات التواصل لمتجرك «<strong>{{ $workspace->company_name }}</strong>» وفعل أو عطل الردود الآلية لكل قناة بضغطة زر واحدة.
      </p>
    </div>

    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-dark border border-secondary text-white-50 fs-8 px-3 py-2">
        <i class="bi bi-robot text-gold me-1"></i> البوت النشط: <strong class="text-white">{{ $bot->name ?? 'مساعد المتجر' }}</strong>
      </span>
      <a href="{{ url('/playground') }}" class="btn btn-outline-gold rounded-pill px-3 fs-8">
        <i class="bi bi-lightning-charge-fill me-1"></i> اختبار الذكاء الاصطناعي
      </a>
    </div>
  </div>

  @if(session('status'))
  <div class="alert alert-success bg-dark bg-opacity-75 border-success text-success fs-8 rounded-3 mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill fs-6"></i>
    <span>{{ session('status') }}</span>
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-danger bg-dark bg-opacity-75 border-danger text-danger fs-8 rounded-3 mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill fs-6"></i>
    <span>{{ session('error') }}</span>
  </div>
  @endif

  <!-- 2. Channel Quick Telemetry Bar -->
  <div class="row g-3 mb-4">
    @php
      $channelList = [
        ['key' => 'whatsapp', 'name' => 'WhatsApp Cloud', 'icon' => 'bi-whatsapp', 'color' => 'text-success', 'border' => 'border-success'],
        ['key' => 'telegram', 'name' => 'Telegram Bot', 'icon' => 'bi-send', 'color' => 'text-info', 'border' => 'border-info'],
        ['key' => 'web', 'name' => 'Web Live Widget', 'icon' => 'bi-globe2', 'color' => 'text-gold', 'border' => 'border-warning'],
        ['key' => 'instagram', 'name' => 'Instagram Direct', 'icon' => 'bi-instagram', 'color' => 'text-danger', 'border' => 'border-danger'],
      ];
    @endphp

    @foreach($channelList as $item)
      @php $ch = $channels[$item['key']] ?? null; @endphp
      <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 bg-dark bg-opacity-40 border border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <i class="bi {{ $item['icon'] }} {{ $item['color'] }} fs-4"></i>
            <div>
              <div class="fs-8 fw-bold text-white">{{ $item['name'] }}</div>
              <div class="fs-9 {{ ($ch && $ch->isActive()) ? 'text-success' : 'text-white-50' }}">
                <i class="bi bi-circle-fill fs-9 me-1"></i> {{ ($ch && $ch->isActive()) ? 'مفعلة وتستقبل' : 'معطلة أو غير متصلة' }}
              </div>
            </div>
          </div>
          @if($ch && $ch->id)
          <form action="{{ route('channels.toggle', $ch->id) }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm {{ $ch->is_active ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-2 py-0 fs-9 fw-bold" title="تبديل التشغيل والإيقاف">
              {{ $ch->is_active ? 'ON' : 'OFF' }}
            </button>
          </form>
          @endif
        </div>
      </div>
    @endforeach
  </div>

  <!-- 3. Channel Cards Grid -->
  <div class="row g-4">

    <!-- 🟢 1. WhatsApp Cloud API Card -->
    <div class="col-lg-6">
      <div class="card bg-dark bg-opacity-50 border border-secondary border-opacity-25 rounded-4 h-100 shadow-sm">
        <div class="card-header bg-transparent border-bottom border-secondary border-opacity-25 p-3 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-3 bg-success bg-opacity-20 text-success"><i class="bi bi-whatsapp fs-5"></i></div>
            <div>
              <h5 class="fw-bold text-white mb-0 fs-7">واتساب كلاود (WhatsApp Cloud API)</h5>
              <small class="text-white-50 fs-9">الربط الرسمي المباشر مع منصة Meta للأعمال</small>
              <div class="d-flex flex-wrap gap-1 mt-1">
                <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25 fs-9 py-0 px-2"><i class="bi bi-hand-index-thumb me-1"></i> أزرار الرد السريع</span>
                <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-25 fs-9 py-0 px-2"><i class="bi bi-list-ul me-1"></i> القوائم التفاعلية</span>
                <span class="badge bg-info bg-opacity-20 text-info border border-info border-opacity-25 fs-9 py-0 px-2"><i class="bi bi-grid-3x3-gap me-1"></i> بطاقات الكتالوج</span>
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            @php $wa = $channels['whatsapp'] ?? null; @endphp
            @if($wa && $wa->id)
            <form action="{{ route('channels.toggle', $wa->id) }}" method="POST" class="m-0">
              @csrf
              <button type="submit" class="btn btn-sm {{ $wa->is_active ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 fs-9 fw-bold">
                {{ $wa->is_active ? '✓ مفعل (ON)' : '✕ معطل (OFF)' }}
              </button>
            </form>
            @endif
          </div>
        </div>

        <div class="card-body p-3">
          <form action="{{ route('channels.connect') }}" method="POST">
            @csrf
            <input type="hidden" name="platform" value="whatsapp">

            <div class="mb-3">
              <label class="form-label text-white-50 fs-8">Permanent Access Token (رمز الوصول الدائم من Meta):</label>
              <input type="password" name="access_token" class="form-control bg-black bg-opacity-40 border-secondary border-opacity-25 text-white fs-8"
                     placeholder="EAA..." value="{{ $wa?->access_token ? '••••••••••••••••' : '' }}" required>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-md-6">
                <label class="form-label text-white-50 fs-8">Phone Number ID (معرف رقم الهاتف):</label>
                <input type="text" name="phone_number_id" class="form-control bg-black bg-opacity-40 border-secondary border-opacity-25 text-white fs-8"
                       placeholder="1029384756..." value="{{ $wa?->phone_number_id }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label text-white-50 fs-8">Verify Token (رمز التحقق الخاص بك):</label>
                <input type="text" name="verify_token" class="form-control bg-black bg-opacity-40 border-secondary border-opacity-25 text-white fs-8"
                       placeholder="مثال: rudood_wa_secret" value="{{ $wa?->verify_token ?: 'rudood_whatsapp_token' }}">
              </div>
            </div>

            <div class="mb-3 p-2 rounded-3 bg-black bg-opacity-40 border border-secondary border-opacity-25">
              <label class="text-gold fs-9 fw-bold d-block mb-1"><i class="bi bi-link-45deg me-1"></i>رابط الـ Webhook للإدخال في Meta:</label>
              <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm bg-transparent border-0 text-white-50 fs-9" readonly
                       value="{{ url('/api/webhook/whatsapp') }}" id="waWebhookUrl">
                <button type="button" class="btn btn-sm btn-outline-warning fs-9 flex-shrink-0" onclick="copyToClipboard('waWebhookUrl')">نسخ</button>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              @if($wa && $wa->id)
              <a href="{{ route('channels.verify', $wa->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fs-8">
                <i class="bi bi-arrow-repeat me-1"></i> فحص الاتصال
              </a>
              @else
              <span></span>
              @endif
              <button type="submit" class="btn btn-sm btn-gold rounded-pill px-4 fs-8 fw-bold">حفظ إعدادات واتساب</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- 🔵 2. Telegram Bot Card -->
    <div class="col-lg-6">
      <div class="card bg-dark bg-opacity-50 border border-secondary border-opacity-25 rounded-4 h-100 shadow-sm">
        <div class="card-header bg-transparent border-bottom border-secondary border-opacity-25 p-3 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-3 bg-info bg-opacity-20 text-info"><i class="bi bi-send fs-5"></i></div>
            <div>
              <h5 class="fw-bold text-white mb-0 fs-7">بوت تيليجرام (Telegram Bot)</h5>
              <small class="text-white-50 fs-9">ربط مباشر وسريع عبر توكن BotFather</small>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            @php $tg = $channels['telegram'] ?? null; @endphp
            @if($tg && $tg->id)
            <form action="{{ route('channels.toggle', $tg->id) }}" method="POST" class="m-0">
              @csrf
              <button type="submit" class="btn btn-sm {{ $tg->is_active ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 fs-9 fw-bold">
                {{ $tg->is_active ? '✓ مفعل (ON)' : '✕ معطل (OFF)' }}
              </button>
            </form>
            @endif
          </div>
        </div>

        <div class="card-body p-3">
          <form action="{{ route('channels.connect') }}" method="POST">
            @csrf
            <input type="hidden" name="platform" value="telegram">

            <div class="mb-3">
              <label class="form-label text-white-50 fs-8">Bot Token (توكن البوت من @BotFather):</label>
              <input type="password" name="bot_token" class="form-control bg-black bg-opacity-40 border-secondary border-opacity-25 text-white fs-8"
                     placeholder="8698938459:AAEnsn9z..." value="{{ $tg?->bot_token ? '••••••••••••••••' : '' }}" required>
            </div>

            <div class="mb-3">
              <label class="form-label text-white-50 fs-8">معرف البوت (Bot Username):</label>
              <input type="text" name="bot_username" class="form-control bg-black bg-opacity-40 border-secondary border-opacity-25 text-white fs-8"
                     placeholder="@MyStoreBot" value="{{ $tg?->bot_username }}">
            </div>

            <div class="mb-3 p-2 rounded-3 bg-black bg-opacity-40 border border-secondary border-opacity-25">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-gold fs-9 fw-bold"><i class="bi bi-broadcast me-1"></i>خدمة الاستماع الحي (Polling):</span>
                <span class="badge bg-dark text-white-50 fs-9 font-monospace">php artisan telegram:poll</span>
              </div>
              <small class="text-white-50 fs-9 d-block">
                يعمل الـ Polling تلقائياً على السيرفر المحلي للاستجابة الفورية لكل محادثات التيليجرام.
              </small>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              @if($tg && $tg->id)
              <a href="{{ route('channels.verify', $tg->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fs-8">
                <i class="bi bi-arrow-repeat me-1"></i> فحص الاتصال
              </a>
              @else
              <span></span>
              @endif
              <button type="submit" class="btn btn-sm btn-gold rounded-pill px-4 fs-8 fw-bold">حفظ إعدادات تيليجرام</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- 🟡 3. Web Live Chat Widget Card -->
    <div class="col-lg-6">
      <div class="card bg-dark bg-opacity-50 border border-secondary border-opacity-25 rounded-4 h-100 shadow-sm">
        <div class="card-header bg-transparent border-bottom border-secondary border-opacity-25 p-3 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-3 bg-warning bg-opacity-20 text-gold"><i class="bi bi-globe2 fs-5"></i></div>
            <div>
              <h5 class="fw-bold text-white mb-0 fs-7">ودجت المحادثة المباشرة للموقع والمتجر (Web Widget)</h5>
              <small class="text-white-50 fs-9">ودجت عائم يضاف لأي متجر أو موقع (سلة، زد، شوبيفاي)</small>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            @php $wb = $channels['web'] ?? null; @endphp
            @if($wb && $wb->id)
            <form action="{{ route('channels.toggle', $wb->id) }}" method="POST" class="m-0">
              @csrf
              <button type="submit" class="btn btn-sm {{ $wb->is_active ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 fs-9 fw-bold">
                {{ $wb->is_active ? '✓ مفعل (ON)' : '✕ معطل (OFF)' }}
              </button>
            </form>
            @endif
          </div>
        </div>

        <div class="card-body p-3">
          
          <!-- 1-Line Embed Snippet -->
          <div class="mb-3 p-3 rounded-3 bg-black bg-opacity-60 border border-warning border-opacity-25">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="text-gold fs-8 fw-bold m-0"><i class="bi bi-code-slash me-1"></i>كود التضمين في متجرك (سطر واحد فقط):</label>
              <button type="button" class="btn btn-xs btn-gold rounded-pill px-2 py-0 fs-9 fw-bold" onclick="copyToClipboard('widgetEmbedCode')">
                <i class="bi bi-clipboard me-1"></i> نسخ الكود
              </button>
            </div>
            <textarea id="widgetEmbedCode" class="form-control form-control-sm bg-dark border-0 text-warning font-monospace fs-9" rows="2" readonly>&lt;script src="{{ url('/widget.js') }}" data-workspace="{{ $workspace->id }}" data-color="{{ $wb?->widget_color ?? '#d4af37' }}" data-position="{{ $wb?->widget_position ?? 'right' }}"&gt;&lt;/script&gt;</textarea>
            <small class="text-white-50 fs-9 mt-1 d-block">انسخ هذا الكود وضعه قبل وسم &lt;/body&gt; في متجرك بسلة أو زد أو موقعك الخاص.</small>
          </div>

          <!-- Customizer Form -->
          <form action="{{ route('channels.widget.save') }}" method="POST">
            @csrf
            <div class="row g-2 mb-3">
              <div class="col-md-6">
                <label class="form-label text-white-50 fs-8">لون الودجت الأساسي:</label>
                <div class="d-flex align-items-center gap-2">
                  <input type="color" name="widget_color" class="form-control form-control-color bg-transparent border-0 p-0"
                         value="{{ $wb?->widget_color ?? '#d4af37' }}" id="widgetColorPicker" style="width: 40px; height: 35px;">
                  <input type="text" class="form-control bg-black bg-opacity-40 border-secondary border-opacity-25 text-white fs-8"
                         value="{{ $wb?->widget_color ?? '#d4af37' }}" id="widgetColorHex" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label text-white-50 fs-8">موقع ظهور الودجت:</label>
                <select name="widget_position" class="form-select bg-black bg-opacity-40 border-secondary border-opacity-25 text-white fs-8">
                  <option value="right" {{ ($wb?->widget_position ?? 'right') === 'right' ? 'selected' : '' }}>أسفل اليمين (Right)</option>
                  <option value="left" {{ ($wb?->widget_position ?? 'right') === 'left' ? 'selected' : '' }}>أسفل اليسار (Left)</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label text-white-50 fs-8">رسالة الترحيب الأولى للزائر:</label>
              <input type="text" name="widget_greeting" class="form-control bg-black bg-opacity-40 border-secondary border-opacity-25 text-white fs-8"
                     placeholder="أهلاً بك في متجرنا! كيف أقدر أساعدك اليوم؟"
                     value="{{ $wb?->widget_greeting ?: 'أهلاً بك! كيف أقدر أساعدك اليوم؟' }}">
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 fs-8" onclick="openWidgetTestPreview()">
                <i class="bi bi-eye-fill me-1"></i> معاينة الودجت حياً
              </button>
              <button type="submit" class="btn btn-sm btn-gold rounded-pill px-4 fs-8 fw-bold">حفظ تخصيص الودجت</button>
            </div>
          </form>

        </div>
      </div>
    </div>

    <!-- 🟣 4. Instagram Direct & Comments Card -->
    <div class="col-lg-6">
      <div class="card bg-dark bg-opacity-50 border border-secondary border-opacity-25 rounded-4 h-100 shadow-sm">
        <div class="card-header bg-transparent border-bottom border-secondary border-opacity-25 p-3 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-3 bg-danger bg-opacity-20 text-danger"><i class="bi bi-instagram fs-5"></i></div>
            <div>
              <h5 class="fw-bold text-white mb-0 fs-7">إنستغرام دايركت والتعليقات (Instagram Direct)</h5>
              <small class="text-white-50 fs-9">الرد الآلي على رسائل الخاص والتعليقات على المنشورات</small>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            @php $ig = $channels['instagram'] ?? null; @endphp
            @if($ig && $ig->id)
            <form action="{{ route('channels.toggle', $ig->id) }}" method="POST" class="m-0">
              @csrf
              <button type="submit" class="btn btn-sm {{ $ig->is_active ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 fs-9 fw-bold">
                {{ $ig->is_active ? '✓ مفعل (ON)' : '✕ معطل (OFF)' }}
              </button>
            </form>
            @endif
          </div>
        </div>

        <div class="card-body p-3">
          <form action="{{ route('channels.connect') }}" method="POST">
            @csrf
            <input type="hidden" name="platform" value="instagram">

            <div class="mb-3">
              <label class="form-label text-white-50 fs-8">Instagram Business Account ID (معرف حساب إنستغرام للأعمال):</label>
              <input type="text" name="instagram_account_id" class="form-control bg-black bg-opacity-40 border-secondary border-opacity-25 text-white fs-8"
                     placeholder="17841400..." value="{{ $ig?->instagram_account_id }}" required>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-md-6">
                <label class="form-label text-white-50 fs-8">Page Access Token (رمز وصول الصفحة):</label>
                <input type="password" name="page_access_token" class="form-control bg-black bg-opacity-40 border-secondary border-opacity-25 text-white fs-8"
                       placeholder="EAA..." value="{{ $ig?->page_access_token ? '••••••••••••••••' : '' }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label text-white-50 fs-8">Verify Token (رمز التحقق الخاص بك):</label>
                <input type="text" name="verify_token" class="form-control bg-black bg-opacity-40 border-secondary border-opacity-25 text-white fs-8"
                       placeholder="rudood_ig_secret" value="{{ $ig?->verify_token ?: 'rudood_instagram_token' }}">
              </div>
            </div>

            <!-- Auto-Reply to Post Comments Switch -->
            <div class="mb-3 p-2 rounded-3 bg-black bg-opacity-40 border border-secondary border-opacity-25">
              <div class="form-check form-switch mb-1">
                <input class="form-check-input" type="checkbox" name="auto_reply_comments" id="autoCommentsSwitch" value="1"
                       {{ ($ig?->auto_reply_comments ?? false) ? 'checked' : '' }}>
                <label class="form-check-label text-white fs-8 fw-bold" for="autoCommentsSwitch">
                  أتمتة الرد على التعليقات في المنشورات (Auto-Reply to Comments)
                </label>
              </div>
              <small class="text-white-50 fs-9 d-block">
                عند قيام أي عميل بالتعليق على منشوراتك، يقوم البوت بالرد على التعليق ومراسلته في الخاص فوراً.
              </small>
            </div>

            <div class="mb-3 p-2 rounded-3 bg-black bg-opacity-40 border border-secondary border-opacity-25">
              <label class="text-gold fs-9 fw-bold d-block mb-1"><i class="bi bi-link-45deg me-1"></i>رابط الـ Webhook للإدخال في Meta Developer:</label>
              <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm bg-transparent border-0 text-white-50 fs-9" readonly
                       value="{{ url('/api/webhook/instagram') }}" id="igWebhookUrl">
                <button type="button" class="btn btn-sm btn-outline-warning fs-9 flex-shrink-0" onclick="copyToClipboard('igWebhookUrl')">نسخ</button>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              @if($ig && $ig->id)
              <a href="{{ route('channels.verify', $ig->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fs-8">
                <i class="bi bi-arrow-repeat me-1"></i> فحص الاتصال
              </a>
              @else
              <span></span>
              @endif
              <button type="submit" class="btn btn-sm btn-gold rounded-pill px-4 fs-8 fw-bold">حفظ إعدادات إنستغرام</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>

</div>

<!-- Interactive Live Widget Script for merchant to test directly -->
<script src="{{ url('/widget.js') }}" data-workspace="{{ $workspace->id }}" data-color="{{ $channels['web']->widget_color ?? '#d4af37' }}" data-position="{{ $channels['web']->widget_position ?? 'right' }}"></script>

<script>
  function copyToClipboard(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return;
    el.select();
    navigator.clipboard.writeText(el.value).then(() => {
      alert('✓ تم نسخ الرابط / الكود بنجاح!');
    });
  }

  function openWidgetTestPreview() {
    const launcher = document.getElementById('rudood-widget-launcher');
    if (launcher) {
      launcher.click();
    } else {
      alert('الودجت نشط في زاوية الصفحة الآن!');
    }
  }

  // Update Color Hex on picker change
  const picker = document.getElementById('widgetColorPicker');
  const hex = document.getElementById('widgetColorHex');
  if (picker && hex) {
    picker.addEventListener('input', () => {
      hex.value = picker.value;
    });
  }
</script>
@endsection
