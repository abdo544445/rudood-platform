(function () {
  // Prevent double injection
  if (window.__RUDOOD_WIDGET_LOADED__) return;
  window.__RUDOOD_WIDGET_LOADED__ = true;

  // Extract config from script tag attributes
  const currentScript = document.currentScript || document.querySelector('script[src*="widget.js"]');
  const workspaceId   = currentScript ? currentScript.getAttribute('data-workspace') : '1';
  const customColor   = currentScript ? currentScript.getAttribute('data-color') : null;
  const customPos     = currentScript ? currentScript.getAttribute('data-position') : 'right';
  
  // Base API URL detection
  let apiBase = currentScript ? currentScript.getAttribute('data-api') : '';
  if (!apiBase) {
    const src = currentScript ? currentScript.getAttribute('src') : '';
    if (src && src.startsWith('http')) {
      const u = new URL(src);
      apiBase = u.origin;
    } else {
      apiBase = window.location.origin;
    }
  }

  // Session tokens in localStorage
  const STORAGE_KEY_CONV = 'rudood_conv_id_' + workspaceId;
  const STORAGE_KEY_USER = 'rudood_user_id_' + workspaceId;
  let convId = localStorage.getItem(STORAGE_KEY_CONV);
  let userId = localStorage.getItem(STORAGE_KEY_USER) || ('web_user_' + Math.random().toString(36).substring(2, 9));
  localStorage.setItem(STORAGE_KEY_USER, userId);

  let widgetConfig = {
    bot_name: 'مساعد المتجر الذكي',
    primary_color: customColor || '#d4af37',
    position: customPos || 'right',
    welcome_message: 'أهلاً بك! كيف أقدر أساعدك اليوم؟',
    is_active: true
  };

  // Inject Styles
  const styleEl = document.createElement('style');
  styleEl.textContent = `
    #rudood-widget-container {
      position: fixed;
      bottom: 24px;
      ${widgetConfig.position === 'left' ? 'left: 24px;' : 'right: 24px;'}
      z-index: 999999;
      font-family: 'Cairo', system-ui, -apple-system, sans-serif;
      direction: rtl;
    }
    #rudood-widget-launcher {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, ${widgetConfig.primary_color}, #967406);
      color: #000;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 8px 25px rgba(0,0,0,0.4), 0 0 15px rgba(212,175,55,0.4);
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    #rudood-widget-launcher:hover {
      transform: scale(1.08) translateY(-2px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.5), 0 0 25px rgba(212,175,55,0.6);
    }
    #rudood-widget-window {
      display: none;
      position: absolute;
      bottom: 75px;
      ${widgetConfig.position === 'left' ? 'left: 0;' : 'right: 0;'}
      width: 370px;
      max-width: calc(100vw - 32px);
      height: 520px;
      max-height: calc(100vh - 120px);
      background: rgba(15, 23, 42, 0.95);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(212, 175, 55, 0.35);
      border-radius: 18px;
      box-shadow: 0 20px 50px rgba(0,0,0,0.7);
      flex-direction: column;
      overflow: hidden;
      transform-origin: bottom ${widgetConfig.position};
      animation: rudoodPopIn 0.3s ease forwards;
    }
    @keyframes rudoodPopIn {
      0% { opacity: 0; transform: scale(0.85) translateY(20px); }
      100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    .rudood-header {
      background: rgba(11, 15, 25, 0.9);
      padding: 14px 16px;
      border-bottom: 1px solid rgba(212, 175, 55, 0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #fff;
    }
    .rudood-messages {
      flex: 1;
      padding: 14px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 10px;
      background: rgba(11, 15, 25, 0.6);
    }
    .rudood-msg {
      max-width: 80%;
      padding: 9px 13px;
      border-radius: 12px;
      font-size: 0.88rem;
      line-height: 1.45;
      word-break: break-word;
    }
    .rudood-msg-bot {
      background: rgba(59, 130, 246, 0.18);
      border: 1px solid rgba(59, 130, 246, 0.3);
      color: #bfdbfe;
      align-self: flex-start;
      border-bottom-right-radius: 2px;
    }
    .rudood-msg-user {
      background: linear-gradient(135deg, ${widgetConfig.primary_color}, #aa820a);
      color: #000;
      font-weight: 600;
      align-self: flex-end;
      border-bottom-left-radius: 2px;
    }
    .rudood-input-bar {
      padding: 10px 14px;
      border-top: 1px solid rgba(212, 175, 55, 0.2);
      background: rgba(15, 23, 42, 0.95);
      display: flex;
      gap: 8px;
      align-items: center;
    }
    .rudood-input {
      flex: 1;
      background: rgba(11, 15, 25, 0.8);
      border: 1px solid rgba(212, 175, 55, 0.3);
      color: #fff;
      padding: 8px 12px;
      border-radius: 10px;
      font-size: 0.88rem;
      outline: none;
    }
    .rudood-input:focus {
      border-color: ${widgetConfig.primary_color};
      box-shadow: 0 0 10px rgba(212, 175, 55, 0.25);
    }
    .rudood-send-btn {
      background: ${widgetConfig.primary_color};
      color: #000;
      border: none;
      border-radius: 10px;
      padding: 8px 14px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.2s;
    }
    .rudood-typing {
      font-size: 0.78rem;
      color: ${widgetConfig.primary_color};
      padding: 0 14px 6px;
      display: none;
    }
  `;
  document.head.appendChild(styleEl);

  // Inject DOM Elements
  const container = document.createElement('div');
  container.id = 'rudood-widget-container';
  container.innerHTML = `
    <!-- Launcher Button -->
    <div id="rudood-widget-launcher" title="محادثة مباشرة">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
      </svg>
    </div>

    <!-- Chat Modal Window -->
    <div id="rudood-widget-window">
      <div class="rudood-header">
        <div style="display:flex; align-items:center; gap:8px;">
          <div style="width:10px; height:10px; border-radius:50%; background:#22c55e; box-shadow:0 0 6px #22c55e;"></div>
          <div>
            <div style="font-weight:bold; font-size:0.9rem;" id="rudood-bot-name">مساعد المتجر الذكي</div>
            <div style="font-size:0.72rem; color:#94a3b8;">متصل بالذكاء الاصطناعي 24/7</div>
          </div>
        </div>
        <button id="rudood-close-btn" style="background:none; border:none; color:#94a3b8; font-size:1.2rem; cursor:pointer;">✕</button>
      </div>

      <div class="rudood-messages" id="rudood-msg-list">
        <!-- Messages -->
      </div>

      <div class="rudood-typing" id="rudood-typing-indicator">
        <span>الذكاء الاصطناعي يكتب ردك الآن...</span>
      </div>

      <form id="rudood-send-form" class="rudood-input-bar">
        <input type="text" id="rudood-input-field" class="rudood-input" placeholder="اكتب استفسارك هنا..." autocomplete="off" />
        <button type="submit" class="rudood-send-btn">إرسال</button>
      </form>
    </div>
  `;
  document.body.appendChild(container);

  // References
  const launcher = document.getElementById('rudood-widget-launcher');
  const chatWindow = document.getElementById('rudood-widget-window');
  const closeBtn = document.getElementById('rudood-close-btn');
  const sendForm = document.getElementById('rudood-send-form');
  const inputField = document.getElementById('rudood-input-field');
  const msgList = document.getElementById('rudood-msg-list');
  const typingInd = document.getElementById('rudood-typing-indicator');
  const botNameEl = document.getElementById('rudood-bot-name');

  // Toggle Visibility
  let isOpen = false;
  launcher.addEventListener('click', () => {
    isOpen = !isOpen;
    chatWindow.style.display = isOpen ? 'flex' : 'none';
    if (isOpen) {
      inputField.focus();
      if (msgList.children.length === 0) {
        appendMsg(widgetConfig.welcome_message, 'bot');
      }
    }
  });
  closeBtn.addEventListener('click', () => {
    isOpen = false;
    chatWindow.style.display = 'none';
  });

  // Fetch Config
  fetch(`${apiBase}/api/widget/config/${workspaceId}`)
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        widgetConfig = Object.assign(widgetConfig, data.config);
        botNameEl.textContent = widgetConfig.bot_name;
        if (msgList.children.length === 0) {
          appendMsg(widgetConfig.welcome_message, 'bot');
        }
      }
    })
    .catch(() => {});

  // Send Message
  sendForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const text = inputField.value.trim();
    if (!text) return;

    appendMsg(text, 'user');
    inputField.value = '';
    typingInd.style.display = 'block';
    msgList.scrollTop = msgList.scrollHeight;

    fetch(`${apiBase}/api/widget/message`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({
        workspace_id: workspaceId,
        conversation_id: convId,
        user_id: userId,
        message: text
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success && data.reply) {
        if (data.conversation_id) {
          convId = data.conversation_id;
          localStorage.setItem(STORAGE_KEY_CONV, convId);
        }

        // Typing Indicator Simulation (0.8s - 1.5s based on response length)
        const typingDelay = Math.min(1500, Math.max(800, Math.floor(data.reply.length * 12)));
        typingInd.innerHTML = '<span>المساعد يكتب الآن... ✍️</span>';
        typingInd.style.display = 'block';
        msgList.scrollTop = msgList.scrollHeight;

        setTimeout(() => {
          typingInd.style.display = 'none';
          appendMsg(data.reply, 'bot');

          // Check if response invites CSAT rating
          if (data.reply.includes('تقييم') || data.reply.includes('⭐️')) {
            appendCsatRatingWidget();
          }
        }, typingDelay);
      } else {
        typingInd.style.display = 'none';
      }
    })
    .catch(() => {
      typingInd.style.display = 'none';
      appendMsg('عذراً، حدث خطأ مؤقت في الاتصال. يرجى المحاولة مرة أخرى.', 'bot');
    });
  });

  function appendMsg(text, sender) {
    const div = document.createElement('div');
    div.className = `rudood-msg ${sender === 'user' ? 'rudood-msg-user' : 'rudood-msg-bot'}`;
    div.textContent = text;
    msgList.appendChild(div);
    msgList.scrollTop = msgList.scrollHeight;
  }

  function appendCsatRatingWidget() {
    const card = document.createElement('div');
    card.className = 'rudood-msg rudood-msg-bot';
    card.style.background = 'rgba(212,175,55,0.1)';
    card.style.border = '1px solid rgba(212,175,55,0.3)';
    card.innerHTML = `
      <div style="font-weight:bold; margin-bottom:6px; color:#d4af37; font-size:0.82rem;">تقييم مستوى الخدمة:</div>
      <div style="display:flex; justify-content:center; gap:6px; font-size:1.2rem; cursor:pointer;" id="rudood-stars-row">
        <span onclick="window.__rudoodSendCsat(1)">⭐️</span>
        <span onclick="window.__rudoodSendCsat(2)">⭐️</span>
        <span onclick="window.__rudoodSendCsat(3)">⭐️</span>
        <span onclick="window.__rudoodSendCsat(4)">⭐️</span>
        <span onclick="window.__rudoodSendCsat(5)">⭐️</span>
      </div>
    `;
    msgList.appendChild(card);
    msgList.scrollTop = msgList.scrollHeight;
  }

  window.__rudoodSendCsat = function(score) {
    if (!convId) return;
    const row = document.getElementById('rudood-stars-row');
    if (row) {
      row.innerHTML = `<span style="color:#d4af37; font-size:0.85rem;">تم تسجيل تقييمك (${'⭐️'.repeat(score)} - ${score}/5) بنجاح ✓</span>`;
    }
    fetch(`${apiBase}/api/widget/csat/${convId}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ score: score })
    }).then(r => r.json()).then(res => {
      if (res.success) {
        appendMsg(`شكراً جزيلاً لتقييمك الكريم (${score}/5)! نسعد بخدمتك دائماً 🌸`, 'bot');
      }
    }).catch(() => {});
  };

})();
