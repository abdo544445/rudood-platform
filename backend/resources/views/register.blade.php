<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة ردود - إنشاء حساب جديد</title>
    
    <!-- Bootstrap 5 RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts (Cairo) -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- ملف CSS الخاص بك -->
    <link rel="stylesheet" href="mystyle.css">

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
            padding: 20px 0;
        }

      body {
    font-family: 'Cairo', sans-serif;
    background-color: #060913; /* لون كحلي غامق جداً كأساس */
    
    /* دمج تدرج كحلي داكن مع الصورة لإبراز الألوان وإلغاء البهتان */
    background-image: 
        radial-gradient(circle at center, rgba(11, 20, 38, 0.4) 0%, rgba(6, 9, 19, 0.85) 100%),
        url('images/log22.png'); /* تأكدي من اسم صورتك هنا */
        
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    
    color: #ffffff;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 20px 0;
}

/* طبقة الإضاءة الذهبية الناعمة خلف النموذج */
body::before {
    content: '';
    position: absolute;
    width: 350px;
    height: 350px;
    background: rgba(212, 175, 55, 0.15);
    filter: blur(140px);
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 0;
}

        .register-card {
            background: rgba(255, 255, 255, 0.04) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 460px;
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
            transition: color 0.2s;
        }

        .link-gold:hover {
            color: #f1c40f;
            text-decoration: underline;
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

    <div class="register-card p-4 p-sm-5 rounded-4 text-center">
        
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
            <p class="text-white-50 fs-6 mt-2">انضم إلينا وابدأ أتمتة أعمالك ✨</p>
        </div>

        <!-- مكان عرض التنبيهات والأخطاء من الباك إند -->
        @if ($errors->any())
        <div class="alert alert-danger py-2 fs-7 mb-3" role="alert">
            {{ $errors->first() }}
        </div>
        @endif

        <!-- نموذج إنشاء حساب جديد -->
        <form action="{{ url('/register') }}" method="POST" id="registerForm">
            @csrf
            <!-- الاسم الكامل -->
            <div class="mb-3 text-start">
                <label for="fullName" class="form-label text-white-50 fs-7">الاسم الكامل</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-gold border-opacity-25 text-gold">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" class="form-control" id="fullName" name="full_name" placeholder="محمد أحمد" required>
                </div>
            </div>

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

            <!-- رقم الهاتف / الواتساب -->
            <div class="mb-3 text-start">
                <label for="phone" class="form-label text-white-50 fs-7">رقم الهاتف (الواتساب)</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-gold border-opacity-25 text-gold">
                        <i class="bi bi-whatsapp"></i>
                    </span>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+968 9000 0000" required>
                </div>
            </div>

            <!-- كلمة المرور -->
            <div class="mb-3 text-start">
                <label for="password" class="form-label text-white-50 fs-7">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-gold border-opacity-25 text-gold">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <!-- تأكيد كلمة المرور -->
            <div class="mb-3 text-start">
                <label for="password_confirmation" class="form-label text-white-50 fs-7">تأكيد كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-gold border-opacity-25 text-gold">
                        <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                </div>
            </div>

            <!-- زر إنشاء الحساب -->
            <button type="submit" class="btn btn-gold w-100 rounded-3 mt-3 fs-6">
                إنشاء حساب جديد <i class="bi bi-person-plus ms-1"></i>
            </button>
        </form>

        <!-- رابط العودة لتسجيل الدخول -->
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
            <p class="text-white-50 mb-0 fs-7">
                لديك حساب بالفعل؟ <a href="{{ url('/login') }}" class="link-gold fw-bold ms-1">تسجيل الدخول</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
