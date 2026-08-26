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
          <h4 class="fw-bold text-white mb-4"><i class="bi bi-robot text-gold me-2"></i>تخصيص سلوك البوت</h4>
          
          <form action="{{ url('/settings/save-bot') }}" method="POST" id="botSettingsForm">
            @csrf
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

            <button type="submit" class="btn btn-gold px-4 rounded-pill">حفظ التغييرات</button>
          </form>
        </div>
      </div>

      <!-- إعدادات مزود الذكاء الاصطناعي -->
      <div class="col-lg-5">
        <div class="glass-card">
          @php $isSuperAdmin = auth()->user() && auth()->user()->isSuperAdmin(); @endphp
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="fw-bold text-white mb-0"><i class="bi bi-cpu text-gold me-2"></i>مزود الذكاء الاصطناعي</h4>
            @if($isSuperAdmin)
              <span class="badge bg-warning bg-opacity-25 text-warning border border-warning fs-8"><i class="bi bi-shield-lock-fill me-1"></i> صلاحية الإدارة العليا</span>
            @elseif($workspace->allow_custom_api_key ?? false)
              <span class="badge bg-success bg-opacity-25 text-success border border-success fs-8"><i class="bi bi-unlock-fill me-1"></i> مخصص (BYOK)</span>
            @else
              <span class="badge bg-secondary bg-opacity-25 text-white-50 border border-secondary fs-8"><i class="bi bi-lock-fill me-1"></i> خادم المنصة</span>
            @endif
          </div>

          @if($isSuperAdmin)
          <div class="alert alert-info py-2 px-3 mb-3 fs-8" style="background: rgba(52, 152, 219, 0.15); border: 1px solid #3498db; color: #3498db;">
            <i class="bi bi-shield-check me-1"></i> <strong>وضع المدير العام:</strong> لديك صلاحية تحكم مطلقة لتغيير المزود والمفتاح والنموذج لهذا المتجر.
          </div>
          @elseif(!($workspace->allow_custom_api_key ?? false))
          <div class="alert alert-warning py-2 px-3 mb-3 fs-8" style="background: rgba(212,175,55,0.1); border: 1px solid rgba(212,175,55,0.3); color: #d4af37;">
            <i class="bi bi-info-circle-fill me-1"></i> <strong>الذكاء الاصطناعي مفعل تلقائياً:</strong> يعمل حسابك عبر خوادم المنصة المركزية السريعة. للترقية واستخدام مفتاح مخصص خاص بك (BYOK)، يرجى التواصل مع الإدارة.
          </div>
          @else
          <p class="text-white-50 mb-3 fs-7">بياناتك محفوظة ومشفرة. يدعم OpenAI وGemini وAnthropic وأي مزود متوافق مع OpenAI.</p>
          @endif

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
                    {{ (!($workspace->allow_custom_api_key ?? false) && !$isSuperAdmin) ? 'disabled' : '' }}
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
                @if(($workspace->allow_custom_api_key ?? false) || $isSuperAdmin)
                <button type="button" class="btn btn-link text-gold p-0 fs-8 text-decoration-none" id="fetchModelsBtn" onclick="fetchModelsForProvider()">
                  <i class="bi bi-arrow-repeat me-1"></i> جلب النماذج المتاحة
                </button>
                @endif
              </div>
              <div id="modelInputContainer">
                <input type="text" id="model_type" name="model_type" class="form-control form-control-dark"
                  value="{{ $bot->model_type }}" placeholder="gpt-4o-mini, gemini-1.5-flash, moonshotai/Kimi-K2.6..." 
                  {{ (!($workspace->allow_custom_api_key ?? false) && !$isSuperAdmin) ? 'readonly' : 'required' }}>
              </div>
            </div>

            <!-- مفتاح API -->
            <div class="mb-3">
              <label for="ai_api_key" class="form-label text-white-50 fs-7">مفتاح API الخاص بك</label>
              <input type="password" id="ai_api_key" name="ai_api_key" class="form-control form-control-dark"
                placeholder="{{ $bot->api_key_encrypted ? '•••••••• (تم الحفظ)' : 'أدخل مفتاح API...' }}" 
                {{ (!($workspace->allow_custom_api_key ?? false) && !$isSuperAdmin) ? 'disabled' : '' }}>
              <div class="form-text text-white-50 fs-8"><i class="bi bi-shield-lock text-gold"></i> مخزن بشكل مشفر تماماً في قاعدة البيانات</div>
            </div>

            <!-- Base URL (للمزودين المتوافقين فقط) -->
            <div class="mb-3" id="baseUrlSection" style="display: {{ $bot->ai_provider === 'openai_compatible' ? 'block' : 'none' }}">
              <label for="api_base_url" class="form-label text-white-50 fs-7">Base URL للمزود</label>
              <input type="url" id="api_base_url" name="api_base_url" class="form-control form-control-dark"
                value="{{ $bot->api_base_url }}" placeholder="https://api.your-provider.com/v1"
                {{ (!($workspace->allow_custom_api_key ?? false) && !$isSuperAdmin) ? 'disabled' : '' }}>
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
  </script>
</body>
</html>
