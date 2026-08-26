@extends('layouts.app')

@section('title', 'مختبر الذكاء الاصطناعي (Playground) | منصة ردود')

@section('styles')
<style>
  .playground-wrapper {
    height: calc(100vh - 120px);
    display: flex;
    flex-direction: column;
  }
  .workbench-panel {
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(16px);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
  }
  .panel-header {
    padding: 14px 18px;
    border-bottom: 1px solid rgba(212, 175, 55, 0.18);
    background: rgba(15, 23, 42, 0.5);
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--gold);
  }
  .panel-body {
    padding: 16px;
    overflow-y: auto;
    flex: 1;
  }
  .custom-range {
    accent-color: var(--gold);
  }
  .chat-box {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: rgba(11, 15, 25, 0.4);
  }
  .bubble {
    max-width: 82%;
    padding: 12px 16px;
    border-radius: 14px;
    font-size: 0.92rem;
    line-height: 1.6;
    position: relative;
    word-wrap: break-word;
  }
  .bubble-user {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    align-self: flex-start;
    border-bottom-right-radius: 3px;
    color: #fff;
  }
  .bubble-bot {
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.12) 0%, rgba(15, 23, 42, 0.8) 100%);
    border: 1px solid rgba(212, 175, 55, 0.35);
    border-right: 3px solid var(--gold);
    align-self: flex-end;
    border-bottom-left-radius: 3px;
    color: #fff;
  }
  .bubble-bot p:last-child {
    margin-bottom: 0;
  }
  .tag-trigger {
    font-size: 0.72rem;
    padding: 2px 8px;
    border-radius: 50px;
    font-weight: 700;
  }
  .tag-auto {
    background: rgba(46, 204, 113, 0.2);
    color: #2ecc71;
    border: 1px solid #2ecc71;
  }
  .tag-ai {
    background: rgba(52, 152, 219, 0.2);
    color: #3498db;
    border: 1px solid #3498db;
  }
  .tag-fallback {
    background: rgba(241, 196, 15, 0.2);
    color: #f1c40f;
    border: 1px solid #f1c40f;
  }
  .preset-pill {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(212, 175, 55, 0.2);
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.78rem;
    padding: 5px 12px;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
  }
  .preset-pill:hover {
    background: rgba(212, 175, 55, 0.15);
    color: var(--gold);
    border-color: var(--gold);
  }
  .chunk-card {
    background: rgba(15, 23, 42, 0.9);
    border: 1px solid rgba(212, 175, 55, 0.25);
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 10px;
    font-size: 0.83rem;
  }
  .score-bar {
    height: 6px;
    border-radius: 3px;
    background: rgba(255,255,255,0.1);
    overflow: hidden;
  }
  .score-fill {
    height: 100%;
    background: linear-gradient(90deg, #d4af37, #2ecc71);
  }
  .form-control-dark, .form-select-dark {
    background: rgba(11, 15, 25, 0.7) !important;
    border: 1px solid rgba(212, 175, 55, 0.3) !important;
    color: #fff !important;
    font-size: 0.85rem;
    border-radius: 8px;
  }
  .form-control-dark:focus, .form-select-dark:focus {
    border-color: var(--gold) !important;
    box-shadow: 0 0 10px rgba(212, 175, 55, 0.25) !important;
  }
  .nav-tabs-custom {
    border-bottom: 1px solid rgba(212, 175, 55, 0.2);
  }
  .nav-tabs-custom .nav-link {
    color: var(--text-muted);
    border: none;
    border-bottom: 2px solid transparent;
    padding: 8px 14px;
    font-size: 0.85rem;
  }
  .nav-tabs-custom .nav-link.active {
    color: var(--gold);
    background: transparent;
    border-bottom: 2px solid var(--gold);
    font-weight: 700;
  }
  .typing-indicator {
    display: none;
    align-self: flex-end;
    background: rgba(15, 23, 42, 0.7);
    border: 1px solid rgba(212, 175, 55, 0.2);
    border-radius: 12px;
    padding: 8px 14px;
    font-size: 0.8rem;
    color: var(--gold);
  }
</style>
@endsection

@section('content')
<div class="playground-wrapper">
  <!-- Top Bar / Telemetry Status -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-25 flex-shrink-0">
    <div class="d-flex align-items-center gap-3">
      <div class="avatar" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(212,175,55,0.15); border: 1px solid var(--gold); display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 1.2rem;">
        <i class="bi bi-cpu-fill"></i>
      </div>
      <div>
        <h4 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
          مختبر الذكاء الاصطناعي (AI Playground)
          <span class="badge rounded-pill bg-success bg-opacity-25 text-success border border-success fs-8">مفعل للتدريب</span>
        </h4>
        <p class="text-white-50 mb-0 fs-8">اختبر ردود المساعد الذكي، عاين دقة استرجاع المقاطع (RAG)، واضبط المعاملات مباشرة</p>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
      <span class="badge bg-dark border border-secondary text-white-50 px-3 py-2 rounded-pill fs-8">
        <i class="bi bi-file-earmark-text text-gold me-1"></i> {{ $docsCount }} وثائق مدربة
      </span>
      <span class="badge bg-dark border border-secondary text-white-50 px-3 py-2 rounded-pill fs-8">
        <i class="bi bi-lightning-charge text-gold me-1"></i> {{ $rulesCount }} قواعد فورية
      </span>
      <button class="btn btn-outline-warning btn-sm rounded-pill px-3 fs-8" onclick="resetChatSession()">
        <i class="bi bi-arrow-clockwise me-1"></i> مسح الجلسة
      </button>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show fs-8 py-2 px-3 mb-3" style="background: rgba(46, 204, 113, 0.15); border: 1px solid #2ecc71; color: #2ecc71;">
      <i class="bi bi-check-circle-fill me-1"></i> {{ session('status') }}
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- 3-Column Workbench Grid -->
  <div class="row g-3 flex-grow-1 overflow-hidden">
    
    <!-- LEFT PANEL: Live Parameter Overrides -->
    <div class="col-lg-3 col-md-4 h-100">
      <div class="workbench-panel">
        <div class="panel-header">
          <span><i class="bi bi-sliders me-2"></i>معاملات النموذج</span>
          <span class="badge bg-warning bg-opacity-10 text-gold fs-9">مباشر</span>
        </div>
        <div class="panel-body">
          <form id="applyDefaultsForm" action="{{ route('playground.apply-defaults') }}" method="POST">
            @csrf
            
            <!-- Provider Selection -->
            <div class="mb-3">
              <label class="text-white-50 fs-8 d-block mb-1">مزود الذكاء الاصطناعي (Provider):</label>
              <select name="ai_provider" id="param_provider" class="form-select form-select-dark">
                <option value="gemini" {{ ($bot->ai_provider ?? 'gemini') === 'gemini' ? 'selected' : '' }}>Google Gemini</option>
                <option value="openai" {{ ($bot->ai_provider ?? '') === 'openai' ? 'selected' : '' }}>OpenAI (ChatGPT)</option>
                <option value="anthropic" {{ ($bot->ai_provider ?? '') === 'anthropic' ? 'selected' : '' }}>Anthropic Claude</option>
                <option value="openai_compatible" {{ ($bot->ai_provider ?? '') === 'openai_compatible' ? 'selected' : '' }}>OpenAI Compatible</option>
              </select>
            </div>

            <!-- Model Selection -->
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="text-white-50 fs-8 mb-0">اسم النموذج (Model ID):</label>
                <button type="button" class="btn btn-link text-gold p-0 fs-9 text-decoration-none" onclick="fetchPlaygroundModels()">
                  <i class="bi bi-arrow-repeat me-1"></i> جلب النماذج
                </button>
              </div>
              <div id="playgroundModelContainer">
                <input type="text" name="model_type" id="param_model" class="form-control form-control-dark" value="{{ $bot->model_type ?? 'gemini-1.5-flash' }}" placeholder="gemini-1.5-flash أو gpt-4o-mini">
              </div>
            </div>

            <!-- Temperature Slider -->
            <div class="mb-3">
              <div class="d-flex justify-content-between text-white-50 fs-8 mb-1">
                <span>الإبداع (Temperature):</span>
                <span id="temp_val" class="text-gold fw-bold">{{ $bot->temperature ?? 0.7 }}</span>
              </div>
              <input type="range" name="temperature" id="param_temp" class="form-range custom-range" min="0" max="1" step="0.05" value="{{ $bot->temperature ?? 0.7 }}" oninput="document.getElementById('temp_val').innerText = this.value">
              <div class="d-flex justify-content-between text-white-50 fs-9">
                <span>0.0 (دقيق ورسمي)</span>
                <span>1.0 (إبداعي)</span>
              </div>
            </div>

            <!-- Max Tokens -->
            <div class="mb-3">
              <div class="d-flex justify-content-between text-white-50 fs-8 mb-1">
                <span>الحد الأقصى للرموز (Max Tokens):</span>
                <span id="tokens_val" class="text-gold fw-bold">{{ $bot->max_tokens ?? 1000 }}</span>
              </div>
              <input type="range" name="max_tokens" id="param_tokens" class="form-range custom-range" min="100" max="3000" step="50" value="{{ $bot->max_tokens ?? 1000 }}" oninput="document.getElementById('tokens_val').innerText = this.value">
            </div>

            <!-- Tone Selection -->
            <div class="mb-3">
              <label class="text-white-50 fs-8 d-block mb-1">نبرة الرد (Tone):</label>
              <select name="bot_tone" id="param_tone" class="form-select form-select-dark">
                <option value="friendly" {{ ($bot->bot_tone ?? 'friendly') === 'friendly' ? 'selected' : '' }}>ودودة وترحيبية (Friendly)</option>
                <option value="formal" {{ ($bot->bot_tone ?? '') === 'formal' ? 'selected' : '' }}>رسمية واحترافية (Formal)</option>
                <option value="sales" {{ ($bot->bot_tone ?? '') === 'sales' ? 'selected' : '' }}>تسويقية تشجع على الشراء (Sales)</option>
              </select>
            </div>

            <!-- System Prompt Live Editor -->
            <div class="mb-3">
              <div class="d-flex justify-content-between text-white-50 fs-8 mb-1">
                <span>التعليمات التوجيهية (System Prompt):</span>
              </div>
              <textarea name="system_prompt" id="param_prompt" rows="3" class="form-control form-control-dark" style="font-size: 0.8rem; line-height: 1.4;">{{ $bot->system_prompt ?? 'أنت مساعد ذكاء اصطناعي مفيد ومهني يرد على العملاء بلطف ودقة باللغة العربية.' }}</textarea>
            </div>

            <!-- Diagnostic Switches -->
            <div class="p-2 rounded mb-3" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(212,175,55,0.15);">
              <div class="form-check form-switch fs-8 text-white mb-2">
                <input class="form-check-input" type="checkbox" name="enable_rag" id="toggle_rag" {{ ($bot->enable_rag ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="toggle_rag">تفعيل استرجاع المعرفة (RAG)</label>
              </div>
              <div class="form-check form-switch fs-8 text-white">
                <input class="form-check-input" type="checkbox" name="enable_auto_rules" id="toggle_rules" {{ ($bot->enable_auto_rules ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="toggle_rules">تفعيل القواعد الفورية (Auto-Rules)</label>
              </div>
            </div>

            <!-- Save as Default Button -->
            <button type="submit" class="btn btn-outline-warning w-100 btn-sm rounded-pill fw-bold fs-8">
              <i class="bi bi-save me-1"></i> حفظ كإعدادات افتراضية للبوت
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- CENTER PANEL: Interactive Chat Simulator -->
    <div class="col-lg-5 col-md-8 h-100">
      <div class="workbench-panel">
        <div class="panel-header">
          <span class="d-flex align-items-center gap-2">
            <i class="bi bi-chat-dots text-gold"></i> محاكاة المحادثة المباشرة
          </span>
          <span id="sessionMsgCount" class="badge rounded-pill bg-secondary bg-opacity-25 text-white fs-9">0 رسائل</span>
        </div>

        <!-- Presets Bar -->
        <div class="px-3 py-2 border-bottom border-secondary border-opacity-25 d-flex gap-2 overflow-auto" style="background: rgba(11,15,25,0.6);">
          <span class="text-white-50 fs-9 align-self-center me-1"><i class="bi bi-lightning-fill text-gold"></i> أسئلة تجريبية:</span>
          @foreach($presetPrompts as $preset)
            <span class="preset-pill" onclick="sendPresetPrompt('{{ addslashes($preset) }}')">{{ $preset }}</span>
          @endforeach
        </div>

        <!-- Chat Message Area -->
        <div class="chat-box" id="chatBox">
          <div class="text-center text-white-50 fs-8 my-auto" id="emptyChatNotice">
            <i class="bi bi-robot text-gold" style="font-size: 2.5rem; opacity: 0.4;"></i>
            <p class="mt-2 mb-1">المختبر جاهز للاختبار في بيئة تفاعلية متعددة الأدوار.</p>
            <span class="fs-9 opacity-75">اكتب استفساراً أدناه أو اختر من الأسئلة التجريبية بالأعلى.</span>
          </div>
        </div>

        <!-- Typing Indicator -->
        <div class="typing-indicator mx-3 mb-2" id="typingIndicator">
          <i class="bi bi-hourglass-split me-1 spinner-border spinner-border-sm"></i> جاري استرجاع المعرفة وتوليد الرد...
        </div>

        <!-- Chat Input Area -->
        <div class="p-3 border-top border-secondary border-opacity-25" style="background: rgba(15,23,42,0.9);">
          <div class="input-group">
            <input type="text" id="userInput" class="form-control custom-input fs-7" placeholder="اطرح سؤالاً لتجربة رد المساعد الذكي..." autocomplete="off" onkeydown="if(event.key==='Enter') sendMessage()">
            <button class="btn btn-gold px-4" id="sendBtn" onclick="sendMessage()">
              <i class="bi bi-send-fill me-1"></i> إرسال
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT PANEL: RAG & Telemetry Inspector (X-Ray) -->
    <div class="col-lg-4 col-md-12 h-100">
      <div class="workbench-panel">
        <div class="panel-header p-0">
          <ul class="nav nav-tabs nav-tabs-custom w-100 px-2 pt-2" id="inspectorTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="tab-rag" data-bs-toggle="tab" data-bs-target="#content-rag" type="button">
                <i class="bi bi-search me-1"></i> المقاطع المسترجعة (RAG)
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-prompt" data-bs-toggle="tab" data-bs-target="#content-prompt" type="button">
                <i class="bi bi-code-square me-1"></i> فحص التوجيه (Prompt)
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-logs" data-bs-toggle="tab" data-bs-target="#content-logs" type="button">
                <i class="bi bi-clock-history me-1"></i> السجل الحي
              </button>
            </li>
          </ul>
        </div>

        <div class="panel-body">
          <div class="tab-content" id="inspectorContent">
            
            <!-- Tab 1: RAG Chunks -->
            <div class="tab-pane fade show active" id="content-rag">
              <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                <span class="text-white-50 fs-8">حالة استرجاع المعرفة:</span>
                <span id="ragStatusBadge" class="badge bg-secondary bg-opacity-25 text-white-50 fs-9">في انتظار الرسالة الأولى</span>
              </div>

              <div id="chunksContainer">
                <div class="text-center text-white-50 fs-8 py-4 opacity-50">
                  <i class="bi bi-layers text-gold" style="font-size: 2rem;"></i>
                  <p class="mt-2 mb-0">ستظهر هنا المقاطع النصية المستخرجة من قاعدة المعرفة مع نسب التطابق الدلالي.</p>
                </div>
              </div>
            </div>

            <!-- Tab 2: Raw Combined Prompt -->
            <div class="tab-pane fade" id="content-prompt">
              <div class="mb-2 d-flex justify-content-between align-items-center">
                <span class="text-white-50 fs-8">التوجيه المجمع الكامل المحقون في النموذج:</span>
                <button class="btn btn-outline-secondary btn-sm py-0 px-2 fs-9 text-white" onclick="copyPrompt()">
                  <i class="bi bi-clipboard me-1"></i> نسخ
                </button>
              </div>
              <textarea id="rawPromptPreview" rows="12" class="form-control form-control-dark font-monospace fs-9" readonly placeholder="سيظهر هنا التوجيه الكامل بعد الإرسال..."></textarea>
            </div>

            <!-- Tab 3: Recent Telemetry Logs -->
            <div class="tab-pane fade" id="content-logs">
              <div class="text-white-50 fs-8 mb-2">أحدث 8 قرارات مسجلة للبوت:</div>
              <div class="d-flex flex-column gap-2" id="recentLogsList">
                @forelse($recentLogs as $log)
                  <div class="p-2 rounded" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); font-size: 0.8rem;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="badge {{ $log->trigger === 'auto_rule' ? 'tag-auto' : ($log->trigger === 'ai_api' ? 'tag-ai' : 'tag-fallback') }} fs-9">
                        {{ $log->trigger === 'auto_rule' ? 'قاعدة تلقائية' : ($log->trigger === 'ai_api' ? 'ذكاء اصطناعي' : 'رد احتياطي') }}
                      </span>
                      <span class="text-white-50 fs-9">{{ $log->response_time_ms }} ms</span>
                    </div>
                    <div class="text-white text-truncate fw-semibold mb-1">س: {{ $log->customer_message }}</div>
                    <div class="text-white-50 text-truncate fs-9">ج: {{ $log->bot_reply }}</div>
                  </div>
                @empty
                  <div class="text-center text-white-50 fs-8 py-3">لا توجد سجلات سابقة.</div>
                @endforelse
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<!-- Include marked.js for client-side markdown parsing -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
  let conversationHistory = [];

  function appendUserBubble(text) {
    const chatBox = document.getElementById('chatBox');
    const notice = document.getElementById('emptyChatNotice');
    if (notice) notice.remove();

    const bubble = document.createElement('div');
    bubble.className = 'bubble bubble-user';
    bubble.innerText = text;
    chatBox.appendChild(bubble);
    chatBox.scrollTop = chatBox.scrollHeight;

    conversationHistory.push({ role: 'user', content: text });
    updateMsgCount();
  }

  function appendBotBubble(data) {
    const chatBox = document.getElementById('chatBox');
    const bubble = document.createElement('div');
    bubble.className = 'bubble bubble-bot';

    // Trigger Badge
    let triggerBadge = '';
    if (data.trigger === 'auto_rule') {
      triggerBadge = `<span class="tag-trigger tag-auto mb-2 d-inline-block"><i class="bi bi-lightning-charge-fill me-1"></i> قاعدة تلقائية (Auto Rule)</span>`;
    } else if (data.trigger === 'ai_api') {
      triggerBadge = `<span class="tag-trigger tag-ai mb-2 d-inline-block"><i class="bi bi-cpu-fill me-1"></i> ذكاء اصطناعي (${data.provider} / ${data.model})</span>`;
    } else {
      triggerBadge = `<span class="tag-trigger tag-fallback mb-2 d-inline-block"><i class="bi bi-shield-exclamation me-1"></i> رد احتياطي (Fallback)</span>`;
    }

    const latencyBadge = `<span class="text-white-50 fs-9 ms-2"><i class="bi bi-stopwatch"></i> ${data.latency_ms} ms</span>`;
    const parsedMarkdown = marked.parse(data.reply || '');
    const errorNotice = (data.trigger === 'fallback' && data.error_detail) ? `
      <div class="mt-2 p-2 rounded-2 bg-danger bg-opacity-15 border border-danger border-opacity-25 text-danger fs-9">
        <i class="bi bi-info-circle-fill me-1"></i><strong>ملاحظة تشخيصية:</strong> ${data.error_detail}
      </div>
    ` : '';

    bubble.innerHTML = `
      <div class="d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-25 pb-1 mb-2">
        ${triggerBadge}
        ${latencyBadge}
      </div>
      <div class="bot-reply-content">${parsedMarkdown}</div>
      ${errorNotice}
    `;

    chatBox.appendChild(bubble);
    chatBox.scrollTop = chatBox.scrollHeight;

    conversationHistory.push({ role: 'assistant', content: data.reply });
    updateMsgCount();
  }

  function updateMsgCount() {
    document.getElementById('sessionMsgCount').innerText = `${conversationHistory.length} رسائل`;
  }

  function sendPresetPrompt(promptText) {
    document.getElementById('userInput').value = promptText;
    sendMessage();
  }

  async function sendMessage() {
    const input = document.getElementById('userInput');
    const sendBtn = document.getElementById('sendBtn');
    const text = input.value.trim();
    if (!text) return;

    appendUserBubble(text);
    input.value = '';
    input.disabled = true;
    sendBtn.disabled = true;

    document.getElementById('typingIndicator').style.display = 'block';

    const payload = {
      message: text,
      history: conversationHistory,
      enable_rag: document.getElementById('toggle_rag').checked,
      enable_rules: document.getElementById('toggle_rules').checked,
      overrides: {
        ai_provider: document.getElementById('param_provider').value,
        model_type: document.getElementById('param_model').value,
        temperature: parseFloat(document.getElementById('param_temp').value),
        max_tokens: parseInt(document.getElementById('param_tokens').value),
        bot_tone: document.getElementById('param_tone').value,
        system_prompt: document.getElementById('param_prompt').value,
      }
    };

    try {
      const response = await fetch("{{ route('playground.send') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json"
        },
        body: JSON.stringify(payload)
      });

      const data = await response.json();
      document.getElementById('typingIndicator').style.display = 'none';

      if (data.success) {
        appendBotBubble(data);
        updateInspector(data);
      } else {
        alert('حدث خطأ أثناء معالجة الطلب');
      }
    } catch (err) {
      document.getElementById('typingIndicator').style.display = 'none';
      alert('خطأ في الاتصال بالخادم: ' + err.message);
    } finally {
      input.disabled = false;
      sendBtn.disabled = false;
      input.focus();
    }
  }

  function updateInspector(data) {
    // 1. Update RAG status & chunks
    const statusBadge = document.getElementById('ragStatusBadge');
    const container = document.getElementById('chunksContainer');

    if (data.chunks && data.chunks.length > 0) {
      statusBadge.className = 'badge bg-success bg-opacity-25 text-success border border-success fs-9';
      statusBadge.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> تم استرجاع ${data.chunks.length} مقاطع تطابق`;

      let html = '';
      data.chunks.forEach((c, idx) => {
        const score = c.score || 10;
        const pct = Math.min(Math.round((score / 50) * 100), 100);
        html += `
          <div class="chunk-card">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-gold fw-bold fs-9">مقطع #${idx + 1}</span>
              <span class="text-white-50 fs-9">درجة التطابق: <strong class="text-white">${score}</strong></span>
            </div>
            <div class="score-bar mb-2">
              <div class="score-fill" style="width: ${pct}%;"></div>
            </div>
            <p class="text-white-50 mb-0 fs-9" style="line-height: 1.4;">${c.text}</p>
          </div>
        `;
      });
      container.innerHTML = html;
    } else {
      statusBadge.className = 'badge bg-secondary bg-opacity-25 text-white-50 fs-9';
      statusBadge.innerText = data.trigger === 'auto_rule' ? 'تمت المطابقة مع قاعدة فورية' : 'لم يتم استرجاع مقاطع (إجابة مباشرة)';
      container.innerHTML = `
        <div class="text-center text-white-50 fs-9 py-3 opacity-75">
          ${data.trigger === 'auto_rule' ? 'تم الرد مباشرة عبر محرك القواعد الفورية دون الحاجة للبحث بالمعرفة.' : 'لم تتطابق كلمات السؤال مع وثائق المعرفة المدربة بنسبة كافية.'}
        </div>
      `;
    }

    // 2. Update Raw System Prompt
    document.getElementById('rawPromptPreview').value = data.system_prompt_used || '';
  }

  function resetChatSession() {
    conversationHistory = [];
    document.getElementById('chatBox').innerHTML = `
      <div class="text-center text-white-50 fs-8 my-auto" id="emptyChatNotice">
        <i class="bi bi-robot text-gold" style="font-size: 2.5rem; opacity: 0.4;"></i>
        <p class="mt-2 mb-1">تم مسح الجلسة بنجاح.</p>
        <span class="fs-9 opacity-75">المختبر جاهز للاختبار في بيئة تفاعلية جديدة.</span>
      </div>
    `;
    updateMsgCount();
    document.getElementById('chunksContainer').innerHTML = `
      <div class="text-center text-white-50 fs-8 py-4 opacity-50">
        <i class="bi bi-layers text-gold" style="font-size: 2rem;"></i>
        <p class="mt-2 mb-0">ستظهر هنا المقاطع المستخرجة من قاعدة المعرفة.</p>
      </div>
    `;
    document.getElementById('ragStatusBadge').innerText = 'في انتظار الرسالة الأولى';
    document.getElementById('rawPromptPreview').value = '';
  }

  function copyPrompt() {
    const text = document.getElementById('rawPromptPreview').value;
    if (!text) return;
    navigator.clipboard.writeText(text);
    alert('تم نسخ التوجيه إلى الحافظة بنجاح!');
  }

  async function fetchPlaygroundModels() {
    const provider = document.getElementById('param_provider')?.value || 'gemini';
    const container = document.getElementById('playgroundModelContainer');
    const currentModel = document.getElementById('param_model')?.value || '';

    container.innerHTML = '<div class="text-warning fs-9 py-1"><span class="spinner-border spinner-border-sm me-1"></span> جاري جلب النماذج...</div>';

    try {
      const res = await fetch("{{ route('settings.fetch-models') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json"
        },
        body: JSON.stringify({ ai_provider: provider })
      });

      const data = await res.json();
      if (data.success && data.models && data.models.length > 0) {
        let optionsHtml = '';
        data.models.forEach(m => {
          const isSelected = m === currentModel ? 'selected' : '';
          optionsHtml += `<option value="${m}" ${isSelected}>${m}</option>`;
        });

        container.innerHTML = `
          <select name="model_type" id="param_model" class="form-select form-control-dark" required>
            ${optionsHtml}
          </select>
        `;
      } else {
        container.innerHTML = `<input type="text" name="model_type" id="param_model" class="form-control form-control-dark" value="${currentModel}" placeholder="اسم النموذج" required>`;
        alert(data.message || 'تعذر جلب النماذج.');
      }
    } catch (e) {
      container.innerHTML = `<input type="text" name="model_type" id="param_model" class="form-control form-control-dark" value="${currentModel}" placeholder="اسم النموذج" required>`;
      alert('خطأ في جلب النماذج: ' + e.message);
    }
  }
</script>
@endsection
