# 🚀 منصة ردود للذكاء الاصطناعي (Rudood AI Platform)

منصة سعودية ذكية متكاملة لأتمتة خدمة العملاء والمبيعات للمتاجر الإلكترونية والشركات عبر قنوات التواصل المتعددة (WhatsApp Cloud API, Telegram Bot, Instagram Direct, Web Live Widget).

---

## 🏗️ البنية التقنية (Tech Stack)

- **Backend**: Laravel 11.x (PHP 8.4/8.2) + Eloquent ORM + Multi-Tenant Workspaces
- **Database**: PostgreSQL 16 مع دعم `pgvector` للبحث الدلالي بالذكاء الاصطناعي
- **Cache & Queues**: Redis Alpine
- **Real-Time WebSockets**: Node.js WebSocket Server
- **Web Server**: Nginx + PHP-FPM مُدار عبر Supervisor
- **Frontend**: Blade + Bootstrap 5.3 (RTL) + تصميم زجاجي عصري (Glassmorphism & Gold Theme)

---

## 💻 تشغيل المشروع على جهاز ويندوز جديد عبر Docker (Windows Setup Guide)

### 1. المتطلبات الأساسية على جهاز ويندوز:
- تثبيت **[Docker Desktop for Windows](https://www.docker.com/products/docker-desktop/)** مع تفعيل خيار **WSL 2 Backend**.
- تشغيل برنامج Docker Desktop والتأكد من أنه يعمل في شريط المهام.

---

### 2. نقل المشروع وتشغيله:
1. انسخ مجلد المشروع بالكامل إلى جهاز الويندوز (مثلاً في `C:\Projects\rudood-platform`).
2. افتح موجه الأوامر **PowerShell** أو **Command Prompt** أو **Git Bash** داخل مجلد المشروع.
3. شغل الأمر التالي لبناء وتشغيل الحاويات في الخلفية:

```bash
docker compose up -d --build
```

---

### 3. إنشاء الجداول وتحميل البيانات الأولية (Migration & Seeding):
بعد اكتمال بناء وتشغيل الحاويات، نفّذ الأمر التالي لإنشاء الجداول في PostgreSQL وتحميل حسابات الاختبار:

```bash
docker compose exec app php artisan migrate --seed
```

---

## 🌐 روابط الوصول للخدمات (Service URLs)

| الخدمة | الرابط المحلي |
| :--- | :--- |
| **الصفحة الرئيسية (Landing Page)** | [http://localhost:8000/index](http://localhost:8000/index) |
| **العرض التجريبي الحي (Live Case Study)** | [http://localhost:8000/demo](http://localhost:8000/demo) |
| **صفحة تواصل معنا (Contact Us)** | [http://localhost:8000/contact](http://localhost:8000/contact) |
| **تسجيل الدخول (Login)** | [http://localhost:8000/login](http://localhost:8000/login) |
| **لوحة تحكم المشرف الأعلى (Super Admin)** | [http://localhost:8000/admin/dashboard](http://localhost:8000/admin/dashboard) |
| **رسائل تواصل معنا (Admin Inquiries)** | [http://localhost:8000/admin/contacts](http://localhost:8000/admin/contacts) |
| **لوحة تحكم المتجر (Store Dashboard)** | [http://localhost:8000/dashboard](http://localhost:8000/dashboard) |
| **مركز القنوات (Omni-Channel Hub)** | [http://localhost:8000/channels](http://localhost:8000/channels) |
| **مختبر الذكاء الاصطناعي (AI Playground)** | [http://localhost:8000/playground](http://localhost:8000/playground) |

---

## 🔑 بيانات الدخول الافتراضية (Default Credentials)

### 1. حساب المدير الأعلى (Super Admin):
- **البريد الإلكتروني**: `admin@rudood.com`
- **كلمة المرور**: `admin123456` (أو `password123`)

### 2. حساب مالك المتجر التجريبي (Store Owner):
- **البريد الإلكتروني**: `owner@store.com`
- **كلمة المرور**: `password` (أو `password123`)

---

## 🛠️ أوامر الصيانة المفيدة داخل Docker

```bash
# إيقاف الحاويات
docker compose down

# إعادة تشغيل الحاويات
docker compose restart

# عرض سجلات الأخطاء والأنشطة للحاوية الرئيسية
docker compose logs -f app

# الدخول إلى سطر أوامر الحاوية
docker compose exec app sh

# مسح الكاش وتحديث الإعدادات
docker compose exec app php artisan optimize:clear
```
