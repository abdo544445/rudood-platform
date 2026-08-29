<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $maintenance['title'] ?? 'أعمال صيانة وتطوير مجدولة' }} - منصة ردود</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --font: 'Cairo', system-ui, -apple-system, sans-serif;
            --gold: #d4af37;
            --gold-dark: #aa820a;
            --gold-soft: rgba(212, 175, 55, 0.15);
            --bg-dark: #090d16;
            --card-bg: rgba(15, 23, 42, 0.75);
            --card-border: rgba(212, 175, 55, 0.25);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: var(--font);
        }

        body {
            background: radial-gradient(circle at 50% 20%, rgba(212, 175, 55, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(14, 165, 233, 0.05) 0%, transparent 40%),
                        linear-gradient(145deg, #070a12 0%, #0b1120 50%, #060911 100%);
            min-height: 100vh;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glow Grid */
        .ambient-grid {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        .maintenance-card {
            position: relative;
            z-index: 1;
            max-width: 680px;
            width: 100%;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6), 0 0 40px rgba(212, 175, 55, 0.1);
            padding: 3.5rem 2.5rem;
            text-align: center;
            animation: fadeInScale 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInScale {
            0% { opacity: 0; transform: scale(0.94) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 18px;
            background: rgba(212, 175, 55, 0.12);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 999px;
            color: var(--gold);
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 2rem;
            letter-spacing: 0.5px;
        }

        .gear-glow-wrapper {
            position: relative;
            width: 96px;
            height: 96px;
            margin: 0 auto 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gear-glow-circle {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.35) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulseGlow 3s ease-in-out infinite alternate;
        }

        @keyframes pulseGlow {
            0% { transform: scale(0.85); opacity: 0.5; }
            100% { transform: scale(1.2); opacity: 0.9; }
        }

        .gear-icon-box {
            position: relative;
            width: 76px;
            height: 76px;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.25) 0%, rgba(15, 23, 42, 0.9) 100%);
            border: 1px solid rgba(212, 175, 55, 0.5);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: var(--gold);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            animation: spinSlow 20s linear infinite;
        }

        @keyframes spinSlow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .maintenance-title {
            font-size: 2.1rem;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .maintenance-desc {
            color: #94a3b8;
            font-size: 1.05rem;
            line-height: 1.8;
            margin-bottom: 2.5rem;
            max-width: 540px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Countdown Container */
        .countdown-container {
            background: rgba(10, 15, 29, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .countdown-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .timer-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .timer-box {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 14px;
            padding: 1rem 0.5rem;
            transition: all 0.3s ease;
        }

        .timer-box:hover {
            border-color: var(--gold);
            transform: translateY(-2px);
        }

        .timer-num {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1;
            margin-bottom: 4px;
        }

        .timer-text {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
        }

        .btn-home {
            background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold) 100%);
            color: #070a12;
            font-weight: 800;
            padding: 0.85rem 2.2rem;
            border-radius: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 18px rgba(212, 175, 55, 0.3);
            border: none;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.5);
            color: #000;
        }

        .admin-bypass-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 2rem;
            color: #475569;
            font-size: 0.82rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .admin-bypass-link:hover {
            color: var(--gold);
        }
    </style>
</head>
<body>
    <div class="ambient-grid"></div>

    <div class="maintenance-card">
        <!-- Brand Pill -->
        <div class="brand-pill">
            <i class="bi bi-robot text-gold"></i>
            <span>منصة ردود الذكية • وضع الصيانة المجدول</span>
        </div>

        <!-- Animated Gear Icon -->
        <div class="gear-glow-wrapper">
            <div class="gear-glow-circle"></div>
            <div class="gear-icon-box">
                <i class="bi bi-gear-wide-connected"></i>
            </div>
        </div>

        <!-- Maintenance Title -->
        <h1 class="maintenance-title">{{ $maintenance['title'] ?? 'أعمال صيانة وتطوير مجدولة 🛠️' }}</h1>

        <!-- Maintenance Description -->
        <p class="maintenance-desc">
            {{ $maintenance['message'] ?? 'نقوم حالياً بإجراء تحديثات دورية وتطويرات هامة على منصة ردود لتعزيز استقرار البنية التحتية وتقديم تجربة ردود ذكية فائقة السرعة.' }}
        </p>

        <!-- Live Countdown Timer -->
        <div class="countdown-container">
            <div class="countdown-label">
                <i class="bi bi-clock-history"></i>
                <span id="countdownStatusText">الموعد التقديري لاستئناف الخدمة</span>
            </div>

            <div class="timer-grid">
                <div class="timer-box">
                    <div class="timer-num" id="cdDays">00</div>
                    <div class="timer-text">يوم</div>
                </div>
                <div class="timer-box">
                    <div class="timer-num" id="cdHours">00</div>
                    <div class="timer-text">ساعة</div>
                </div>
                <div class="timer-box">
                    <div class="timer-num" id="cdMins">00</div>
                    <div class="timer-text">دقيقة</div>
                </div>
                <div class="timer-box">
                    <div class="timer-num" id="cdSecs">00</div>
                    <div class="timer-text">ثانية</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div>
            <a href="/index" class="btn-home">
                <i class="bi bi-house-door-fill"></i>
                <span>العودة للصفحة الرئيسية</span>
            </a>
        </div>

        <!-- Super Admin Subtle Portal Link -->
        <div>
            <a href="{{ route('admin.login') }}" class="admin-bypass-link">
                <i class="bi bi-shield-lock"></i>
                <span>بوابة دخول المشرف العام (Super Admin)</span>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rawTarget = "{{ $maintenance['scheduled_ends_at'] ?? '' }}";
            let targetDate = null;

            if (rawTarget && rawTarget.trim() !== '') {
                targetDate = new Date(rawTarget).getTime();
            } else {
                // Default fallback: 3 hours from now
                targetDate = new Date().getTime() + (3 * 60 * 60 * 1000);
            }

            function updateCountdown() {
                const now = new Date().getTime();
                const distance = targetDate - now;

                if (distance <= 0) {
                    document.getElementById('cdDays').textContent = "00";
                    document.getElementById('cdHours').textContent = "00";
                    document.getElementById('cdMins').textContent = "00";
                    document.getElementById('cdSecs').textContent = "00";
                    document.getElementById('countdownStatusText').textContent = "جاري استئناف الخدمات الآن... 🚀";
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('cdDays').textContent = String(days).padStart(2, '0');
                document.getElementById('cdHours').textContent = String(hours).padStart(2, '0');
                document.getElementById('cdMins').textContent = String(minutes).padStart(2, '0');
                document.getElementById('cdSecs').textContent = String(seconds).padStart(2, '0');
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    </script>
</body>
</html>
