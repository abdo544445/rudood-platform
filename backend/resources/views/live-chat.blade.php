<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>المحادثات المباشرة | منصة ردود</title>
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  @include('layouts.partials.theme')
  <style>
    body { min-height: 100vh; overflow: hidden; }
    .main-content { margin-right: var(--sidebar-w, 255px); padding: 15px 22px; height: 100vh; display: flex; flex-direction: column; }
    .chat-container { background: rgba(255,255,255,0.035) !important; backdrop-filter: blur(14px); border: 1px solid rgba(212,175,55,0.2) !important; border-radius: 14px; flex: 1; display: flex; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
    
    /* 3-Column Split-Pane */
    .chat-sidebar { width: 290px; border-left: 1px solid rgba(212,175,55,0.15); display: flex; flex-direction: column; background: rgba(15,23,42,0.45); flex-shrink: 0; }
    .chat-list { overflow-y: auto; flex: 1; }
    .chat-item { padding: 11px 14px; border-bottom: 1px solid rgba(255,255,255,0.04); cursor: pointer; transition: all 0.2s; text-decoration: none; display: block; }
    .chat-item:hover, .chat-item.active { background: rgba(212,175,55,0.1); border-right: 3px solid #d4af37; }
    
    .chat-main { flex: 1; display: flex; flex-direction: column; background: rgba(11,15,25,0.55); min-width: 0; }
    .chat-header { padding: 12px 18px; border-bottom: 1px solid rgba(212,175,55,0.15); background: rgba(15,23,42,0.65); }
    .chat-messages { flex: 1; padding: 16px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; }
    .message { max-width: 70%; padding: 10px 14px; border-radius: 12px; font-size: 0.9rem; line-height: 1.45; }
    .message-incoming { background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.09); align-self: flex-start; border-bottom-right-radius: 2px; }
    .message-outgoing { background: linear-gradient(135deg,#d4af37,#aa820a); color: #000; font-weight: 600; align-self: flex-end; border-bottom-left-radius: 2px; }
    .message-bot { background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); align-self: flex-end; border-bottom-left-radius: 2px; color: #93c5fd; }
    .message-time { font-size: 0.72rem; opacity: 0.75; margin-top: 3px; display: block; }
    
    .chat-input-area { padding: 12px 18px; border-top: 1px solid rgba(212,175,55,0.15); background: rgba(15,23,42,0.85); position: relative; }
    .custom-chat-input { background: rgba(15,23,42,0.9) !important; border: 1px solid rgba(212,175,55,0.3) !important; color: #fff !important; border-radius: 10px; padding: 8px 12px; font-size: 0.88rem; }
    
    /* Right CRM Sidebar */
    .chat-crm-sidebar { width: 280px; border-right: 1px solid rgba(212,175,55,0.15); background: rgba(15,23,42,0.5); display: flex; flex-direction: column; overflow-y: auto; padding: 16px; flex-shrink: 0; }
    
    .avatar { width: 38px; height: 38px; border-radius: 50%; background: rgba(212,175,55,0.2); border: 1px solid #d4af37; display: flex; align-items: center; justify-content: center; color: #d4af37; font-weight: bold; flex-shrink: 0; font-size: 0.9rem; }
    .avatar-lg { width: 56px; height: 56px; font-size: 1.3rem; }
    
    /* Canned Replies Autocomplete Dropdown */
    #cannedDropdown { display: none; position: absolute; bottom: 65px; right: 18px; left: 18px; background: rgba(15,23,42,0.98); border: 1px solid rgba(212,175,55,0.4); border-radius: 12px; max-height: 200px; overflow-y: auto; z-index: 1050; box-shadow: 0 10px 25px rgba(0,0,0,0.6); }
    .canned-item { padding: 8px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); cursor: pointer; transition: all 0.15s; font-size: 0.85rem; }
    .canned-item:hover, .canned-item.active { background: rgba(212,175,55,0.15); color: #d4af37; }
    
    .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; opacity: 0.4; }
    #typingIndicator { display: none; font-size: 0.78rem; color: #d4af37; padding: 0 18px 4px; }
  </style>
</head>
<body>

  <!-- الشريط الجانبي -->
  @include('layouts.partials.sidebar')

  <!-- المحتوى الرئيسي -->
  <main class="main-content">

    <!-- شريط العنوان والمسارات والإجراءات -->
    <div class="mb-2 d-flex justify-content-between align-items-center flex-shrink-0">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <h5 class="fw-bold text-white mb-0"><i class="bi bi-chat-dots-fill text-gold me-2"></i>المحادثات المباشرة 2.0</h5>
          <span class="badge bg-gold text-dark fw-bold fs-8 rounded-pill">Live Hub</span>
        </div>
        <p class="text-white-50 mb-0 fs-8">متابعة استفسارات العملاء والتدخل البشري وتوثيق الملاحظات في الوقت الحقيقي</p>
      </div>

      <div class="d-flex align-items-center gap-2">
        <a href="{{ route('live-chat.export') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 text-white-50 fs-8">
          <i class="bi bi-download me-1 text-gold"></i> تصدير البيانات (CSV)
        </a>
        <span class="badge bg-success bg-opacity-25 text-success border border-success px-3 py-1 rounded-pill fs-8" id="connectionBadge">
          <i class="bi bi-circle-fill me-1 fs-9"></i> جاري الاتصال...
        </span>
      </div>
    </div>

    <!-- حاوية المحادثات المكونة من 3 أعمدة -->
    <div class="chat-container">

      <!-- 1. القائمة الجانبية للمحادثات (Left Sidebar) -->
      <div class="chat-sidebar">
        <div class="p-2 border-bottom border-secondary border-opacity-25">
          <input type="text" id="conversationSearch" class="form-control custom-chat-input fs-8" placeholder="🔍 بحث بالاسم أو الهاتف...">
        </div>
        <div class="chat-list" id="conversationList">
          @forelse ($conversations as $conv)
          <a href="{{ url('/live-chat?conversation=' . $conv->id) }}"
             class="chat-item {{ $active && $active->id == $conv->id ? 'active' : '' }} d-flex align-items-center gap-2"
             data-id="{{ $conv->id }}">
            <div class="avatar">{{ mb_substr($conv->customer->name ?? 'ع', 0, 2) }}</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 text-white fs-8 fw-bold text-truncate">{{ $conv->customer->name ?? 'عميل جديد' }}</h6>
                <span class="text-white-50 fs-9">{{ $conv->updated_at->diffForHumans(null, true) }}</span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <p class="mb-0 text-white-50 fs-9 text-truncate" style="max-width: 140px;">
                  {{ $conv->messages->first()?->content ?? 'لا توجد رسائل بعد' }}
                </p>
                <div class="d-flex align-items-center gap-1">
                  @if($conv->is_escalated)
                    <span class="badge bg-danger p-1 rounded-circle" title="محادثة متصعدة" style="width:8px;height:8px;"></span>
                  @endif
                  @if($conv->is_bot_paused)
                    <span class="badge bg-warning text-dark fs-9 p-0 px-1">بشري</span>
                  @endif
                  <span class="badge bg-secondary bg-opacity-25 text-white-50 fs-9 px-1">
                    {{ ucfirst(mb_substr($conv->customer->platform ?? 'web', 0, 2)) }}
                  </span>
                </div>
              </div>
            </div>
          </a>
          @empty
          <div class="text-center text-white-50 py-5 px-3">
            <i class="bi bi-chat-square-dots display-4 d-block mb-3 opacity-40"></i>
            <p class="fs-8">لا توجد محادثات مسجلة بعد</p>
          </div>
          @endforelse
        </div>
      </div>

      <!-- 2. نافذة المحادثة الرئيسية (Center Chat Pane) -->
      <div class="chat-main">
        @if ($active)
        <!-- هيدر المحادثة التفاعلي -->
        <div class="chat-header d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <div class="avatar">{{ mb_substr($active->customer->name ?? 'ع', 0, 2) }}</div>
            <div>
              <div class="d-flex align-items-center gap-2">
                <h6 class="mb-0 text-white fw-bold fs-7">{{ $active->customer->name ?? 'عميل جديد' }}</h6>
                <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25 fs-9 px-2">
                  {{ ucfirst($active->customer->platform ?? 'web') }}
                </span>
                @if($active->sentiment === 'urgent' || $active->is_escalated)
                  <span class="badge bg-danger text-white fs-9 px-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> متصعدة</span>
                @elseif($active->sentiment === 'positive')
                  <span class="badge bg-success text-white fs-9 px-2"><i class="bi bi-emoji-smile-fill me-1"></i> إيجابي</span>
                @elseif($active->sentiment === 'negative')
                  <span class="badge bg-warning text-dark fs-9 px-2"><i class="bi bi-emoji-frown-fill me-1"></i> مستاء</span>
                @endif
              </div>
              <span class="text-white-50 fs-9">{{ $active->customer->phone ?? 'محادثة عبر الويب' }}</span>
            </div>
          </div>

          <!-- أزرار التحكم بالبوت والتدخل البشري -->
          <div class="d-flex align-items-center gap-2">
            <button id="toggleBotBtn" class="btn btn-sm {{ $active->is_bot_paused ? 'btn-outline-success' : 'btn-outline-danger' }} rounded-pill px-3 fs-8 fw-bold">
              @if($active->is_bot_paused)
                <i class="bi bi-play-circle-fill me-1"></i> استئناف ردود البوت
              @else
                <i class="bi bi-pause-circle-fill me-1"></i> إيقاف البوت (تدخل بشري)
              @endif
            </button>
          </div>
        </div>

        <!-- منطقة الرسائل -->
        <div class="chat-messages" id="chatMessages">
          @forelse ($messages as $msg)
          <div class="message {{ $msg->sender_type === 'customer' ? 'message-incoming' : ($msg->sender_type === 'bot' ? 'message-bot' : 'message-outgoing') }}"
               data-id="{{ $msg->id }}">
            {{ $msg->content }}
            <span class="message-time {{ $msg->sender_type === 'agent' ? 'text-dark' : 'text-white-50' }}">
              {{ $msg->created_at->format('H:i') }}
              @if ($msg->sender_type === 'bot')
               — رد تلقائي بالذكاء الاصطناعي 🤖
              @elseif ($msg->sender_type === 'agent')
               — الموظف ✓
              @endif
            </span>
          </div>
          @empty
          <div class="empty-state text-center">
            <i class="bi bi-chat-left display-4 mb-2"></i>
            <p class="fs-8">لا توجد رسائل في هذه المحادثة بعد</p>
          </div>
          @endforelse
        </div>

        <!-- مؤشر الكتابة -->
        <div id="typingIndicator"><i class="bi bi-three-dots-fill me-1"></i> العميل يكتب...</div>

        <!-- شريط الردود السريعة الفورية (Quick Canned Chips) -->
        <div class="px-3 pt-2 pb-1 border-top border-secondary border-opacity-25 d-flex gap-1 overflow-x-auto align-items-center" style="background: rgba(15,23,42,0.7);">
          <span class="text-gold fs-9 fw-bold me-1 flex-shrink-0"><i class="bi bi-lightning-fill"></i> ردود سريعة:</span>
          @foreach($cannedReplies as $cr)
            <button type="button" class="btn btn-sm btn-dark border border-secondary border-opacity-50 text-white-50 fs-9 py-0 px-2 rounded-pill flex-shrink-0 canned-chip-btn" data-content="{{ $cr->content }}">
              {{ $cr->shortcut }} ({{ $cr->title }})
            </button>
          @endforeach
        </div>

        <!-- صندوق الإرسال وقائمة الإكمال التلقائي -->
        <div class="chat-input-area">
          <!-- Autocomplete Popup -->
          <div id="cannedDropdown">
            @foreach($cannedReplies as $cr)
              <div class="canned-item d-flex justify-content-between align-items-center" data-content="{{ $cr->content }}">
                <div>
                  <strong class="text-gold">{{ $cr->shortcut }}</strong> - <span class="text-white">{{ $cr->title }}</span>
                  <div class="text-white-50 fs-9 text-truncate" style="max-width:380px;">{{ $cr->content }}</div>
                </div>
                <span class="badge bg-dark text-white-50 fs-9">إدراج</span>
              </div>
            @endforeach
          </div>

          <form id="sendForm" class="d-flex gap-2 align-items-center m-0">
            @csrf
            <input type="text" id="messageInput" class="form-control custom-chat-input flex-grow-1"
              placeholder="اكتب ردك المباشر هنا (أو اكتب / للردود الجاهزة)..." autocomplete="off">
            <button type="submit" class="btn btn-gold px-3 py-1 rounded-3 d-flex align-items-center gap-1 fs-8">
              <span>إرسال</span>
              <i class="bi bi-send-fill"></i>
            </button>
          </form>
        </div>

        @else
        <!-- Empty State: no active conversation -->
        <div class="empty-state">
          <i class="bi bi-chat-dots display-3 mb-3"></i>
          <h6 class="text-white-50">اختر محادثة من القائمة لعرض التفاصيل</h6>
        </div>
        @endif
      </div>

      <!-- 3. عمود بطاقة العميل والملاحظات (Right Mini CRM Sidebar) -->
      @if ($active)
      <div class="chat-crm-sidebar">
        <div class="text-center pb-3 border-bottom border-secondary border-opacity-25 mb-3">
          <div class="avatar avatar-lg mx-auto mb-2">{{ mb_substr($active->customer->name ?? 'ع', 0, 2) }}</div>
          <h6 class="text-white fw-bold mb-1 fs-7">{{ $active->customer->name ?? 'عميل جديد' }}</h6>
          <span class="text-white-50 fs-9 d-block">{{ $active->customer->phone ?? 'لا يوجد رقم مسجل' }}</span>
          <span class="badge bg-secondary bg-opacity-25 text-white-50 fs-9 mt-1">
            بدأت: {{ $active->created_at->format('Y-m-d') }}
          </span>
        </div>

        <!-- بطاقة حالة المشاعر والتصعيد -->
        <div class="mb-3">
          <label class="form-label text-gold fs-9 mb-1"><i class="bi bi-activity me-1"></i>تحليل المشاعر والتصعيد</label>
          <div class="p-2 rounded-3 border border-secondary border-opacity-25 bg-black bg-opacity-30 fs-8">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-white-50">المشاعر:</span>
              <span class="fw-bold text-white">{{ $active->sentiment == 'urgent' ? '🚨 شديدة الأهمية' : ($active->sentiment == 'negative' ? '⚠️ غير راضي' : ($active->sentiment == 'positive' ? '😊 سعيد' : 'محايد')) }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-white-50">التصعيد:</span>
              <span class="badge {{ $active->is_escalated ? 'bg-danger' : 'bg-success' }} fs-9">{{ $active->is_escalated ? 'متصعدة للإدارة' : 'طبيعية' }}</span>
            </div>
            @if($active->escalation_reason)
              <div class="mt-2 text-danger fs-9 border-top border-secondary border-opacity-25 pt-1">
                <strong>السبب:</strong> {{ $active->escalation_reason }}
              </div>
            @endif
          </div>
        </div>

        <!-- بطاقة الوسوم والملاحظات الداخلية -->
        <form id="notesForm" class="flex-grow-1 d-flex flex-direction-column flex-column">
          @csrf
          <div class="mb-3">
            <label class="form-label text-gold fs-9 mb-1"><i class="bi bi-tags-fill me-1"></i>وسوم العميل (مفصولة بفواصل)</label>
            <input type="text" id="tagsInput" class="form-control custom-chat-input fs-8"
                   placeholder="مثال: VIP, عميل دائم, مهتم بالعروض"
                   value="{{ is_array($active->tags) ? implode(', ', $active->tags) : '' }}">
          </div>

          <div class="mb-3 flex-grow-1 d-flex flex-column">
            <label class="form-label text-gold fs-9 mb-1"><i class="bi bi-journal-text me-1"></i>ملاحظات الموظفين الخاصة</label>
            <textarea id="notesTextarea" class="form-control custom-chat-input fs-8 flex-grow-1" style="min-height: 100px;"
                      placeholder="اكتب ملاحظات داخلية لا يراها العميل...">{{ $active->notes }}</textarea>
          </div>

          <button type="button" id="saveNotesBtn" class="btn btn-sm btn-gold w-100 rounded-pill py-1 fs-8">
            <i class="bi bi-save me-1"></i> حفظ الملاحظات والوسوم
          </button>
        </form>
      </div>
      @endif

    </div>
  </main>

  <!-- Global Command Palette Modal -->
  @include('layouts.partials.command-palette')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Socket.io Client -->
  <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
  <script>
    const WORKSPACE_ID     = {{ auth()->user()->workspace_id }};
    const ACTIVE_CONV_ID   = {{ $active?->id ?? 'null' }};
    const SEND_URL         = "{{ $active ? url('/live-chat/' . $active->id . '/send') : '' }}";
    const TOGGLE_BOT_URL   = "{{ $active ? url('/live-chat/' . $active->id . '/toggle-bot') : '' }}";
    const NOTES_URL        = "{{ $active ? url('/live-chat/' . $active->id . '/notes') : '' }}";
    const CSRF_TOKEN       = "{{ csrf_token() }}";
    const WS_URL           = "{{ config('services.websocket_url') }}" || (window.location.protocol + '//' + window.location.hostname + ':3000');

    // ─── Web Audio API Notification Chime ─────────────────────────────────────
    function playNotificationSound() {
      try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
        osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.15); // A5
        gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.3);
      } catch (e) {}
    }

    // ─── Socket.io Connection ───────────────────────────────────────────────
    let socket;
    const badge = document.getElementById('connectionBadge');

    try {
      socket = io(WS_URL, { transports: ['websocket', 'polling'] });

      socket.on('connect', () => {
        socket.emit('join_workspace', WORKSPACE_ID);
        if (ACTIVE_CONV_ID) socket.emit('join_conversation', ACTIVE_CONV_ID);

        badge.innerHTML = '<i class="bi bi-circle-fill me-1 fs-9"></i> البوت متصل ويفحص المحادثات';
        badge.className = 'badge bg-success bg-opacity-25 text-success border border-success px-3 py-1 rounded-pill fs-8';
      });

      socket.on('disconnect', () => {
        badge.innerHTML = '<i class="bi bi-circle-fill me-1 fs-9"></i> غير متصل';
        badge.className = 'badge bg-danger bg-opacity-25 text-danger border border-danger px-3 py-1 rounded-pill fs-8';
      });

      socket.on('new_message', (data) => {
        if (data.conversation_id !== ACTIVE_CONV_ID) {
          playNotificationSound();
          return;
        }

        if (data.sender_type === 'agent' && data.is_self) return;

        appendMessage(data.content, data.sender_type, data.time);
        playNotificationSound();
      });

    } catch (e) {
      console.warn('Socket.io not available:', e);
    }

    // ─── Send Message ────────────────────────────────────────────────────────
    const sendForm   = document.getElementById('sendForm');
    const msgInput   = document.getElementById('messageInput');
    const chatWindow = document.getElementById('chatMessages');
    const cannedDropdown = document.getElementById('cannedDropdown');

    if (sendForm && ACTIVE_CONV_ID) {
      sendForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const content = msgInput.value.trim();
        if (!content) return;

        cannedDropdown.style.display = 'none';
        appendMessage(content, 'agent', new Date().toLocaleTimeString('ar', { hour: '2-digit', minute: '2-digit' }));
        msgInput.value = '';

        try {
          await fetch(SEND_URL, {
            method:  'POST',
            headers: {
              'Content-Type':     'application/json',
              'X-CSRF-TOKEN':     CSRF_TOKEN,
              'Accept':           'application/json',
            },
            body: JSON.stringify({ content }),
          });
        } catch (err) {
          console.error('Send failed:', err);
        }
      });
    }

    // ─── Canned Slash Dropdown Autocomplete ───────────────────────────────────
    if (msgInput && cannedDropdown) {
      msgInput.addEventListener('input', (e) => {
        const val = e.target.value;
        if (val.startsWith('/')) {
          cannedDropdown.style.display = 'block';
          const query = val.toLowerCase();
          document.querySelectorAll('.canned-item').forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(query) ? 'flex' : 'none';
          });
        } else {
          cannedDropdown.style.display = 'none';
        }
      });

      document.querySelectorAll('.canned-item').forEach(item => {
        item.addEventListener('click', () => {
          msgInput.value = item.getAttribute('data-content');
          cannedDropdown.style.display = 'none';
          msgInput.focus();
        });
      });

      document.querySelectorAll('.canned-chip-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          msgInput.value = btn.getAttribute('data-content');
          msgInput.focus();
        });
      });
    }

    // ─── Human Takeover (Toggle Bot) ─────────────────────────────────────────
    const toggleBotBtn = document.getElementById('toggleBotBtn');
    if (toggleBotBtn && ACTIVE_CONV_ID) {
      toggleBotBtn.addEventListener('click', async () => {
        try {
          const res = await fetch(TOGGLE_BOT_URL, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': CSRF_TOKEN,
              'Accept':       'application/json',
            },
          });
          const data = await res.json();
          if (data.success) {
            if (data.is_bot_paused) {
              toggleBotBtn.className = 'btn btn-sm btn-outline-success rounded-pill px-3 fs-8 fw-bold';
              toggleBotBtn.innerHTML = '<i class="bi bi-play-circle-fill me-1"></i> استئناف ردود البوت';
            } else {
              toggleBotBtn.className = 'btn btn-sm btn-outline-danger rounded-pill px-3 fs-8 fw-bold';
              toggleBotBtn.innerHTML = '<i class="bi bi-pause-circle-fill me-1"></i> إيقاف البوت (تدخل بشري)';
            }
          }
        } catch (err) {
          console.error(err);
        }
      });
    }

    // ─── Save Notes & Tags ───────────────────────────────────────────────────
    const saveNotesBtn = document.getElementById('saveNotesBtn');
    if (saveNotesBtn && ACTIVE_CONV_ID) {
      saveNotesBtn.addEventListener('click', async () => {
        const notes = document.getElementById('notesTextarea').value;
        const tags  = document.getElementById('tagsInput').value;

        saveNotesBtn.disabled = true;
        saveNotesBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...';

        try {
          await fetch(NOTES_URL, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': CSRF_TOKEN,
              'Accept':       'application/json',
            },
            body: JSON.stringify({ notes, tags }),
          });
          saveNotesBtn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> تم الحفظ بنجاح ✓';
          setTimeout(() => {
            saveNotesBtn.disabled = false;
            saveNotesBtn.innerHTML = '<i class="bi bi-save me-1"></i> حفظ الملاحظات والوسوم';
          }, 1500);
        } catch (e) {
          saveNotesBtn.disabled = false;
          saveNotesBtn.innerHTML = '<i class="bi bi-save me-1"></i> حفظ الملاحظات والوسوم';
        }
      });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    function appendMessage(content, senderType, time) {
      if (!chatWindow) return;

      const cls = senderType === 'customer' ? 'message-incoming'
                : senderType === 'bot'      ? 'message-bot'
                :                             'message-outgoing';

      const label = senderType === 'bot'   ? ' — رد تلقائي 🤖'
                  : senderType === 'agent' ? ' — أنت ✓' : '';

      const timeClass = senderType === 'agent' ? 'text-dark' : 'text-white-50';

      const div = document.createElement('div');
      div.className = `message ${cls}`;
      div.innerHTML = `${escapeHtml(content)}<span class="message-time ${timeClass}">${time}${label}</span>`;
      chatWindow.appendChild(div);
      chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    function escapeHtml(str) {
      const d = document.createElement('div');
      d.textContent = str;
      return d.innerHTML;
    }

    if (chatWindow) chatWindow.scrollTop = chatWindow.scrollHeight;

    // Search filter
    const searchInput = document.getElementById('conversationSearch');
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        document.querySelectorAll('.chat-item').forEach(item => {
          const text = item.textContent.toLowerCase();
          item.style.display = text.includes(query) ? '' : 'none';
        });
      });
    }
  </script>
</body>
</html>
