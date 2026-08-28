@extends('admin.layouts.app')

@section('page_title', 'إضافة مشترك وتفعيل متجر جديد')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('admin.subscribers.index') }}" class="text-white-50 fs-8 text-decoration-none hover-gold">
                    <i class="bi bi-arrow-right me-1"></i> العودة لقائمة طلبات المشتركين
                </a>
                <h3 class="fw-bold text-white mt-1 mb-0">إضافة مشترك جديد وتفعيل مساحة عمله وبوته 🚀</h3>
            </div>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger card-custom border-danger p-3 mb-4">
            <ul class="mb-0 fs-8">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.subscribers.store') }}" method="POST">
            @csrf
            
            <!-- 1. بيانات المشترك الشخصية -->
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge text-gold"></i>
                    <span>1. بيانات المشترك الشخصية ووصول الحساب</span>
                </h5>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label text-white fs-8">اسم المشترك / المالك الكامل <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="مثال: عبدالمحسن السالم" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label text-white fs-8">البريد الإلكتروني المعتمد <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="مثال: subscriber@store.com" value="{{ old('email') }}" required>
                        <small class="text-white-50 fs-9">سيتم استخدام هذا البريد لتسجيل دخول المالك للنظام.</small>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label text-white fs-8">رقم الجوال / واتساب <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" placeholder="مثال: +966501234567" value="{{ old('phone') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label text-white fs-8">كلمة المرور الأولية للحساب <span class="text-danger">*</span></label>
                        <input type="text" name="password" class="form-control" value="{{ old('password', 'password123') }}" required>
                        <small class="text-white-50 fs-9">يمكن للمشترك تغيير كلمة المرور لاحقاً من ملفه الشخصي.</small>
                    </div>
                </div>
            </div>

            <!-- 2. بيانات المتجر والاشتراك -->
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-shop text-info"></i>
                    <span>2. بيانات المتجر وخطة الاشتراك</span>
                </h5>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label text-white fs-8">اسم المتجر أو الشركة <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control" placeholder="مثال: متجر الأناقة للملابس" value="{{ old('company_name') }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label text-white fs-8">باقة الاشتراك المعتمدة <span class="text-danger">*</span></label>
                        <select name="selected_plan" class="form-select" required>
                            <option value="starter" {{ old('selected_plan') == 'starter' ? 'selected' : '' }}>باقة البداية (Starter - $39/شهرياً)</option>
                            <option value="professional" {{ old('selected_plan', 'professional') == 'professional' ? 'selected' : '' }}>الباقة الاحترافية الأكثر طلباً (Professional - $79/شهرياً)</option>
                            <option value="enterprise" {{ old('selected_plan') == 'enterprise' ? 'selected' : '' }}>باقة الشركات الكبرى (Enterprise - $199/شهرياً)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 3. إعدادات وتخصيص البوت الذكي -->
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-robot text-gold"></i>
                    <span>3. إعدادات وتخصيص البوت الذكي التلقائي</span>
                </h5>

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label text-white fs-8">اسم المساعد الذكي</label>
                        <input type="text" name="bot_name" class="form-control" placeholder="مثال: مساعد الأناقة الذكي" value="{{ old('bot_name') }}">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label text-white fs-8">مزود محرك الذكاء الاصطناعي</label>
                        <select name="ai_provider" class="form-select" required>
                            <option value="gemini" {{ old('ai_provider', 'gemini') == 'gemini' ? 'selected' : '' }}>Google Gemini (فائق السرعة والدقة العربية)</option>
                            <option value="openai" {{ old('ai_provider') == 'openai' ? 'selected' : '' }}>OpenAI (GPT-4o Mini)</option>
                            <option value="claude" {{ old('ai_provider') == 'claude' ? 'selected' : '' }}>Anthropic Claude (Claude 3.5 Sonnet)</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label text-white fs-8">نبرة حوار البوت الافتراضية</label>
                        <select name="bot_tone" class="form-select" required>
                            <option value="friendly" {{ old('bot_tone', 'friendly') == 'friendly' ? 'selected' : '' }}>ودية ولطيفة (Friendly)</option>
                            <option value="formal" {{ old('bot_tone') == 'formal' ? 'selected' : '' }}>رسمية ومهنية (Formal)</option>
                            <option value="enthusiastic" {{ old('bot_tone') == 'enthusiastic' ? 'selected' : '' }}>حماسية وتسويقية (Enthusiastic)</option>
                            <option value="empathetic" {{ old('bot_tone') == 'empathetic' ? 'selected' : '' }}>متعاطفة وداعمة (Empathetic)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-white fs-8">التوجيه البرمجي الأساسي للبوت (System Prompt)</label>
                        <textarea name="system_prompt" class="form-control" rows="3" placeholder="أنت مساعد خدمة عملاء ذكي وخبير لمتجر...">{{ old('system_prompt') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-white fs-8">رسالة الترحيب الأولى للعميل</label>
                        <input type="text" name="welcome_message" class="form-control" placeholder="أهلاً بك! 👋 كيف يمكنني خدمتك اليوم؟" value="{{ old('welcome_message') }}">
                    </div>
                </div>
            </div>

            <!-- 4. ملاحظات المشرف العام -->
            <div class="card card-custom p-4 mb-4">
                <label class="form-label text-white fs-8">ملاحظات داخلية للمشرف (اتفاق الأسعار أو تفاصيل خاصة)</label>
                <textarea name="admin_notes" class="form-control" rows="2" placeholder="تم الاتفاق هاتفياً على تفعيل الربط مع واتساب وتدريب البوت على قائمة المنتجات...">{{ old('admin_notes') }}</textarea>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="d-flex justify-content-end gap-3 mb-5">
                <a href="{{ route('admin.subscribers.index') }}" class="btn btn-secondary rounded-pill px-4">إلغاء</a>
                <button type="submit" class="btn btn-gold rounded-pill px-5 fw-bold fs-7 d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>إضافة وتفعيل المشترك فوراً</span>
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
