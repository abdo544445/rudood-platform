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
  <style>
    body { background-color: #0b0f19 !important; color: #fff !important; font-family: 'Cairo', sans-serif; min-height: 100vh; overflow: hidden; }
    .sidebar { width: 260px; background: rgba(15,23,42,0.95) !important; backdrop-filter: blur(16px); border-left: 1px solid rgba(212,175,55,0.2); min-height: 100vh; position: fixed; top: 0; right: 0; z-index: 1000; }
    .sidebar .nav-link { color: rgba(255,255,255,0.7) !important; padding: 12px 18px; border-radius: 10px; margin: 4px 10px; transition: all 0.3s; }
    .sidebar .nav-link:hover,.sidebar .nav-link.active { color: #000 !important; background: linear-gradient(135deg,#d4af37,#aa820a) !important; font-weight: bold; }
    .main-content { margin-right: 260px; padding: 20px 30px; height: 100vh; display: flex; flex-direction: column; }
    .chat-container { background: rgba(255,255,255,0.03) !important; backdrop-filter: blur(12px); border: 1px solid rgba(212,175,55,0.2) !important; border-radius: 16px; flex: 1; display: flex; overflow: hidden; }
    .chat-sidebar { width: 320px; border-left: 1px solid rgba(212,175,55,0.15); display: flex; flex-direction: column; background: rgba(15,23,42,0.4); }
    .chat-list { overflow-y: auto; flex: 1; }
    .chat-item { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); cursor: pointer; transition: all 0.2s; text-decoration: none; display: block; }
    .chat-item:hover,.chat-item.active { background: rgba(212,175,55,0.1); border-right: 4px solid #d4af37; }
    .chat-main { flex: 1; display: flex; flex-direction: column; background: rgba(11,15,25,0.5); }
    .chat-header { padding: 15px 20px; border-bottom: 1px solid rgba(212,175,55,0.15); background: rgba(15,23,42,0.6); }
    .chat-messages { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; }
    .message { max-width: 65%; padding: 12px 16px; border-radius: 14px; font-size: 0.95rem; line-height: 1.5; }
    .message-incoming { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); align-self: flex-start; border-bottom-right-radius: 2px; }
    .message-outgoing { background: linear-gradient(135deg,#d4af37,#aa820a); color: #000; font-weight: 600; align-self: flex-end; border-bottom-left-radius: 2px; }
    .message-bot { background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); align-self: flex-end; border-bottom-left-radius: 2px; color: #93c5fd; }
    .message-time { font-size: 0.75rem; opacity: 0.7; margin-top: 4px; display: block; }
    .chat-input-area { padding: 15px 20px; border-top: 1px solid rgba(212,175,55,0.15); background: rgba(15,23,42,0.8); }
    .custom-chat-input { background: rgba(15,23,42,0.9) !important; border: 1px solid rgba(212,175,55,0.3) !important; color: #fff !important; border-radius: 12px; padding: 10px 15px; }
    .custom-chat-input:focus { border-color: #d4af37 !important; box-shadow: 0 0 10px rgba(212,175,55,0.2) !important; }
    .btn-gold { background: linear-gradient(135deg,#d4af37,#aa820a) !important; color: #000 !important; border: none; font-weight: bold; }
    .avatar { width: 42px; height: 42px; border-radius: 50%; background: rgba(212,175,55,0.2); border: 1px solid #d4af37; display: flex; align-items: center; justify-content: center; color: #d4af37; font-weight: bold; flex-shrink: 0; }
    .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; opacity: 0.4; }
    #typingIndicator { display: none; font-size: 0.8rem; color: #d4af37; padding: 0 20px 5px; }
  </style>
</head>
<body>

  <!-- الشريط الجانبي -->
  <aside class="sidebar d-flex flex-column justify-content-between py-3">
    <div>
      <div class="px-4 mb-4 text-center">
        <a href="{{ url('/') }}">
          <img src="{{ asset('images/img.png') }}" alt="ردود" style="max-height:45px;">
        </a>
      </div>
      <ul class="nav nav-pills flex-column">
        <li class="nav-item"><a href="{{ url('/dashboard') }}" class="nav-link d-flex align-items-center gap-3"><i class="bi bi-grid-1x2-fill"></i> الرئيسية</a></li>
        <li class="nav-item"><a href="{{ url('/ai-manage') }}" class="nav-link d-flex align-items-center gap-3"><i class="bi bi-cpu-fill"></i> تدريب الذكاء الاصطناعي</a></li>
        <li class="nav-item"><a href="{{ url('/live-chat') }}" class="nav-link active d-flex align-items-center gap-3"><i class="bi bi-chat-dots-fill"></i> المحادثات المباشرة</a></li>
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

    <div class="mb-3 d-flex justify-content-between align-items-center flex-shrink-0">
      <div>
        <h4 class="fw-bold text-white mb-0"><i class="bi bi-chat-dots-fill text-gold me-2"></i>المحادثات المباشرة</h4>
        <p class="text-white-50 mb-0 fs-7">متابعة استفسارات العملاء في الوقت الحقيقي</p>
      </div>
      <span class="badge bg-success bg-opacity-25 text-success border border-success px-3 py-2 rounded-pill fs-7" id="connectionBadge">
        <i class="bi bi-circle-fill me-1 fs-8"></i> جاري الاتصال...
      </span>
    </div>

    <div class="chat-container">

      <!-- القائمة الجانبية للمحادثات -->
      <div class="chat-sidebar">
        <div class="p-3 border-bottom border-secondary border-opacity-25">
          <input type="text" id="conversationSearch" class="form-control custom-chat-input fs-7" placeholder="بحث في المحادثات...">
        </div>
        <div class="chat-list" id="conversationList">
          @forelse ($conversations as $conv)
          <a href="{{ url('/live-chat?conversation=' . $conv->id) }}"
             class="chat-item {{ $active && $active->id == $conv->id ? 'active' : '' }} d-flex align-items-center gap-3"
             data-id="{{ $conv->id }}">
            <div class="avatar">{{ mb_substr($conv->customer->name ?? 'ع', 0, 2) }}</div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="mb-0 text-white fs-7 fw-bold text-truncate">{{ $conv->customer->name ?? 'عميل جديد' }}</h6>
                <span class="text-white-50 fs-8">{{ $conv->updated_at->diffForHumans(null, true) }}</span>
              </div>
              <p class="mb-0 text-white-50 fs-8 text-truncate">
                {{ $conv->messages->first()?->content ?? 'لا توجد رسائل بعد' }}
              </p>
            </div>
          </a>
          @empty
          <div class="text-center text-white-50 py-5 px-3">
            <i class="bi bi-chat-square-dots display-4 d-block mb-3 opacity-40"></i>
            <p class="fs-7">لا توجد محادثات بعد</p>
          </div>
          @endforelse
        </div>
      </div>

      <!-- نافذة المحادثة الرئيسية -->
      <div class="chat-main">
        @if ($active)
        <!-- هيدر المحادثة -->
        <div class="chat-header d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-3">
            <div class="avatar">{{ mb_substr($active->customer->name ?? 'ع', 0, 2) }}</div>
            <div>
              <h6 class="mb-0 text-white fw-bold">{{ $active->customer->name ?? 'عميل جديد' }}</h6>
              <span class="badge bg-success mt-1" style="font-size:0.7rem;border-radius:50px;">
                {{ ucfirst($active->customer->platform ?? 'web') }}
              </span>
            </div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-warning rounded-pill px-3 fs-7">
              <i class="bi bi-person-fill-gear me-1"></i> تحويل موظف
            </button>
          </div>
        </div>

        <!-- منطقة الرسائل -->
        <div class="chat-messages" id="chatMessages">
          @forelse ($messages as $msg)
          <div class="message {{ $msg->sender_type === 'customer' ? 'message-incoming' : ($msg->sender_type === 'bot' ? 'message-bot' : 'message-outgoing') }}"
               data-id="{{ $msg->id }}">
            {{ $msg->content }}
            <span class="message-time {{ $msg->sender_type !== 'customer' ? 'text-dark' : 'text-white-50' }}">
              {{ $msg->created_at->format('H:i') }}
              @if ($msg->sender_type === 'bot')
               — رد تلقائي بالذكاء الاصطناعي 🤖
              @elseif ($msg->sender_type === 'agent')
               — أنت ✓
              @endif
            </span>
          </div>
          @empty
          <div class="empty-state text-center">
            <i class="bi bi-chat-left display-3 mb-3"></i>
            <p>لا توجد رسائل في هذه المحادثة بعد</p>
          </div>
          @endforelse
        </div>

        <!-- مؤشر الكتابة -->
        <div id="typingIndicator"><i class="bi bi-three-dots-fill me-1"></i> العميل يكتب...</div>

        <!-- صندوق الإرسال -->
        <div class="chat-input-area">
          <form id="sendForm" class="d-flex gap-2 align-items-center">
            @csrf
            <input type="text" id="messageInput" class="form-control custom-chat-input flex-grow-1"
              placeholder="اكتب ردك المباشر هنا..." autocomplete="off">
            <button type="submit" class="btn btn-gold px-4 rounded-3 d-flex align-items-center gap-2">
              <span>إرسال</span>
              <i class="bi bi-send-fill"></i>
            </button>
          </form>
        </div>

        @else
        <!-- Empty State: no active conversation -->
        <div class="empty-state">
          <i class="bi bi-chat-dots display-3 mb-3"></i>
          <h5 class="text-white-50">اختر محادثة من القائمة لعرضها</h5>
        </div>
        @endif
      </div>

    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Socket.io Client (from Node.js server) -->
  <script src="http://localhost:3000/socket.io/socket.io.js"></script>
  <script>
    // ─── Configuration (passed from Laravel) ───────────────────────────────
    const WORKSPACE_ID     = {{ auth()->user()->workspace_id }};
    const ACTIVE_CONV_ID   = {{ $active?->id ?? 'null' }};
    const SEND_URL         = "{{ $active ? url('/live-chat/' . $active->id . '/send') : '' }}";
    const CSRF_TOKEN       = "{{ csrf_token() }}";

    // ─── Socket.io Connection ───────────────────────────────────────────────
    let socket;
    const badge = document.getElementById('connectionBadge');

    try {
      socket = io('http://localhost:3000', { transports: ['websocket', 'polling'] });

      socket.on('connect', () => {
        // Join the workspace-scoped room
        socket.emit('join_workspace', WORKSPACE_ID);
        if (ACTIVE_CONV_ID) socket.emit('join_conversation', ACTIVE_CONV_ID);

        badge.innerHTML = '<i class="bi bi-circle-fill me-1 fs-8"></i> البوت متصل ويفحص المحادثات';
        badge.className = 'badge bg-success bg-opacity-25 text-success border border-success px-3 py-2 rounded-pill fs-7';
      });

      socket.on('disconnect', () => {
        badge.innerHTML = '<i class="bi bi-circle-fill me-1 fs-8"></i> غير متصل';
        badge.className = 'badge bg-danger bg-opacity-25 text-danger border border-danger px-3 py-2 rounded-pill fs-7';
      });

      // ─── Real-time new message ────────────────────────────────────────────
      socket.on('new_message', (data) => {
        if (data.conversation_id !== ACTIVE_CONV_ID) return;

        // Don't re-render our own messages (we add them optimistically)
        if (data.sender_type === 'agent' && data.is_self) return;

        appendMessage(data.content, data.sender_type, data.time);
      });

    } catch (e) {
      console.warn('Socket.io not available:', e);
    }

    // ─── Send Message ────────────────────────────────────────────────────────
    const sendForm   = document.getElementById('sendForm');
    const msgInput   = document.getElementById('messageInput');
    const chatWindow = document.getElementById('chatMessages');

    if (sendForm && ACTIVE_CONV_ID) {
      sendForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const content = msgInput.value.trim();
        if (!content) return;

        // Optimistic UI: append message immediately
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

    // ─── Helpers ─────────────────────────────────────────────────────────────
    function appendMessage(content, senderType, time) {
      if (!chatWindow) return;

      const cls = senderType === 'customer' ? 'message-incoming'
                : senderType === 'bot'      ? 'message-bot'
                :                             'message-outgoing';

      const label = senderType === 'bot'   ? ' — رد تلقائي 🤖'
                  : senderType === 'agent' ? ' — أنت ✓' : '';

      const timeClass = senderType === 'customer' ? 'text-white-50' : 'text-dark';

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

    // Auto-scroll on page load
    if (chatWindow) chatWindow.scrollTop = chatWindow.scrollHeight;

    // ─── Search filter ────────────────────────────────────────────────────────
    const searchInput = document.getElementById('conversationSearch');
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        document.querySelectorAll('.chat-item').forEach(item => {
          const name = item.querySelector('h6')?.textContent.toLowerCase() ?? '';
          item.style.display = name.includes(query) ? '' : 'none';
        });
      });
    }
  </script>
</body>
</html>
