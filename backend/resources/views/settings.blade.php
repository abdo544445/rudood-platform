<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الإعدادات والقنوات | منصة ردود</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  @include('layouts.partials.theme')

  <style>
    .glass-card {
      background: rgba(255, 255, 255, 0.03);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(212, 175, 55, 0.2);
      border-radius: 16px;
      padding: 25px;
      margin-bottom: 25px;
    }

    .form-control-dark, .form-select-dark {
      background-color: rgba(11, 15, 25, 0.6) !important;
      border: 1px solid rgba(212, 175, 55, 0.25) !important;
      color: #fff !important;
    }

    .btn-gold {
      background-color: #D4AF37 !important;
      color: #0b0f19 !important;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <!-- الشريط الجانبي (Sidebar) -->
  @include('layouts.partials.sidebar')

  <!-- المحتوى الرئيسي -->
  <main class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
      <div>
        <h3 class="fw-bold text-white mb-1"><i class="bi bi-gear text-gold me-2"></i>الإعدادات وتخصيص البوت</h3>
        <p class="text-white-50 mb-0 fs-7">التحكم بسياسات الرد ونبرة المحادثة ومزود الذكاء الاصطناعي</p>
      </div>
    </div>

    @if (session('status'))
    <div class="alert alert-success mb-4 py-2">
        <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
    </div>
    @endif

    <div class="row g-4">
      
      <!-- إعدادات المساعد الذكي -->
      <div class="col-lg-7">
        <div class="glass-card">
          <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
            <div>
              <h4 class="fw-bold text-white mb-1"><i class="bi bi-robot text-gold me-2"></i>تخصيص سلوك البوت</h4>
              <p class="text-white-50 fs-8 mb-0">تحكم بهوية المساعد ونصوص الترحيب وتفعيل الردود الذكية</p>
            </div>
            <div class="d-flex align-items-center gap-3">
              <span id="botActiveBadge" class="badge {{ $bot->is_active ? 'bg-success' : 'bg-danger' }} bg-opacity-25 {{ $bot->is_active ? 'text-success' : 'text-danger' }} border {{ $bot->is_active ? 'border-success' : 'border-danger' }} px-3 py-2 fs-8 fw-bold">
                <i class="bi {{ $bot->is_active ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }} me-1" id="botActiveIcon"></i>
                <span id="botActiveText">{{ $bot->is_active ? 'البوت مفعّل ونشط' : 'البوت معطّل (إيقاف مؤقت)' }}</span>
              </span>
              <div class="form-check form-switch m-0" style="transform: scale(1.3);" title="تبديل تفعيل / إيقاف البوت فوراً">
                <input class="form-check-input" type="checkbox" role="switch" id="botIsActiveSwitch" name="is_active" value="1" {{ $bot->is_active ? 'checked' : '' }} style="cursor: pointer;" onchange="toggleBotStatus(this)">
              </div>
            </div>
          </div>
          
          <form action="{{ url('/settings/save-bot') }}" method="POST" id="botSettingsForm">
            @csrf
            <input type="hidden" name="is_active" id="hiddenIsActiveInput" value="{{ $bot->is_active ? '1' : '0' }}">
            
            <div class="mb-3">
              <label for="botName" class="form-label text-white-50 fs-7">اسم البوت (المساعد الذكي)</label>
              <input type="text" id="botName" name="bot_name" class="form-control form-control-dark" value="{{ $bot->name }}" required>
            </div>

            <div class="mb-3">
              <label for="botTone" class="form-label text-white-50 fs-7">نبرة الحديث والتفاعل</label>
              <select id="botTone" name="bot_tone" class="form-select form-select-dark">
                <option value="formal" {{ $bot->bot_tone === 'formal' ? 'selected' : '' }}>احترافية ورسمية</option>
                <option value="friendly" {{ $bot->bot_tone === 'friendly' ? 'selected' : '' }}>ودودة ومرحبة</option>
                <option value="sales" {{ $bot->bot_tone === 'sales' ? 'selected' : '' }}>تسويقية ومحفزة للشراء</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="welcomeMsg" class="form-label text-white-50 fs-7">رسالة الترحيب الآلية الأوليّة</label>
              <textarea id="welcomeMsg" name="welcome_message" class="form-control form-control-dark" rows="3" required>{{ $bot->welcome_message }}</textarea>
            </div>

            <div class="mb-4">
              <label for="systemPrompt" class="form-label text-white-50 fs-7">النظام الموجّه (System Prompt)</label>
              <textarea id="systemPrompt" name="system_prompt" class="form-control form-control-dark" rows="4" placeholder="حدد شخصية البوت، مجال عمله، وسلوكه...">{{ $bot->system_prompt }}</textarea>
            </div>

            <button type="submit" class="btn btn-gold px-4 rounded-pill">
              <i class="bi bi-save me-1"></i> حفظ إعدادات البوت
            </button>
          </form>
        </div>
      </div>

      <!-- إعدادات مزود الذكاء الاصطناعي ومفتاح API -->
      <div class="col-lg-5">
        <div class="glass-card">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-white mb-0"><i class="bi bi-cpu text-gold me-2"></i>مزود الذكاء الاصطناعي</h4>
            @if($bot->api_key_encrypted)
              <span class="badge bg-success bg-opacity-25 text-success border border-success fs-8">
                <i class="bi bi-shield-lock-fill me-1"></i> مفتاح API محفوظ ومشفّر
              </span>
            @else
              <span class="badge bg-warning bg-opacity-25 text-warning border border-warning fs-8">
                <i class="bi bi-key me-1"></i> بانتظار إدخال المفتاح
              </span>
            @endif
          </div>

          <p class="text-white-50 mb-3 fs-7">
            بياناتك ومفاتيحك مشفرة تماماً في قاعدة البيانات (AES-256). يمكنك استخدام Gemini أو OpenAI أو Claude أو أي مزود متوافق.
          </p>

          <form action="{{ url('/settings/save-ai-key') }}" method="POST" id="aiKeyForm">
            @csrf
            
            <!-- اختيار المزود -->
            <div class="mb-3">
              <label class="form-label text-white-50 fs-7">مزود الذكاء الاصطناعي</label>
              <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach(['openai' => ['OpenAI', 'bi-openai'], 'gemini' => ['Google Gemini', 'bi-google'], 'anthropic' => ['Claude / Anthropic', 'bi-stars'], 'openai_compatible' => ['متوافق مع OpenAI', 'bi-code-slash']] as $key => $label)
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="ai_provider" id="provider_{{ $key }}" value="{{ $key }}"
                    {{ $bot->ai_provider === $key ? 'checked' : '' }}
                    onchange="handleProviderChange('{{ $key }}')">
                  <label class="form-check-label text-white" for="provider_{{ $key }}">
                    <i class="bi {{ $label[1] }} me-1"></i>{{ $label[0] }}
                  </label>
                </div>
                @endforeach
              </div>
            </div>

            <!-- اسم النموذج وجلب النماذج -->
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="model_type" class="form-label text-white-50 fs-7 mb-0">اسم النموذج (Model)</label>
                <button type="button" class="btn btn-link text-gold p-0 fs-8 text-decoration-none" id="fetchModelsBtn" onclick="fetchModelsForProvider()">
                  <i class="bi bi-arrow-repeat me-1"></i> جلب النماذج المتاحة
                </button>
              </div>
              <div id="modelInputContainer">
                <input type="text" id="model_type" name="model_type" class="form-control form-control-dark"
                  value="{{ $bot->model_type }}" placeholder="gpt-4o-mini, gemini-1.5-flash, moonshotai/Kimi-K2.6..." required>
              </div>
            </div>

            <!-- مفتاح API -->
            <div class="mb-3">
              <label for="ai_api_key" class="form-label text-white-50 fs-7">مفتاح API الخاص بك</label>
              <div class="input-group">
                <input type="password" id="ai_api_key" name="ai_api_key" class="form-control form-control-dark"
                  placeholder="{{ $bot->api_key_encrypted ? '•••••••••••••••• (مفتاحك محفوظ ومشفّر - املأ هنا للتحديث)' : 'أدخل مفتاح API الخاص بك هنا...' }}" autocomplete="new-password">
                <button class="btn btn-outline-secondary border-opacity-25 text-white-50" type="button" onclick="togglePasswordVisibility('ai_api_key')">
                  <i class="bi bi-eye" id="ai_api_key_eye"></i>
                </button>
              </div>
              <div class="form-text text-white-50 fs-8"><i class="bi bi-shield-lock text-gold me-1"></i> يتم تشفير المفتاح وتخزينه في جدول البوت بقاعدة البيانات بشكل فوري.</div>
            </div>

            <!-- Base URL (للمزودين المتوافقين فقط) -->
            <div class="mb-3" id="baseUrlSection" style="display: {{ $bot->ai_provider === 'openai_compatible' ? 'block' : 'none' }}">
              <label for="api_base_url" class="form-label text-white-50 fs-7">Base URL للمزود (OpenAI Compatible API Base)</label>
              <input type="url" id="api_base_url" name="api_base_url" class="form-control form-control-dark"
                value="{{ $bot->api_base_url }}" placeholder="https://api.your-provider.com/v1">
            </div>

            <!-- إعدادات متقدمة -->
            <div class="row g-3 mb-4">
              <div class="col-6">
                <label for="max_tokens" class="form-label text-white-50 fs-7">حد الرد (Tokens)</label>
                <input type="number" id="max_tokens" name="max_tokens" class="form-control form-control-dark" value="{{ $bot->max_tokens }}" min="100" max="8000">
              </div>
              <div class="col-6">
                <label for="temperature" class="form-label text-white-50 fs-7">الإبداع (0–1)</label>
                <input type="number" id="temperature" name="temperature" class="form-control form-control-dark" value="{{ $bot->temperature }}" min="0" max="1" step="0.1">
              </div>
            </div>

            <button type="submit" class="btn btn-gold w-100 rounded-pill">
              <i class="bi bi-save me-2"></i>حفظ إعدادات الذكاء الاصطناعي
            </button>
          </form>
        </div>
      </div>

    </div>

    <!-- القنوات والتكاملات (Omni-Channel Hub Banner) -->
    <div class="glass-card mt-4 p-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 rounded-3 bg-warning bg-opacity-15 text-gold fs-4">
            <i class="bi bi-diagram-3-fill"></i>
          </div>
          <div>
            <h4 class="fw-bold text-white mb-1">مركز ربط القنوات والتكاملات (Omni-Channel Hub)</h4>
            <p class="text-white-50 mb-0 fs-7">
              تم تخصيص صفحة مستقلة متكاملة لإدارة كافة قنوات التواصل لمتجرك مع مفاتيح تشغيل وإيقاف الردود بضغطة زر وفحص الاتصال.
            </p>
          </div>
        </div>
        <a href="{{ url('/channels') }}" class="btn btn-gold rounded-pill px-4 py-2 fs-8 fw-bold">
          <i class="bi bi-gear-wide-connected me-1"></i> إدارة وضبط كافة القنوات
        </a>
      </div>

      @php
        $channelByPlatform = ($channels ?? collect())->keyBy('platform');
        $allPlatforms = [
          ['key' => 'whatsapp', 'name' => 'WhatsApp Cloud', 'icon' => 'bi-whatsapp', 'color' => 'text-success'],
          ['key' => 'telegram', 'name' => 'Telegram Bot', 'icon' => 'bi-send', 'color' => 'text-info'],
          ['key' => 'web', 'name' => 'Web Live Widget', 'icon' => 'bi-globe2', 'color' => 'text-gold'],
          ['key' => 'instagram', 'name' => 'Instagram Direct', 'icon' => 'bi-instagram', 'color' => 'text-danger'],
        ];
      @endphp

      <div class="row g-3 pt-2">
        @foreach($allPlatforms as $p)
          @php $ch = $channelByPlatform[$p['key']] ?? null; @endphp
          <div class="col-6 col-md-3">
            <div class="p-3 rounded-3 bg-black bg-opacity-40 border border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2">
                <i class="bi {{ $p['icon'] }} {{ $p['color'] }} fs-5"></i>
                <div>
                  <div class="fs-8 fw-bold text-white">{{ $p['name'] }}</div>
                  <small class="fs-9 {{ ($ch && $ch->isActive()) ? 'text-success' : 'text-white-50' }}">
                    <i class="bi bi-circle-fill fs-9 me-1"></i> {{ ($ch && $ch->isActive()) ? 'مفعلة ونشطة' : 'غير متصلة' }}
                  </small>
                </div>
              </div>
              <a href="{{ url('/channels') }}" class="text-gold fs-8" title="تعديل"><i class="bi bi-arrow-left"></i></a>
            </div>
          </div>
        @endforeach
      </div>
    </div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function handleProviderChange(provider) {
      const baseUrlSection = document.getElementById('baseUrlSection');
      const modelInput = document.getElementById('model_type');
      const models = {
        openai: 'gpt-4o-mini',
        gemini: 'gemini-1.5-flash',
        anthropic: 'claude-3-haiku-20240307',
        openai_compatible: 'moonshotai/Kimi-K2.6'
      };
      if (baseUrlSection) {
        baseUrlSection.style.display = provider === 'openai_compatible' ? 'block' : 'none';
      }
      if (modelInput && models[provider]) {
        modelInput.placeholder = models[provider];
      }
    }

    async function fetchModelsForProvider() {
      const btn = document.getElementById('fetchModelsBtn');
      const container = document.getElementById('modelInputContainer');
      const currentModel = document.getElementById('model_type')?.value || '';
      const checkedProvider = document.querySelector('input[name="ai_provider"]:checked')?.value || 'gemini';
      const apiKey = document.getElementById('ai_api_key')?.value || '';
      const baseUrl = document.getElementById('api_base_url')?.value || '';

      const originalBtnText = btn.innerHTML;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الجلب...';
      btn.disabled = true;

      try {
        const response = await fetch("{{ route('settings.fetch-models') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
          },
          body: JSON.stringify({
            ai_provider: checkedProvider,
            ai_api_key: apiKey,
            api_base_url: baseUrl
          })
        });

        const data = await response.json();

        if (data.success && data.models && data.models.length > 0) {
          let optionsHtml = '';
          data.models.forEach(m => {
            const isSelected = m === currentModel ? 'selected' : '';
            optionsHtml += `<option value="${m}" ${isSelected}>${m}</option>`;
          });

          container.innerHTML = `
            <select id="model_type" name="model_type" class="form-select form-control-dark" required>
              ${optionsHtml}
            </select>
          `;
          alert('تم جلب وتعبئة (' + data.models.length + ') نماذج متاحة بنجاح ✓');
        } else {
          alert(data.message || 'تعذر جلب النماذج، يرجى التحقق من صحة المفتاح والرابط.');
        }
      } catch (err) {
        alert('خطأ أثناء جلب النماذج: ' + err.message);
      } finally {
        btn.innerHTML = originalBtnText;
        btn.disabled = false;
      }
    }

    async function toggleBotStatus(checkbox) {
      const isChecked = checkbox.checked;
      const badge = document.getElementById('botActiveBadge');
      const text = document.getElementById('botActiveText');
      const icon = document.getElementById('botActiveIcon');
      const hiddenInput = document.getElementById('hiddenIsActiveInput');

      if (hiddenInput) hiddenInput.value = isChecked ? '1' : '0';

      // Update badge optimistically
      if (isChecked) {
        badge.className = 'badge bg-success bg-opacity-25 text-success border border-success px-3 py-2 fs-8 fw-bold';
        text.innerText = 'البوت مفعّل ونشط';
        if (icon) icon.className = 'bi bi-check-circle-fill me-1';
      } else {
        badge.className = 'badge bg-danger bg-opacity-25 text-danger border border-danger px-3 py-2 fs-8 fw-bold';
        text.innerText = 'البوت معطّل (إيقاف مؤقت)';
        if (icon) icon.className = 'bi bi-pause-circle-fill me-1';
      }

      try {
        const response = await fetch("{{ route('settings.toggle-bot') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
          },
          body: JSON.stringify({ is_active: isChecked })
        });

        const data = await response.json();
        if (!data.success) {
          throw new Error(data.message || 'فشل تحديث الحالة');
        }
      } catch (err) {
        alert('خطأ أثناء تحديث حالة البوت: ' + err.message);
        // Revert on error
        checkbox.checked = !isChecked;
        if (hiddenInput) hiddenInput.value = checkbox.checked ? '1' : '0';
        toggleBotStatus(checkbox);
      }
    }

    function togglePasswordVisibility(inputId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(inputId + '_eye');
      if (input) {
        if (input.type === 'password') {
          input.type = 'text';
          if (icon) icon.className = 'bi bi-eye-slash';
        } else {
          input.type = 'password';
          if (icon) icon.className = 'bi bi-eye';
        }
      }
    }
  </script>
</body>
</html>
