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

  <style>
    body {
      background-color: #0b0f19;
      color: #ffffff;
      font-family: 'Cairo', sans-serif;
      min-height: 100vh;
    }

    .sidebar {
      width: 260px;
      background: rgba(15, 23, 42, 0.8);
      backdrop-filter: blur(16px);
      border-left: 1px solid rgba(212, 175, 55, 0.2);
      min-height: 100vh;
      position: fixed;
      top: 0;
      right: 0;
      z-index: 1000;
    }

    .sidebar .nav-link {
      color: rgba(255, 255, 255, 0.7);
      padding: 12px 20px;
      border-radius: 10px;
      margin: 4px 15px;
      transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover, .sidebar .nav-link.active {
      color: #000;
      background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%);
      font-weight: bold;
    }

    .main-content {
      margin-right: 260px;
      padding: 30px;
    }

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
  <aside class="sidebar d-flex flex-column justify-content-between py-3">
    <div>
      <div class="px-4 mb-4 text-center">
        <a href="{{ url('/index') }}">
          <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" style="max-height: 45px;">
        </a>
      </div>

      <ul class="nav nav-pills flex-column">
        <li class="nav-item">
          <a href="{{ url('/dashboard') }}" class="nav-link d-flex align-items-center gap-3">
            <i class="bi bi-grid-1x2-fill"></i> الرئيسية
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/ai-manage') }}" class="nav-link d-flex align-items-center gap-3">
            <i class="bi bi-cpu-fill"></i> تدريب الذكاء الاصطناعي
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/live-chat') }}" class="nav-link d-flex align-items-center gap-3">
            <i class="bi bi-chat-dots-fill"></i> المحادثات المباشرة
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/settings') }}" class="nav-link active d-flex align-items-center gap-3">
            <i class="bi bi-gear-fill"></i> الإعدادات والقنوات
          </a>
        </li>
      </ul>
    </div>

    <div class="px-3">
      <a href="{{ url('/login') }}" class="btn btn-outline-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
      </a>
    </div>
  </aside>

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
          <h4 class="fw-bold text-white mb-1"><i class="bi bi-cpu text-gold me-2"></i>مزود الذكاء الاصطناعي</h4>
          <p class="text-white-50 mb-4 fs-7">خططتك محفوظة ومشفرة. يدعم OpenAI وGemini وAnthropic وأي مزود متوافق مع OpenAI.</p>

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

            <!-- رابط ال**نموذج** لكل مزود -->
            <div class="mb-3">
              <label for="model_type" class="form-label text-white-50 fs-7">اسم النموذج</label>
              <input type="text" id="model_type" name="model_type" class="form-control form-control-dark"
                value="{{ $bot->model_type }}" placeholder="gpt-4o-mini, gemini-1.5-pro, claude-3-haiku-20240307..." required>
            </div>

            <!-- مفتاح API -->
            <div class="mb-3">
              <label for="ai_api_key" class="form-label text-white-50 fs-7">مفتاح API</label>
              <input type="password" id="ai_api_key" name="ai_api_key" class="form-control form-control-dark"
                placeholder="{{ $bot->api_key_encrypted ? '•••••••• (تم الحفظ)' : 'أدخل مفتاح API...' }}" {{ $bot->api_key_encrypted ? '' : 'required' }}>
              <div class="form-text text-white-50 fs-8"><i class="bi bi-shield-lock text-gold"></i> مخزن بشكل مشفر في قاعدة البيانات</div>
            </div>

            <!-- Base URL (للمزودين المتوافقين فقط) -->
            <div class="mb-3" id="baseUrlSection" style="display: {{ $bot->ai_provider === 'openai_compatible' ? 'block' : 'none' }}">
              <label for="api_base_url" class="form-label text-white-50 fs-7">Base URL للمزود</label>
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
        openai_compatible: ''
      };
      baseUrlSection.style.display = provider === 'openai_compatible' ? 'block' : 'none';
      if (models[provider]) modelInput.placeholder = models[provider];
  </script>
</body>
</html>
