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

    /* ── Interactive Modal: WhatsApp Tab Pills ─────────────────────────────── */
    #waInteractiveTabs .nav-link {
      color: rgba(255, 255, 255, 0.75) !important;
      background: rgba(255, 255, 255, 0.05) !important;
      border: 1px solid rgba(255, 255, 255, 0.1) !important;
      transition: all 0.2s ease;
      font-weight: 700 !important;
    }
    #waInteractiveTabs .nav-link:hover:not(.active) {
      color: #ffffff !important;
      background: rgba(255, 255, 255, 0.1) !important;
      border-color: rgba(255, 255, 255, 0.25) !important;
    }
    #waInteractiveTabs .nav-link.active {
      background: linear-gradient(135deg, rgba(212, 175, 55, 0.3), rgba(212, 175, 55, 0.15)) !important;
      color: #d4af37 !important;
      border: 1px solid rgba(212, 175, 55, 0.6) !important;
      box-shadow: 0 2px 12px rgba(212, 175, 55, 0.2) !important;
    }
    /* ── Interactive message rendered inside chat bubble ───────────────────── */
    .whatsapp-buttons-container .btn {
      font-weight: 700 !important;
    }
    .whatsapp-list-container {
      font-size: 0.82rem;
    }
    .whatsapp-carousel-container .card {
      background: rgba(15, 23, 42, 0.9) !important;
      border-color: rgba(212, 175, 55, 0.2) !important;
    }
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

          <!-- أزرار التحكم بالبوت والتنبيهات والتدخل البشري -->
          <div class="d-flex align-items-center gap-2">
            <button type="button" id="toggleNotificationsBtn" class="btn btn-sm btn-outline-secondary border-secondary border-opacity-40 text-white-50 rounded-pill px-3 py-1 fs-9 d-flex align-items-center gap-1" title="تفعيل / كتم التنبيهات الصوتية والمكتبية">
              <i class="bi bi-bell-fill text-gold" id="notifIcon"></i>
              <span id="notifLabel" class="d-none d-sm-inline">التنبيهات: مفعلة</span>
            </button>

            @if($active->status === 'resolved')
              <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-30 rounded-pill px-3 py-1 fs-9 d-flex align-items-center gap-1">
                <i class="bi bi-check-circle-fill"></i> تم الحل والإنهاء ✓
              </span>
            @else
              <button type="button" id="resolveConvBtn" class="btn btn-sm btn-outline-success border-success border-opacity-50 text-success rounded-pill px-3 fs-9 fw-bold d-flex align-items-center gap-1">
                <i class="bi bi-check2-circle"></i> إنهاء والتقييم
              </button>
            @endif

            <button id="toggleBotBtn" class="btn btn-sm {{ $active->is_bot_paused ? 'btn-outline-success' : 'btn-outline-danger' }} rounded-pill px-3 fs-9 fw-bold">
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
          @if (!empty($active->context_summary))
          <div class="alert alert-dark border border-gold border-opacity-25 rounded-3 py-2 px-3 mb-3 fs-9 text-gold d-flex align-items-center gap-2" style="background: rgba(212,175,55,0.08);">
            <i class="bi bi-stars fs-6"></i>
            <div>
              <span class="fw-bold">ملخص السياق التراكمي للذكاء الاصطناعي:</span>
              <span class="text-white-50 ms-1">{{ $active->context_summary }}</span>
            </div>
          </div>
          @endif

          @forelse ($messages as $msg)
          <div class="message {{ $msg->sender_type === 'customer' ? 'message-incoming' : ($msg->sender_type === 'bot' ? 'message-bot' : 'message-outgoing') }}"
               data-id="{{ $msg->id }}">
            @if (($msg->media_type ?? 'text') === 'audio' || str_contains($msg->content, '🎙️'))
            <div class="d-inline-flex align-items-center gap-2 mb-1 px-2 py-1 rounded-pill bg-dark border border-secondary border-opacity-50 fs-9 text-gold">
              <i class="bi bi-mic-fill text-danger"></i>
              <span>رسالة صوتية مفرّغة بالذكاء الاصطناعي</span>
            </div>
            @endif

            @if ($msg->media_type === 'image' && !empty($msg->media_url))
            <div class="chat-attachment-img mt-1 mb-2">
              <a href="{{ $msg->media_url }}" target="_blank" class="d-inline-block position-relative rounded-3 overflow-hidden border border-secondary border-opacity-30">
                <img src="{{ $msg->media_url }}" class="img-fluid rounded-3" style="max-height: 200px; max-width: 280px; object-fit: cover;" alt="{{ $msg->file_name ?? 'صورة مرفقة' }}">
              </a>
            </div>
            @elseif ($msg->media_type === 'document' && !empty($msg->media_url))
            <div class="chat-attachment-doc mt-1 mb-2 p-2 rounded-3 bg-black bg-opacity-40 border border-secondary border-opacity-30 d-flex align-items-center justify-content-between gap-3" style="max-width: 320px;">
              <div class="d-flex align-items-center gap-2 text-truncate">
                <i class="bi bi-file-earmark-text-fill text-danger fs-4"></i>
                <div class="text-truncate">
                  <div class="text-white fs-9 fw-bold text-truncate">{{ $msg->file_name ?? 'مستند مرفق' }}</div>
                  <small class="text-white-50" style="font-size: 0.72rem;">{{ $msg->file_size ? round($msg->file_size / 1024, 1) . ' KB' : 'ملف مرفق' }}</small>
                </div>
              </div>
              <a href="{{ $msg->media_url }}" target="_blank" download class="btn btn-sm btn-dark border border-secondary border-opacity-50 text-gold fs-9 rounded-pill px-2 py-1 flex-shrink-0">
                <i class="bi bi-download me-1"></i> تحميل
              </a>
            </div>
            @endif

            <div>{{ $msg->content }}</div>

            @if ($msg->interactive_type === 'button' && !empty($msg->interactive_data))
            <div class="whatsapp-buttons-container d-flex flex-wrap gap-2 mt-2 pt-2 border-top border-white border-opacity-10">
              @foreach((array)$msg->interactive_data as $b)
                @php
                  $bId = is_array($b) ? ($b['id'] ?? '') : '';
                  $bTitle = is_array($b) ? ($b['title'] ?? $bId) : $b;
                  $isCsat = str_starts_with($bId, 'csat_');
                  $score = $isCsat ? (int)substr($bId, 5) : 5;
                @endphp
                @if($isCsat)
                  <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 fs-9 d-inline-flex align-items-center gap-1 border-warning border-opacity-40 text-warning bg-warning bg-opacity-10 shadow-sm csat-interactive-btn" onclick="submitCsatScore({{ $active->id }}, {{ $score }})">
                    <i class="bi bi-star-fill fs-9"></i> {{ $bTitle }}
                  </button>
                @else
                  <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fs-9 d-inline-flex align-items-center gap-1 border-success border-opacity-40 text-success bg-success bg-opacity-10 shadow-sm disabled">
                    <i class="bi bi-cursor-fill fs-9"></i> {{ $bTitle }}
                  </button>
                @endif
              @endforeach
            </div>
            @elseif ($msg->interactive_type === 'list' && !empty($msg->interactive_data))
            <div class="whatsapp-list-container mt-2 p-2 rounded-3 bg-black bg-opacity-40 border border-secondary border-opacity-30">
              <div class="d-flex align-items-center justify-content-between text-success fs-9 fw-bold mb-2 pb-1 border-bottom border-secondary border-opacity-25">
                <span><i class="bi bi-list-ul me-1"></i> قائمة خيارات واتساب التفاعلية (List Menu):</span>
                <span class="badge bg-dark text-white-50 border border-secondary border-opacity-25">واتساب كلاود</span>
              </div>
              @foreach((array)$msg->interactive_data as $sec)
                <div class="text-gold fs-9 fw-bold mt-1 mb-1">{{ $sec['title'] ?? 'خيارات' }}</div>
                <div class="d-flex flex-column gap-1 mb-1">
                  @foreach($sec['rows'] ?? [] as $r)
                    <div class="p-2 rounded bg-dark bg-opacity-60 border border-secondary border-opacity-20 d-flex justify-content-between align-items-center">
                      <div>
                        <div class="text-white fs-9 fw-bold">{{ $r['title'] }}</div>
                        @if(!empty($r['description']))<div class="text-white-50" style="font-size: 0.75rem;">{{ $r['description'] }}</div>@endif
                      </div>
                      <i class="bi bi-chevron-left text-success fs-9"></i>
                    </div>
                  @endforeach
                </div>
              @endforeach
            </div>
            @elseif ($msg->interactive_type === 'carousel' && !empty($msg->interactive_data))
            <div class="whatsapp-carousel-container mt-2">
              <div class="text-success fs-9 fw-bold mb-1"><i class="bi bi-grid-3x3-gap-fill me-1"></i> كتالوج بطاقات المنتجات التفاعلي:</div>
              <div class="d-flex gap-2 overflow-x-auto pb-2" style="scrollbar-width: thin;">
                @foreach((array)$msg->interactive_data as $card)
                  <div class="card bg-dark border border-secondary border-opacity-40 rounded-3 shadow-sm flex-shrink-0" style="width: 210px;">
                    @if(!empty($card['image_url']))
                      <img src="{{ $card['image_url'] }}" class="card-img-top rounded-top-3" style="height: 110px; object-fit: cover;" alt="{{ $card['name'] ?? $card['title'] }}">
                    @endif
                    <div class="card-body p-2 d-flex flex-column">
                      <div class="fw-bold text-white fs-9 text-truncate mb-1">{{ $card['name'] ?? $card['title'] }}</div>
                      <div class="text-white-50 fs-9 mb-2" style="font-size: 0.75rem; line-height: 1.3; height: 32px; overflow: hidden;">
                        {{ $card['description'] }}
                      </div>
                      <div class="d-flex align-items-center justify-content-between mt-auto">
                        <span class="text-gold fw-bold fs-9">{{ $card['price'] }} {{ $card['currency'] ?? 'ر.س' }}</span>
                        <a href="{{ $card['checkout_url'] ?? '#' }}" target="_blank" class="btn btn-sm btn-success rounded-pill py-0 px-2 fs-9">
                          {{ $card['button_text'] ?? 'طلب 🛒' }}
                        </a>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
            @endif

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
            <label for="chatAttachmentInput" class="btn btn-outline-secondary border-secondary border-opacity-40 text-white-50 rounded-3 px-2 py-1 fs-8 d-flex align-items-center mb-0 cursor-pointer flex-shrink-0" title="إرفاق صورة أو مستند PDF / فاتورة">
              <i class="bi bi-paperclip fs-6"></i>
              <input type="file" id="chatAttachmentInput" accept="image/*,.pdf,.doc,.docx,.txt,.csv" class="d-none">
            </label>
            <button type="button" class="btn btn-outline-success border-success border-opacity-50 text-success rounded-3 px-2 py-1 d-flex align-items-center gap-1 fs-8 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#waInteractiveModal" title="إرسال رسالة تفاعلية (أزرار / قوائم / كتالوج)">
              <i class="bi bi-whatsapp"></i> <span class="d-none d-md-inline">رسالة تفاعلية</span>
            </button>
            <input type="text" id="messageInput" class="form-control custom-chat-input flex-grow-1"
              placeholder="اكتب ردك المباشر هنا (أو اكتب / للردود الجاهزة)..." autocomplete="off">
            <button type="submit" class="btn btn-gold px-3 py-1 rounded-3 d-flex align-items-center gap-1 fs-8 flex-shrink-0">
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

        <!-- بطاقة تقييم رضا العميل (CSAT) -->
        <div class="mb-3">
          <label class="form-label text-gold fs-9 mb-1"><i class="bi bi-star-fill me-1"></i>تقييم رضا العميل (CSAT)</label>
          <div class="p-2 rounded-3 border border-secondary border-opacity-25 bg-black bg-opacity-30 fs-8" id="csatCrmCard">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-white-50">التقييم:</span>
              <span id="csatScoreDisplay" class="fw-bold text-warning">
                @if($active->csat_score)
                  {{ str_repeat('⭐️', $active->csat_score) }} ({{ $active->csat_score }}/5)
                @else
                  <span class="text-white-50 fs-9">لم يتم التقييم بعد</span>
                @endif
              </span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-white-50">حالة المعالجة:</span>
              <span class="badge {{ $active->status === 'resolved' ? 'bg-success' : 'bg-secondary' }} fs-9" id="csatStatusBadge">
                {{ $active->status === 'resolved' ? 'تم الحل والإنهاء ✓' : 'قيد المتابعة' }}
              </span>
            </div>
            @if($active->csat_feedback)
            <div class="mt-2 text-white-50 fs-9 border-top border-secondary border-opacity-25 pt-1">
              <strong class="text-white">ملاحظة العميل:</strong> {{ $active->csat_feedback }}
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

    // ─── Notification Settings & Web Audio Synthesizer ────────────────────────
    let notificationsEnabled = localStorage.getItem('rudood_notifs') !== 'false';
    const notifBtn   = document.getElementById('toggleNotificationsBtn');
    const notifIcon  = document.getElementById('notifIcon');
    const notifLabel = document.getElementById('notifLabel');

    function updateNotifUI() {
      if (notifIcon && notifLabel) {
        if (notificationsEnabled) {
          notifIcon.className = 'bi bi-bell-fill text-gold';
          notifLabel.innerText = 'التنبيهات: مفعلة';
        } else {
          notifIcon.className = 'bi bi-bell-slash text-white-50';
          notifLabel.innerText = 'التنبيهات: مكتومة';
        }
      }
    }
    updateNotifUI();

    if (notifBtn) {
      notifBtn.addEventListener('click', async () => {
        notificationsEnabled = !notificationsEnabled;
        localStorage.setItem('rudood_notifs', notificationsEnabled ? 'true' : 'false');
        updateNotifUI();

        if (notificationsEnabled && 'Notification' in window && Notification.permission !== 'granted') {
          await Notification.requestPermission();
        }

        if (notificationsEnabled) {
          playNotificationSound('message');
        }
      });
    }

    function playNotificationSound(type = 'message') {
      if (!notificationsEnabled) return;
      try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();

        if (type === 'escalation') {
          // Urgent 2-stage buzzer alert
          osc.type = 'triangle';
          osc.frequency.setValueAtTime(750, audioCtx.currentTime);
          osc.frequency.setValueAtTime(950, audioCtx.currentTime + 0.12);
          osc.frequency.setValueAtTime(750, audioCtx.currentTime + 0.24);
          gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
          gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.45);
          osc.connect(gain);
          gain.connect(audioCtx.destination);
          osc.start();
          osc.stop(audioCtx.currentTime + 0.45);
        } else {
          // Sweet harmonic chime
          osc.type = 'sine';
          osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
          osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.15); // A5
          gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
          gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
          osc.connect(gain);
          gain.connect(audioCtx.destination);
          osc.start();
          osc.stop(audioCtx.currentTime + 0.3);
        }
      } catch (e) {}
    }

    function showDesktopNotification(title, body, convId) {
      if (!notificationsEnabled || !('Notification' in window)) return;
      if (Notification.permission === 'granted') {
        const notif = new Notification(title, {
          body: body || 'وصلت رسالة جديدة في المحادثة',
          icon: '/favicon.ico',
        });
        notif.onclick = () => {
          window.focus();
          if (convId && convId !== ACTIVE_CONV_ID) {
            window.location.href = `/live-chat/${convId}`;
          }
        };
      }
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
        const isEscalated = data.is_escalated || data.sentiment === 'urgent';

        if (data.conversation_id !== ACTIVE_CONV_ID) {
          playNotificationSound(isEscalated ? 'escalation' : 'message');
          showDesktopNotification(
            isEscalated ? '🚨 تصعيد عاجل: عميل بحاجة لموظف!' : '📩 رسالة جديدة من العميل',
            data.content,
            data.conversation_id
          );
          return;
        }

        if (data.sender_type === 'agent' && data.is_self) return;

        appendMessage(
          data.content,
          data.sender_type,
          data.time,
          data.interactive_type,
          data.interactive_data,
          data.media_type,
          data.media_url,
          data.file_name,
          data.file_size
        );

        playNotificationSound(isEscalated ? 'escalation' : 'message');
        if (document.hidden) {
          showDesktopNotification(
            isEscalated ? '🚨 تصعيد عاجل: عميل بحاجة لموظف!' : '📩 رسالة واردة في المحادثة',
            data.content,
            data.conversation_id
          );
        }
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
    function appendMessage(content, senderType, time, interactiveType = null, interactiveData = null, mediaType = 'text', mediaUrl = null, fileName = null, fileSize = null) {
      if (!chatWindow) return;

      const cls = senderType === 'customer' ? 'message-incoming'
                : senderType === 'bot'      ? 'message-bot'
                :                             'message-outgoing';

      const label = senderType === 'bot'   ? ' — رد تلقائي 🤖'
                  : senderType === 'agent' ? ' — أنت ✓' : '';

      const timeClass = senderType === 'agent' ? 'text-dark' : 'text-white-50';

      const div = document.createElement('div');
      div.className = `message ${cls}`;

      let mediaHtml = '';
      if (mediaType === 'image' && mediaUrl) {
        mediaHtml = `<div class="chat-attachment-img mt-1 mb-2">
          <a href="${escapeHtml(mediaUrl)}" target="_blank" class="d-inline-block position-relative rounded-3 overflow-hidden border border-secondary border-opacity-30">
            <img src="${escapeHtml(mediaUrl)}" class="img-fluid rounded-3" style="max-height: 200px; max-width: 280px; object-fit: cover;">
          </a>
        </div>`;
      } else if (mediaType === 'document' && mediaUrl) {
        const sizeStr = fileSize ? (Math.round(fileSize / 1024 * 10) / 10) + ' KB' : 'ملف مرفق';
        mediaHtml = `<div class="chat-attachment-doc mt-1 mb-2 p-2 rounded-3 bg-black bg-opacity-40 border border-secondary border-opacity-30 d-flex align-items-center justify-content-between gap-3" style="max-width: 320px;">
          <div class="d-flex align-items-center gap-2 text-truncate">
            <i class="bi bi-file-earmark-text-fill text-danger fs-4"></i>
            <div class="text-truncate">
              <div class="text-white fs-9 fw-bold text-truncate">${escapeHtml(fileName || 'مستند مرفق')}</div>
              <small class="text-white-50" style="font-size: 0.72rem;">${sizeStr}</small>
            </div>
          </div>
          <a href="${escapeHtml(mediaUrl)}" target="_blank" download class="btn btn-sm btn-dark border border-secondary border-opacity-50 text-gold fs-9 rounded-pill px-2 py-1 flex-shrink-0">
            <i class="bi bi-download me-1"></i> تحميل
          </a>
        </div>`;
      }
      
      let interactiveHtml = '';
      if (interactiveType === 'button' && interactiveData) {
        const buttons = Array.isArray(interactiveData) ? interactiveData : [];
        interactiveHtml = `<div class="whatsapp-buttons-container d-flex flex-wrap gap-2 mt-2 pt-2 border-top border-white border-opacity-10">` +
          buttons.map(b => {
            const bId = typeof b === 'object' ? (b.id || '') : '';
            const bTitle = typeof b === 'object' ? (b.title || bId) : b;
            const isCsat = bId.startsWith('csat_');
            const score = isCsat ? parseInt(bId.replace('csat_', '')) : 5;
            if (isCsat && ACTIVE_CONV_ID) {
              return `<button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 fs-9 d-inline-flex align-items-center gap-1 border-warning border-opacity-40 text-warning bg-warning bg-opacity-10 shadow-sm" onclick="submitCsatScore(${ACTIVE_CONV_ID}, ${score})"><i class="bi bi-star-fill fs-9"></i> ${escapeHtml(bTitle)}</button>`;
            }
            return `<button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fs-9 d-inline-flex align-items-center gap-1 border-success border-opacity-40 text-success bg-success bg-opacity-10 shadow-sm disabled"><i class="bi bi-cursor-fill fs-9"></i> ${escapeHtml(bTitle)}</button>`;
          }).join('') +
          `</div>`;
      } else if (interactiveType === 'list' && interactiveData) {
        interactiveHtml = `<div class="whatsapp-list-container mt-2 p-2 rounded-3 bg-black bg-opacity-40 border border-secondary border-opacity-30">
          <div class="d-flex align-items-center justify-content-between text-success fs-9 fw-bold mb-2 pb-1 border-bottom border-secondary border-opacity-25">
            <span><i class="bi bi-list-ul me-1"></i> قائمة خيارات واتساب التفاعلية (List Menu):</span>
            <span class="badge bg-dark text-white-50 border border-secondary border-opacity-25">واتساب كلاود</span>
          </div>`;
        const sections = Array.isArray(interactiveData) ? interactiveData : [];
        sections.forEach(sec => {
          interactiveHtml += `<div class="text-gold fs-9 fw-bold mt-1 mb-1">${escapeHtml(sec.title || 'خيارات')}</div><div class="d-flex flex-column gap-1 mb-1">`;
          (sec.rows || []).forEach(r => {
            interactiveHtml += `<div class="p-2 rounded bg-dark bg-opacity-60 border border-secondary border-opacity-20 d-flex justify-content-between align-items-center">
              <div>
                <div class="text-white fs-9 fw-bold">${escapeHtml(r.title)}</div>
                ${r.description ? `<div class="text-white-50" style="font-size: 0.75rem;">${escapeHtml(r.description)}</div>` : ''}
              </div>
              <i class="bi bi-chevron-left text-success fs-9"></i>
            </div>`;
          });
          interactiveHtml += `</div>`;
        });
        interactiveHtml += `</div>`;
      } else if (interactiveType === 'carousel' && interactiveData) {
        const cards = Array.isArray(interactiveData) ? interactiveData : [];
        interactiveHtml = `<div class="whatsapp-carousel-container mt-2">
          <div class="text-success fs-9 fw-bold mb-1"><i class="bi bi-grid-3x3-gap-fill me-1"></i> كتالوج بطاقات المنتجات التفاعلي:</div>
          <div class="d-flex gap-2 overflow-x-auto pb-2" style="scrollbar-width: thin;">` +
          cards.map(c => `
            <div class="card bg-dark border border-secondary border-opacity-40 rounded-3 shadow-sm flex-shrink-0" style="width: 210px;">
              ${c.image_url ? `<img src="${escapeHtml(c.image_url)}" class="card-img-top rounded-top-3" style="height: 110px; object-fit: cover;">` : ''}
              <div class="card-body p-2 d-flex flex-column">
                <div class="fw-bold text-white fs-9 text-truncate mb-1">${escapeHtml(c.name || c.title || 'منتج')}</div>
                <div class="text-white-50 fs-9 mb-2" style="font-size: 0.75rem; line-height: 1.3; height: 32px; overflow: hidden;">${escapeHtml(c.description || '')}</div>
                <div class="d-flex align-items-center justify-content-between mt-auto">
                  <span class="text-gold fw-bold fs-9">${escapeHtml(c.price || '')} ${escapeHtml(c.currency || 'ر.س')}</span>
                  <a href="${escapeHtml(c.checkout_url || '#')}" target="_blank" class="btn btn-sm btn-success rounded-pill py-0 px-2 fs-9">${escapeHtml(c.button_text || 'طلب 🛒')}</a>
                </div>
              </div>
            </div>
          `).join('') +
          `</div></div>`;
      }

      div.innerHTML = `${mediaHtml}<div>${escapeHtml(content)}</div>${interactiveHtml}<span class="message-time ${timeClass}">${time}${label}</span>`;
      chatWindow.appendChild(div);
      chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    // ─── File Attachment Upload Handler ──────────────────────────────────────
    const fileInput = document.getElementById('chatAttachmentInput');
    if (fileInput && ACTIVE_CONV_ID) {
      fileInput.addEventListener('change', async () => {
        const file = fileInput.files[0];
        if (!file) return;

        if (file.size > 10 * 1024 * 1024) {
          alert('حجم الملف يتجاوز الحد المسموح به (10 ميجابايت).');
          return;
        }

        const formData = new FormData();
        formData.append('attachment', file);
        formData.append('_token', CSRF_TOKEN);

        const time = new Date().toLocaleTimeString('ar', { hour: '2-digit', minute: '2-digit' });
        const isImage = file.type.startsWith('image/');
        const tempUrl = URL.createObjectURL(file);
        
        appendMessage(
          isImage ? `📷 صورة: ${file.name}` : `📎 ملف: ${file.name}`,
          'agent',
          time,
          null,
          null,
          isImage ? 'image' : 'document',
          tempUrl,
          file.name,
          file.size
        );

        try {
          const res = await fetch(`/live-chat/${ACTIVE_CONV_ID}/attachment`, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData,
          });
          const data = await res.json();
          fileInput.value = '';
        } catch (e) {
          console.error(e);
        }
      });
    }

    // ─── Resolve Conversation & Automated CSAT Survey ────────────────────────
    const resolveConvBtn = document.getElementById('resolveConvBtn');
    if (resolveConvBtn && ACTIVE_CONV_ID) {
      resolveConvBtn.addEventListener('click', async () => {
        if (!confirm('هل أنت متأكد من إنهاء هذه المحادثة وإرسال استبيان الرضا (CSAT) للعميل؟')) return;

        resolveConvBtn.disabled = true;
        resolveConvBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الإنهاء...';

        try {
          const res = await fetch(`/live-chat/${ACTIVE_CONV_ID}/resolve`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': CSRF_TOKEN,
              'Accept':       'application/json',
            },
          });
          const data = await res.json();
          if (data.success) {
            resolveConvBtn.outerHTML = `<span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-30 rounded-pill px-3 py-1 fs-9 d-flex align-items-center gap-1"><i class="bi bi-check-circle-fill"></i> تم الحل والإنهاء ✓</span>`;
            const badgeEl = document.getElementById('csatStatusBadge');
            if (badgeEl) {
              badgeEl.className = 'badge bg-success fs-9';
              badgeEl.innerText = 'تم الحل والإنهاء ✓';
            }
            if (data.survey) {
              appendMessage(
                data.survey.content,
                'bot',
                new Date().toLocaleTimeString('ar', { hour: '2-digit', minute: '2-digit' }),
                data.survey.interactive_type,
                data.survey.interactive_data
              );
            }
          }
        } catch (e) {
          console.error(e);
          resolveConvBtn.disabled = false;
          resolveConvBtn.innerHTML = '<i class="bi bi-check2-circle"></i> إنهاء والتقييم';
        }
      });
    }

    // ─── Customer Satisfaction (CSAT) Submission ─────────────────────────────
    window.submitCsatScore = async function(convId, score) {
      try {
        const res = await fetch(`/live-chat/${convId}/csat`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept':       'application/json',
          },
          body: JSON.stringify({ score }),
        });
        const data = await res.json();
        if (data.success) {
          const scoreDisplay = document.getElementById('csatScoreDisplay');
          if (scoreDisplay) {
            scoreDisplay.innerHTML = '⭐️'.repeat(score) + ` (${score}/5)`;
          }
          appendMessage(
            `شكراً جزيلاً لتقييمك الكريم (${'⭐️'.repeat(score)} - ${score}/5)! يسعدنا دائماً خدمتك 🌸`,
            'bot',
            new Date().toLocaleTimeString('ar', { hour: '2-digit', minute: '2-digit' })
          );
        }
      } catch (e) {
        console.error(e);
      }
    };

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

    // ─── WhatsApp Interactive Modal Submission ───────────────────────────────
    const sendInteractiveActionBtn = document.getElementById('sendInteractiveActionBtn');
    if (sendInteractiveActionBtn && ACTIVE_CONV_ID) {
      sendInteractiveActionBtn.addEventListener('click', async () => {
        const activeTab = document.querySelector('#waInteractiveTabs .nav-link.active');
        const tabType = activeTab ? activeTab.getAttribute('data-type') : 'button';
        let payload = { type: tabType, content: '' };

        if (tabType === 'button') {
          payload.content = document.getElementById('waBtnPrompt').value.trim() || 'يرجى اختيار أحد الخيارات التالية للمتابعة:';
          payload.buttons = [
            { id: 'btn_1', title: document.getElementById('waBtn1').value.trim() || '📦 تتبع طلبي' },
            { id: 'btn_2', title: document.getElementById('waBtn2').value.trim() || '🛍️ المنتجات والعروض' },
            { id: 'btn_3', title: document.getElementById('waBtn3').value.trim() || '👨‍💼 موظف بشري' },
          ].filter(b => b.title.length > 0);
        } else if (tabType === 'list') {
          payload.content = document.getElementById('waListPrompt').value.trim() || 'مرحباً بك! يرجى اختيار الخدمة المطلوبة من القائمة:';
        } else if (tabType === 'carousel') {
          payload.content = document.getElementById('waCarouselPrompt').value.trim() || 'إليك أحدث العروض والمنتجات المميزة في المتجر:';
        }

        sendInteractiveActionBtn.disabled = true;
        sendInteractiveActionBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الإرسال...';

        try {
          const res = await fetch(`/live-chat/${ACTIVE_CONV_ID}/send-interactive`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': CSRF_TOKEN,
              'Accept':       'application/json',
            },
            body: JSON.stringify(payload),
          });
          const data = await res.json();
          if (data.success) {
            appendMessage(
              data.message.content,
              'agent',
              new Date().toLocaleTimeString('ar', { hour: '2-digit', minute: '2-digit' }),
              data.message.interactive_type,
              data.message.interactive_data
            );
            const modalEl = document.getElementById('waInteractiveModal');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
          }
        } catch (e) {
          console.error(e);
        } finally {
          sendInteractiveActionBtn.disabled = false;
          sendInteractiveActionBtn.innerHTML = '<i class="bi bi-send-fill me-1"></i> إرسال عبر واتساب';
        }
      });
    }
  </script>

  <!-- 🟢 Modal: إرسال رسالة واتساب تفاعلية (Interactive WhatsApp Modal) -->
  <div class="modal fade" id="waInteractiveModal" tabindex="-1" aria-labelledby="waInteractiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-dark border border-secondary border-opacity-25 rounded-4 shadow-lg text-white">
        <div class="modal-header border-bottom border-secondary border-opacity-25 p-3">
          <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-3 bg-success bg-opacity-20 text-success"><i class="bi bi-whatsapp fs-5"></i></div>
            <div>
              <h6 class="modal-title fw-bold fs-7 mb-0" id="waInteractiveModalLabel">إرسال رسالة تفاعلية عبر واتساب (WhatsApp Interactive)</h6>
              <small class="text-white-50 fs-9">أزرار الرد السريع، القوائم المنسدلة، وبطاقات المنتجات بدلاً من النص العادي</small>
            </div>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <ul class="nav nav-pills nav-fill mb-4 gap-2 bg-black bg-opacity-40 p-2 rounded-3 border border-secondary border-opacity-20" id="waInteractiveTabs" role="tablist">
            <li class="nav-item">
              <button class="nav-link active py-2 rounded-2 fs-9 fw-bold d-flex align-items-center justify-content-center gap-1" id="buttons-tab" data-bs-toggle="pill" data-bs-target="#tabButtons" type="button" data-type="button">
                <i class="bi bi-hand-index-thumb-fill"></i>
                <span>أزرار الرد السريع</span>
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link py-2 rounded-2 fs-9 fw-bold d-flex align-items-center justify-content-center gap-1" id="list-tab" data-bs-toggle="pill" data-bs-target="#tabList" type="button" data-type="list">
                <i class="bi bi-list-ul"></i>
                <span>القوائم التفاعلية</span>
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link py-2 rounded-2 fs-9 fw-bold d-flex align-items-center justify-content-center gap-1" id="carousel-tab" data-bs-toggle="pill" data-bs-target="#tabCarousel" type="button" data-type="carousel">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span>بطاقات الكتالوج</span>
              </button>
            </li>
          </ul>

          <div class="tab-content" id="waInteractiveTabContent">
            <!-- 1. أزرار الرد السريع -->
            <div class="tab-pane fade show active" id="tabButtons" role="tabpanel">
              <div class="mb-3">
                <label class="form-label text-white-50 fs-8">نص الرسالة الأساسي (Body):</label>
                <textarea id="waBtnPrompt" class="form-control bg-black bg-opacity-50 border-secondary border-opacity-25 text-white fs-8 rounded-3" rows="2">يسعدنا خدمتك دائماً! اختر الإجراء المناسب لك من الأزرار التالية:</textarea>
              </div>
              <label class="form-label text-white-50 fs-8">أزرار الرد السريع (بحد أقصى 3 أزرار معتمدة من Meta):</label>
              <div class="row g-2">
                <div class="col-md-4">
                  <input type="text" id="waBtn1" class="form-control bg-black bg-opacity-50 border-secondary border-opacity-25 text-white fs-8" value="📦 تتبع طلبي" maxlength="20">
                </div>
                <div class="col-md-4">
                  <input type="text" id="waBtn2" class="form-control bg-black bg-opacity-50 border-secondary border-opacity-25 text-white fs-8" value="🛍️ تصفح المنتجات" maxlength="20">
                </div>
                <div class="col-md-4">
                  <input type="text" id="waBtn3" class="form-control bg-black bg-opacity-50 border-secondary border-opacity-25 text-white fs-8" value="👨‍💼 التحدث مع موظف" maxlength="20">
                </div>
              </div>
              <div class="form-text text-white-50 fs-9 mt-2"><i class="bi bi-info-circle me-1"></i> عند نقر العميل على أي زر يتم إرسال الاختيار فورياً ومعالجته آلياً عبر الذكاء الاصطناعي.</div>
            </div>

            <!-- 2. القائمة المنسدلة -->
            <div class="tab-pane fade" id="tabList" role="tabpanel">
              <div class="mb-3">
                <label class="form-label text-white-50 fs-8">نص مقدمة القائمة (Body):</label>
                <textarea id="waListPrompt" class="form-control bg-black bg-opacity-50 border-secondary border-opacity-25 text-white fs-8 rounded-3" rows="2">أهلاً بك في متجرنا! يرجى اختيار الخدمة المطلوبة من القائمة أدناه:</textarea>
              </div>
              <div class="p-3 rounded-3 bg-black bg-opacity-40 border border-secondary border-opacity-25">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="text-gold fw-bold fs-8"><i class="bi bi-card-checklist me-1"></i> أقسام القائمة المنسدلة التلقائية:</span>
                  <span class="badge bg-success bg-opacity-20 text-success fs-9">جاهزة للإرسال</span>
                </div>
                <div class="row g-2 fs-9 text-white-50">
                  <div class="col-6">• 📦 تتبع حالة الشحنة برقم الطلب</div>
                  <div class="col-6">• 🚚 أوقات وأسعار التوصيل</div>
                  <div class="col-6">• 🔄 سياسة الاستبدال والاسترجاع</div>
                  <div class="col-6">• 🛍️ تصفح الكتالوج والعروض</div>
                  <div class="col-6">• 🔥 الخصومات الأسبوعية</div>
                  <div class="col-6">• 👨‍💼 التحويل لموظف خدمة العملاء</div>
                </div>
              </div>
            </div>

            <!-- 3. بطاقات الكتالوج -->
            <div class="tab-pane fade" id="tabCarousel" role="tabpanel">
              <div class="mb-3">
                <label class="form-label text-white-50 fs-8">نص الرسالة الأساسي (Body):</label>
                <textarea id="waCarouselPrompt" class="form-control bg-black bg-opacity-50 border-secondary border-opacity-25 text-white fs-8 rounded-3" rows="2">إليك باقة مختارة من أفضل منتجاتنا المتوفرة للشحن الفوري مع ضمان شامل:</textarea>
              </div>
              <div class="p-3 rounded-3 bg-black bg-opacity-40 border border-secondary border-opacity-25">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="text-info fw-bold fs-8"><i class="bi bi-images me-1"></i> البطاقات المتضمنة (3 منتجات):</span>
                  <span class="badge bg-info bg-opacity-20 text-info fs-9">كتالوج تفاعلي</span>
                </div>
                <div class="d-flex gap-2 overflow-x-auto">
                  <div class="p-2 rounded bg-dark border border-secondary border-opacity-25 text-center" style="width: 140px;">
                    <div class="fs-9 fw-bold text-white text-truncate">سماعات النخبة Pro</div>
                    <div class="text-gold fs-9 fw-bold">199.00 ر.س</div>
                  </div>
                  <div class="p-2 rounded bg-dark border border-secondary border-opacity-25 text-center" style="width: 140px;">
                    <div class="fs-9 fw-bold text-white text-truncate">ساعة رياضية AMOLED</div>
                    <div class="text-gold fs-9 fw-bold">299.00 ر.س</div>
                  </div>
                  <div class="p-2 rounded bg-dark border border-secondary border-opacity-25 text-center" style="width: 140px;">
                    <div class="fs-9 fw-bold text-white text-truncate">شاحن سريع 3 في 1</div>
                    <div class="text-gold fs-9 fw-bold">149.00 ر.س</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-25 p-3">
          <button type="button" class="btn btn-secondary rounded-pill px-3 fs-9" data-bs-dismiss="modal">إلغاء</button>
          <button type="button" id="sendInteractiveActionBtn" class="btn btn-success rounded-pill px-4 fs-9 fw-bold d-flex align-items-center gap-1">
            <i class="bi bi-send-fill me-1"></i> إرسال عبر واتساب
          </button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
