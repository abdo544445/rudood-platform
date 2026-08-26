/**
 * Rudood Platform - Landing Page Animation & Interactive Simulation Engine
 * Features:
 * 1. Animated Arabic "ردود" Typography (SVG Calligraphy Stroke + Gold Light Shimmer)
 * 2. Background Luminous Ambient Particle Canvas
 * 3. Hero Interactive Live Typing Chat Mockup with pulsing response badges
 * 4. Scroll-Triggered Animated Metric Number Counters
 * 5. 3D Card Tilt & Parallax Physics
 */

document.addEventListener('DOMContentLoaded', () => {

  // ─── 1. Background Ambient Particle Mesh Canvas ───────────────────────────
  const canvas = document.getElementById('ambientCanvas');
  if (canvas) {
    const ctx = canvas.getContext('2d');
    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);

    window.addEventListener('resize', () => {
      width = canvas.width = window.innerWidth;
      height = canvas.height = window.innerHeight;
    });

    const particles = [];
    const particleCount = Math.min(width < 768 ? 25 : 55, 60);

    for (let i = 0; i < particleCount; i++) {
      particles.push({
        x: Math.random() * width,
        y: Math.random() * height,
        radius: Math.random() * 2 + 0.8,
        vx: (Math.random() - 0.5) * 0.4,
        vy: (Math.random() - 0.5) * 0.4,
        alpha: Math.random() * 0.45 + 0.15,
        pulse: Math.random() * 0.02 + 0.01,
      });
    }

    let mouseX = width / 2;
    let mouseY = height / 2;

    window.addEventListener('mousemove', (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
    });

    function drawParticles() {
      ctx.clearRect(0, 0, width, height);

      // Draw connecting lines between nearby particles
      for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
          const dx = particles[i].x - particles[j].x;
          const dy = particles[i].y - particles[j].y;
          const dist = Math.sqrt(dx * dx + dy * dy);

          if (dist < 130) {
            ctx.beginPath();
            ctx.strokeStyle = `rgba(212, 175, 55, ${0.12 * (1 - dist / 130)})`;
            ctx.lineWidth = 0.8;
            ctx.moveTo(particles[i].x, particles[i].y);
            ctx.lineTo(particles[j].x, particles[j].y);
            ctx.stroke();
          }
        }
      }

      // Draw particle nodes
      particles.forEach((p) => {
        p.x += p.vx;
        p.y += p.vy;

        // Bounce at boundaries
        if (p.x < 0 || p.x > width) p.vx *= -1;
        if (p.y < 0 || p.y > height) p.vy *= -1;

        // Subtle attraction to mouse cursor
        const dx = mouseX - p.x;
        const dy = mouseY - p.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < 180) {
          p.x += dx * 0.003;
          p.y += dy * 0.003;
        }

        // Pulse alpha
        p.alpha += p.pulse;
        if (p.alpha > 0.65 || p.alpha < 0.1) p.pulse *= -1;

        ctx.beginPath();
        ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(212, 175, 55, ${p.alpha})`;
        ctx.shadowBlur = 8;
        ctx.shadowColor = '#d4af37';
        ctx.fill();
        ctx.shadowBlur = 0;
      });

      requestAnimationFrame(drawParticles);
    }
    drawParticles();
  }

  // ─── 2. Interactive Hero AI Typing Chat Mockup ────────────────────────────
  const mockChatContainer = document.getElementById('heroMockMessages');
  if (mockChatContainer) {
    const simulationScenarios = [
      {
        question: "السلام عليكم، هل متوفر عطر مسك الختام وكم مدة التوصيل للرياض؟",
        sender: "customer",
        customerName: "سارة العتيبي",
        reply: "وعليكم السلام يا أهلاً سارة! 🌸 نعم متوفر «مسك الختام» الفاخر بحجم 100 مل، والتوصيل داخل الرياض فوري خلال 24 ساعة فقط! نوفر لك كود خصم إضافي (WELCOME10).",
        speed: "0.2 ثانية",
        platform: "WhatsApp"
      },
      {
        question: "كيف أقدر أتتبع شحنتي رقم #98421؟",
        sender: "customer",
        customerName: "فهد الشمري",
        reply: "أهلاً فهد! 📦 شحنتك رقم #98421 تم تسليمها لشركة أرامكس وهي حالياً «في طريق التوزيع»، ورابط التتبع المباشر هو: https://track.rodood.sa/98421",
        speed: "0.3 ثانية",
        platform: "Telegram"
      },
      {
        question: "أبغى أسترجع الطلب، إيش الشروط وطريقة الاستبدال؟",
        sender: "customer",
        customerName: "نورة القحطاني",
        reply: "أهلاً نورة! 🔄 يسعدنا خدمتك، الاسترجاع والاستبدال مجاني تماماً خلال 14 يوماً من الشراء عبر بوليصة إرجاع مجانية نرسلها لك الآن على الواتساب فوراً.",
        speed: "0.1 ثانية",
        platform: "Web Chat"
      }
    ];

    let currentScenarioIdx = 0;

    async function runChatSimulation() {
      const scenario = simulationScenarios[currentScenarioIdx];
      currentScenarioIdx = (currentScenarioIdx + 1) % simulationScenarios.length;

      // 1. Clear previous chat
      mockChatContainer.innerHTML = '';

      // 2. Render Incoming Customer Message
      const custMsgEl = document.createElement('div');
      custMsgEl.className = 'mock-msg mock-msg-incoming animate-pop-in';
      custMsgEl.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-1">
          <strong class="text-white fs-8">${scenario.customerName}</strong>
          <span class="badge bg-secondary bg-opacity-50 text-white-50 fs-9">${scenario.platform}</span>
        </div>
        <div>${scenario.question}</div>
        <span class="mock-time">${new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })}</span>
      `;
      mockChatContainer.appendChild(custMsgEl);

      // 3. Show Typing Indicator
      await sleep(600);
      const typingEl = document.createElement('div');
      typingEl.className = 'mock-typing animate-fade-in';
      typingEl.id = 'activeMockTyping';
      typingEl.innerHTML = `
        <span class="text-gold fs-8 fw-bold me-2"><i class="bi bi-robot"></i> الذكاء الاصطناعي يكتب...</span>
        <div class="typing-dots"><span></span><span></span><span></span></div>
      `;
      mockChatContainer.appendChild(typingEl);
      mockChatContainer.scrollTop = mockChatContainer.scrollHeight;

      // 4. Render Bot Reply after simulated latency
      await sleep(1400);
      if (document.getElementById('activeMockTyping')) {
        document.getElementById('activeMockTyping').remove();
      }

      const botMsgEl = document.createElement('div');
      botMsgEl.className = 'mock-msg mock-msg-outgoing animate-pop-in';
      botMsgEl.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="badge bg-dark text-gold border border-warning border-opacity-25 fs-9"><i class="bi bi-cpu-fill me-1"></i> رد آلي ذكي</span>
          <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 fs-9">⚡ ${scenario.speed}</span>
        </div>
        <div>${scenario.reply}</div>
        <span class="mock-time text-dark">${new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })} — رد فوري ✓</span>
      `;
      mockChatContainer.appendChild(botMsgEl);
      mockChatContainer.scrollTop = mockChatContainer.scrollHeight;

      // Wait before next scenario
      setTimeout(runChatSimulation, 5500);
    }

    function sleep(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
    }

    setTimeout(runChatSimulation, 1000);
  }

  // ─── 3. Scroll-Triggered Animated Metric Counters ────────────────────────
  const counters = document.querySelectorAll('.stat-counter');
  let hasCounted = false;

  const countObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !hasCounted) {
        hasCounted = true;
        counters.forEach(counter => {
          const target = parseFloat(counter.getAttribute('data-target'));
          const decimals = parseInt(counter.getAttribute('data-decimals') || '0');
          const prefix = counter.getAttribute('data-prefix') || '';
          const suffix = counter.getAttribute('data-suffix') || '';
          const duration = 2000;
          const startTime = performance.now();

          function updateCounter(now) {
            const progress = Math.min((now - startTime) / duration, 1);
            // Ease-out cubic formula
            const easeOutProgress = 1 - Math.pow(1 - progress, 3);
            const currentVal = (easeOutProgress * target).toFixed(decimals);
            counter.textContent = `${prefix}${currentVal}${suffix}`;

            if (progress < 1) {
              requestAnimationFrame(updateCounter);
            } else {
              counter.textContent = `${prefix}${target}${suffix}`;
            }
          }
          requestAnimationFrame(updateCounter);
        });
      }
    });
  }, { threshold: 0.3 });

  const statsSection = document.querySelector('.features-bar-section') || document.querySelector('.stats-section');
  if (statsSection) {
    countObserver.observe(statsSection);
  }

  // ─── 4. 3D Card Tilt Effect on Mouse Move ─────────────────────────────────
  const tiltCards = document.querySelectorAll('.tilt-effect');
  tiltCards.forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const centerX = rect.width / 2;
      const centerY = rect.height / 2;
      const rotateX = ((y - centerY) / centerY) * -7;
      const rotateY = ((x - centerX) / centerX) * 7;

      card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
    });

    card.addEventListener('mouseleave', () => {
      card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
    });
  });

  // ─── 5. Scroll Reveal Intersection Observer ──────────────────────────────
  const revealElements = document.querySelectorAll('.reveal-on-scroll');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-revealed');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  revealElements.forEach(el => revealObserver.observe(el));

});
