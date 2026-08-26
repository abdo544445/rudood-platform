<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>منصة ردود - تواصل معنا</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
  <!-- Google Fonts (Cairo) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  @include('layouts.partials.theme')

  <style>
    .contact-icon-box {
      width: 50px;
      height: 50px;
      background: rgba(212, 163, 89, 0.1);
      border: 1px solid rgba(212, 163, 89, 0.3);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      color: var(--text-gold);
    }
  </style>
</head>
<body>

  <!-- شريط التنقل العلوي (Navbar) -->
  <nav class="navbar navbar-expand-lg navbar-rodood sticky-top">
    <div class="container">
      
      <!-- 1. الشعار (جهة اليمين) -->
      <a class="navbar-brand d-flex align-items-center me-3" href="{{ url('/index') }}">
        <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" class="nav-logo-img">
      </a>

      <!-- زر القائمة للشاشات الصغيرة -->
      <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarRodood" aria-controls="navbarRodood" aria-expanded="false" aria-label="Toggle navigation">
        <i class="bi bi-list fs-2 text-gold"></i>
      </button>

      <!-- محتوى الهيدر -->
      <div class="collapse navbar-collapse" id="navbarRodood">
        
        <!-- 2. روابط التنقل -->
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold align-items-center">
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/index') }}">الرئيسية</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/features') }}">المميزات</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/pricing') }}">التسعيرة</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="{{ url('/blog') }}">المدونة</a></li>
          
          <!-- قائمة أقسام المنصة المنسدلة -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white-50" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              أقسام المنصة
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 mt-2" aria-labelledby="servicesDropdown">
              <li><a class="dropdown-item py-2" href="{{ url('/auto') }}"><i class="bi bi-robot me-2 text-gold"></i>الرد الآلي</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/chat') }}"><i class="bi bi-chat-dots me-2 text-gold"></i>المحادثات</a></li>
              <li><a class="dropdown-item py-2" href="{{ url('/ai') }}"><i class="bi bi-cpu me-2 text-gold"></i>الذكاء الاصطناعي</a></li>
            </ul>
          </li>

          <li class="nav-item"><a class="nav-link active text-gold" href="{{ url('/try') }}">تواصل معنا</a></li>
        </ul>

        <!-- 3. الأزرار -->
        <div class="d-flex align-items-center gap-2">
          <a href="{{ url('/demo') }}" class="btn btn-outline-danger rounded-pill px-3 fs-8 fw-bold d-flex align-items-center gap-1">
            <i class="bi bi-broadcast text-danger"></i> استعراض حي
          </a>
          <a href="{{ url('/login') }}" class="btn btn-outline-light rounded-pill px-3 fs-8">تسجيل الدخول</a>
          <a href="{{ url('/register') }}" class="btn btn-gold rounded-pill px-3 fw-bold fs-8 d-flex align-items-center gap-1">
            <i class="bi bi-headset"></i> ابدأ الآن
          </a>
        </div>

      </div>
    </div>
  </nav>

  <!-- قسم العنوان الرئيسي للأسعار -->
  <section class="py-5 text-center position-relative">
    <div class="container py-4">
      <span class="badge bg-gold-subtle text-gold rounded-pill px-3 py-2 mb-3 border border-warning border-opacity-25 fs-6">نحن هنا لمساعدتك</span>
      <h1 class="display-4 fw-extrabold mb-3 text-white">تواصل مع <span class="text-gold-gradient">فريق ردود</span></h1>
      <p class="lead text-white-50 mx-auto" style="max-width: 650px;">
        نحن هنا لمساعدتك في أتمتة خدمات عملائك والارتقاء بأعمالك إلى المستوى التالي.
      </p>
    </div>
  </section>

  <!-- محتوى الصفحة الرئيسي -->
  <main class="pb-5">
    <div class="container">
      <div class="row g-4 align-items-stretch">
        
        <!-- العمود الأيمن: معلومات التواصل -->
        <div class="col-lg-5">
          <div class="pricing-card h-100 p-4 p-md-5 rounded-4 d-flex flex-column justify-content-between text-white">
            <div>
              <h3 class="fw-bold text-white mb-3">معلومات الاتصال</h3>
              <p class="text-white-50 mb-4 lh-lg">يسعدنا استقبال استفساراتك واقتراحاتك في أي وقت، فريق الدعم متواجد لخدمتك.</p>
              
              <!-- عنصر 1: البريد -->
              <div class="d-flex align-items-center gap-3 mb-4">
                <div class="contact-icon-box">
                  <i class="bi bi-envelope"></i>
                </div>
                <div>
                  <span class="d-block text-white-50 fs-7">البريد الإلكتروني</span>
                  <strong class="text-white">support@rodood.ai</strong>
                </div>
              </div>

              <!-- عنصر 2: الواتساب -->
              <div class="d-flex align-items-center gap-3 mb-4">
                <div class="contact-icon-box">
                  <i class="bi bi-whatsapp"></i>
                </div>
                <div>
                  <span class="d-block text-white-50 fs-7">الدعم الفني عبر الواتساب</span>
                  <strong class="text-white" dir="ltr">+968 9000 0000</strong>
                </div>
              </div>

              <!-- عنصر 3: أوقات العمل -->
              <div class="d-flex align-items-center gap-3 mb-4">
                <div class="contact-icon-box">
                  <i class="bi bi-clock"></i>
                </div>
                <div>
                  <span class="d-block text-white-50 fs-7">ساعات العمل</span>
                  <strong class="text-white">متاح 24/7 عبر الذكاء الاصطناعي</strong>
                </div>
              </div>
            </div>

            <!-- وسائل التواصل الاجتماعي -->
            <div class="pt-4 border-top border-white border-opacity-10">
              <span class="d-block text-white-50 mb-3 fs-7">تابعنا على شبكات التواصل</span>
              <div class="d-flex gap-3 fs-5">
                <a href="#" class="text-white-50 text-gold-hover"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="text-white-50 text-gold-hover"><i class="bi bi-linkedin"></i></a>
                <a href="#" class="text-white-50 text-gold-hover"><i class="bi bi-instagram"></i></a>
              </div>
            </div>
          </div>
        </div>

        <!-- العمود الأيسر: نموذج المراسلة -->
        <div class="col-lg-7">
          <div class="pricing-card p-4 p-md-5 rounded-4 text-white">
            <h3 class="fw-bold text-white mb-4">أرسل لنا رسالة</h3>
            
            @if(session('success'))
              <div class="alert alert-success bg-success bg-opacity-15 border-success text-success mb-4 rounded-3 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
              </div>
            @endif

            <form action="{{ route('contact.submit') }}" method="POST" id="contactForm">
              @csrf
              <div class="row g-3">
                <div class="col-md-6 text-start">
                  <label for="senderName" class="form-label text-white-50 fs-7">الاسم الكامل</label>
                  <input type="text" class="form-control bg-dark border-white border-opacity-10 text-white rounded-3 py-2.5" id="senderName" name="sender_name" placeholder="محمد أحمد" required>
                </div>

                <div class="col-md-6 text-start">
                  <label for="senderEmail" class="form-label text-white-50 fs-7">البريد الإلكتروني</label>
                  <input type="email" class="form-control bg-dark border-white border-opacity-10 text-white rounded-3 py-2.5" id="senderEmail" name="sender_email" placeholder="name@example.com" required>
                </div>

                <div class="col-12 text-start">
                  <label for="subject" class="form-label text-white-50 fs-7">عنوان الرسالة</label>
                  <input type="text" class="form-control bg-dark border-white border-opacity-10 text-white rounded-3 py-2.5" id="subject" name="subject" placeholder="استفسار عن خطط الأسعار" required>
                </div>

                <div class="col-12 text-start">
                  <label for="message" class="form-label text-white-50 fs-7">نص الرسالة</label>
                  <textarea class="form-control bg-dark border-white border-opacity-10 text-white rounded-3" id="message" name="message" rows="5" placeholder="اكتب استفسارك هنا..." required></textarea>
                </div>

                <div class="col-12 mt-4 text-end">
                  <button type="submit" class="btn btn-gold px-5 py-3 rounded-pill fw-bold shadow-gold w-100 w-md-auto">
                    إرسال الرسالة <i class="bi bi-send ms-2"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- الفوتر / أسفل الصفحة -->
  <footer class="py-4 border-top border-white border-opacity-10 text-center text-white-50 fs-7">
    <div class="container">
      <p class="m-0">جميع الحقوق محفوظة © 2026 منصة ردود (Rudood)</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
