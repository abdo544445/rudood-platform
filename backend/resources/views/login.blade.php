<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة ردود - تسجيل الدخول</title>
    
    <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  <!-- Google Fonts (Cairo) -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  @include('layouts.partials.theme')

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #0b0f19;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        /* هالة ذهبية ناعمة في الخلفية */
        body::before {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            background: rgba(212, 175, 55, 0.12);
            filter: blur(130px);
            border-radius: 50%;
            top: 25%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.04) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        .form-control {
            background-color: rgba(11, 15, 25, 0.6) !important;
            border: 1px solid rgba(212, 175, 55, 0.25) !important;
            color: #fff !important;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: #D4AF37 !important;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.3) !important;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .text-gold {
            color: #D4AF37 !important;
        }

        .btn-gold {
            background-color: #D4AF37 !important;
            color: #0b0f19 !important;
            border: none;
            font-weight: bold;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background-color: #f1c40f !important;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
            transform: translateY(-2px);
        }

        .link-gold {
            color: #D4AF37;
            text-decoration: none;
            transition: color 0.2s, transform 0.2s;
        }

        .link-gold:hover {
            color: #f1c40f;
        }

        .btn-back {
            color: rgba(255, 255, 255, 0.6);
            transition: all 0.2s;
        }

        .btn-back:hover {
            color: #D4AF37;
        }
    </style>
</head>
<body>

    <div class="login-card p-4 p-sm-5 rounded-4 text-center">
        
        <!-- زر العودة السريع للرئيسية -->
        <div class="text-start mb-3">
            <a href="{{ url('/index') }}" class="btn-back text-decoration-none fs-7 d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-right"></i> الرئيسية
            </a>
        </div>

        <!-- الشعار / اسم المنصة -->
        <div class="mb-4">
            <a href="{{ url('/index') }}" class="text-decoration-none">
                <h2 class="fw-bold text-gold m-0">ردود</h2>
            </a>
            <p class="text-white-50 fs-6 mt-2">مرحباً بعودتك! 👋</p>
        </div>

        <!-- مكان عرض التنبيهات والأخطاء من الباك إند -->
        @if ($errors->any())
        <div class="alert alert-danger py-2 fs-7 mb-3" role="alert">
            {{ $errors->first() }}
        </div>
        @endif
        @if (session('status'))
        <div class="alert alert-success py-2 fs-7 mb-3" role="alert">
            {{ session('status') }}
        </div>
        @endif

        <!-- نموذج تسجيل الدخول -->
        <form action="{{ url('/login') }}" method="POST" id="loginForm">
            @csrf
            @method('POST')
            <!-- البريد الإلكتروني -->
            <div class="mb-3 text-start">
                <label for="email" class="form-label text-white-50 fs-7">البريد الإلكتروني</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-gold border-opacity-25 text-gold">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                </div>
            </div>

            <!-- كلمة المرور -->
            <div class="mb-3 text-start">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label text-white-50 fs-7 mb-0">كلمة المرور</label>
                    <a href="#" class="link-gold fs-7">نسيت كلمة المرور؟</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-gold border-opacity-25 text-gold">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <!-- زر الدخول -->
            <button type="submit" class="btn btn-gold w-100 rounded-3 mt-3 fs-6">
                تسجيل الدخول <i class="bi bi-box-arrow-in-left ms-1"></i>
            </button>
        </form>

        <!-- رابط إنشاء حساب جديد -->
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
            <p class="text-white-50 mb-0 fs-7">
                ليس لديك حساب؟ <a href="{{ url('/register') }}" class="link-gold fw-bold ms-1">إنشاء حساب جديد</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
