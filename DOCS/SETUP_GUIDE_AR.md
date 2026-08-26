# 🚀 دليل التشغيل والتثبيت السريع — منصة ردود (Rudood Setup Guide)

<div dir="rtl">

أهلاً بك في دليل تشغيل وتثبيت **منصة ردود للذكاء الاصطناعي**. يشرح هذا الدليل طريقتين لتشغيل المشروع:
1. **الطريقة الأولى**: التشغيل باستخدام **Docker** (موصى بها لنقل المشروع وتشغيله على أي جهاز ويندوز أو سيرفر دون تعقيدات).
2. **الطريقة الثانية**: التشغيل المباشر **بدون Docker** (للتطوير المحلي السريع باستخدام PHP و Composer).

---

## 🐳 الطريقة الأولى: التشغيل باستخدام Docker (الأسهل والأسرع)

هذه الطريقة تشغل التطبيق كاملاً مع قاعدة بيانات **PostgreSQL 16** وخادم **Redis** وخادم **WebSockets** بضغطة زر واحدة.

### 1. المتطلبات:
- تثبيت برنامج **[Docker Desktop](https://www.docker.com/products/docker-desktop/)** والتأكد من أنه قيد التشغيل في شريط المهام.
- (لمستخدمي Windows): التأكد من تفعيل خيار **WSL 2 Backend** في إعدادات Docker Desktop.

### 2. خطوات التشغيل:
افتح موجه الأوامر (Terminal أو PowerShell أو Git Bash) داخل مجلد المشروع الرئيسي، ثم نفّذ الأوامر التالية:

</div>

```bash
# 1. بناء وتشغيل الحاويات في الخلفية
docker compose up -d --build

# 2. إنشاء جداول قاعدة البيانات وتحميل البيانات التجريبية
docker compose exec app php artisan migrate --seed
```

<div dir="rtl">

مبروك! المنصة الآن تعمل وجاهزة للاستخدام على الرابط: **[http://localhost:8000](http://localhost:8000)**.

---

## 💻 الطريقة الثانية: التشغيل المحلي بدون Docker (Local Setup)

إذا كنت تفضل تشغيل المشروع مباشرة على جهازك دون استخدام Docker:

### 1. المتطلبات:
- **PHP 8.2** أو أحدث مع الامتدادات الأساسية (`pdo`, `mbstring`, `openssl`, `curl`, `pdo_sqlite` أو `pdo_pgsql`).
- **Composer** مثبت على جهازك.
- **Node.js & NPM** (اختياري، لتشغيل سيرفر الويب سوكت).

### 2. خطوات التشغيل:
افتح موجه الأوامر وتوجه إلى مجلد `backend`:

</div>

```bash
# 1. الدخول لمجلد الباك إند
cd backend

# 2. تثبيت حزم PHP عبر Composer
composer install

# 3. نسخ ملف الإعدادات وإنشاء مفتاح التطبيق (إن لم يكن موجوداً)
cp .env.example .env
php artisan key:generate

# 4. تشغيل الهجرات وتحميل البيانات (افتراضياً باستخدام SQLite السريع)
touch database/database.sqlite
php artisan migrate --seed

# 5. تشغيل خادم التطوير المحلي
php artisan serve
```

<div dir="rtl">

سيعمل التطبيق مباشرة على الرابط: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**.

*(ملاحظة: لاختبار بوت تيليجرام محلياً بدون رابط خارجي، يمكنك تشغيل الأمر: `php artisan telegram:poll` في نافذة منفصلة).*

---

## 🔑 بيانات الدخول الافتراضية (Default Accounts)

تم تجهيز قاعدة البيانات بحسابات تجريبية مفعلة مسبقاً:

| الحساب | نوع الصلاحية | البريد الإلكتروني | كلمة المرور |
| :--- | :--- | :--- | :--- |
| **المدير الأعلى للمنصة (Super Admin)** | وصول كامل لكافة الشركات والإحصائيات والرسائل | `admin@rudood.com` | `admin123456` |
| **مالك المتجر التجريبي (Store Owner)** | إدارة متجر النخبة والمحادثات والبوت والقنوات | `owner@store.com` | `password` |

---

## 🌐 أهم الروابط المباشرة في المنصة

| الصفحة | الرابط |
| :--- | :--- |
| **الصفحة الرئيسية للموقع** | [http://localhost:8000/index](http://localhost:8000/index) |
| **العرض التجريبي التفاعلي (Live Demo)** | [http://localhost:8000/demo](http://localhost:8000/demo) |
| **صفحة تواصل معنا** | [http://localhost:8000/contact](http://localhost:8000/contact) |
| **تسجيل الدخول** | [http://localhost:8000/login](http://localhost:8000/login) |
| **لوحة تحكم المتجر (Merchant Dashboard)** | [http://localhost:8000/dashboard](http://localhost:8000/dashboard) |
| **محادثات خدمة العملاء الحية (Live Chat)** | [http://localhost:8000/live-chat](http://localhost:8000/live-chat) |
| **مركز ربط القنوات الأربعة (Omni-Channel Hub)** | [http://localhost:8000/channels](http://localhost:8000/channels) |
| **مختبر الذكاء الاصطناعي (AI Playground)** | [http://localhost:8000/playground](http://localhost:8000/playground) |
| **لوحة الإدارة العليا (Super Admin)** | [http://localhost:8000/admin/dashboard](http://localhost:8000/admin/dashboard) |
| **صندوق رسائل تواصل معنا الواردة** | [http://localhost:8000/admin/contacts](http://localhost:8000/admin/contacts) |
| **سجل تدقيق الأنشطة والأمان (Audit Logs)** | [http://localhost:8000/admin/audit-logs](http://localhost:8000/admin/audit-logs) |

---

## 🛠️ أوامر سريعة ومفيدة

</div>

```bash
# تشغيل فاحص الاختبارات الآلية الشامل (50 اختبار معتمد بنسبة نجاح 100%)
php tests_suite_runner.php

# مسح كافة أنواع الكاش (Views, Routes, Config)
php artisan optimize:clear

# فحص حالة استماع تيليجرام الفوري
php artisan telegram:poll
```

<div dir="rtl">

</div>
