@extends('admin.layouts.app')

@section('title', 'إدارة متجر - ' . $workspace->company_name)
@section('page_title', 'ملف وتحكم متجر: ' . $workspace->company_name)

@section('content')
<!-- Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <a href="{{ route('admin.workspaces.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="bi-arrow-right me-1"></i> العودة لقائمة الشركات والمتاجر
    </a>

    <div class="d-flex gap-2">
        <form action="{{ route('admin.workspaces.impersonate', $workspace->id) }}" method="POST" onsubmit="return confirm('تسجيل الدخول والتصفح كمالك لمتجر ({{ $workspace->company_name }})؟')">
            @csrf
            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold d-flex align-items-center gap-2">
                <i class="bi-person-badge"></i> تسجيل الدخول كمالك المتجر (Impersonate)
            </button>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Left Quick Stats Column -->
    <div class="col-12 col-lg-4">
        <!-- Store Overview Card -->
        <div class="card card-custom p-4 mb-4 text-center">
            <div class="mx-auto mb-3" style="width: 75px; height: 75px; border-radius: 20px; background: linear-gradient(135deg, var(--gold-dark), var(--gold)); display: flex; align-items: center; justify-content: center; font-size: 2.2rem; color: #000;">
                <i class="bi bi-building"></i>
            </div>
            <h4 class="fw-bold text-white mb-1">{{ $workspace->company_name }}</h4>
            <div class="text-muted small mb-3">ID: #{{ $workspace->id }} | تاريخ التسجيل: {{ $workspace->created_at->format('Y-m-d') }}</div>
            
            <div class="d-flex justify-content-center gap-2 mb-3">
                @if($workspace->status === 'active')
                    <span class="badge bg-success bg-opacity-25 text-success px-3 py-2 rounded-pill">حساب نشط (Active)</span>
                @elseif($workspace->status === 'suspended')
                    <span class="badge bg-danger bg-opacity-25 text-danger px-3 py-2 rounded-pill">موقوف (Suspended)</span>
                @else
                    <span class="badge bg-warning bg-opacity-25 text-warning px-3 py-2 rounded-pill">تجريبي (Trial)</span>
                @endif
                <span class="badge bg-primary bg-opacity-25 text-primary px-3 py-2 rounded-pill text-uppercase">
                    {{ $workspace->plan_id ?? 'Starter' }}
                </span>
            </div>

            <hr class="border-secondary border-opacity-25">

            <div class="row text-center g-2">
                <div class="col-6">
                    <div class="p-3 rounded bg-dark border border-secondary border-opacity-25">
                        <div class="fs-4 fw-bold text-white">{{ $workspace->conversations_count }}</div>
                        <div class="small text-muted">محادثة مستلمة</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded bg-dark border border-secondary border-opacity-25">
                        <div class="fs-4 fw-bold text-white">{{ $workspace->customers_count }}</div>
                        <div class="small text-muted">عميل مسجل</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription & Revenue Card -->
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3 text-gold"><i class="bi-file-text-dollar me-2"></i>تفاصيل الاشتراك المالي</h5>
            @if($subscription)
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                    <span class="text-white-50">الخطة الحالية:</span>
                    <span class="fw-bold text-white text-uppercase">{{ $subscription->plan_name }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                    <span class="text-white-50">السعر الشهري:</span>
                    <span class="fw-bold text-success">${{ $subscription->price }} / شهرياً</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                    <span class="text-white-50">تاريخ التجديد:</span>
                    <span class="text-white">{{ $subscription->renews_at ? \Carbon\Carbon::parse($subscription->renews_at)->format('Y-m-d') : 'N/A' }}</span>
                </div>
            @else
                <div class="text-center py-3 px-2 rounded mb-2" style="background: rgba(255,255,255,0.03); border: 1px dashed rgba(212,175,55,0.3); color: rgba(255,255,255,0.85); font-size: 0.85rem;">
                    <i class="bi bi-info-circle text-gold me-1"></i> خطة افتراضية بدون اشتراك مسجل
                </div>
            @endif

            <!-- Update Plan Form -->
            <form action="{{ route('admin.workspaces.update-plan', $workspace->id) }}" method="POST" class="mt-3">
                @csrf
                <div class="mb-2">
                    <label class="form-label text-white small fw-bold">تعديل الخطة وسعر الاشتراك:</label>
                    <select name="plan_id" class="form-select bg-dark text-white border-secondary mb-2" required>
                        <option value="starter" {{ ($workspace->plan_id ?? '') == 'starter' ? 'selected' : '' }}>Starter ($19/mo)</option>
                        <option value="pro" {{ ($workspace->plan_id ?? '') == 'pro' ? 'selected' : '' }}>Pro ($49/mo)</option>
                        <option value="enterprise" {{ ($workspace->plan_id ?? '') == 'enterprise' ? 'selected' : '' }}>Enterprise ($99/mo)</option>
                    </select>
                    <input type="number" step="1" name="price" class="form-control bg-dark text-white border-secondary mb-2" value="{{ $subscription->price ?? 49 }}" placeholder="السعر الشهري ($)">
                </div>
                <button type="submit" class="btn btn-outline-warning w-100 btn-sm rounded-pill fw-bold">
                    تحديث الخطة المالية
                </button>
            </form>
        </div>
    </div>

    <!-- Right Detailed Tabs Column -->
    <div class="col-12 col-lg-8">
        <div class="card card-custom p-4">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs border-secondary border-opacity-25 mb-4" id="wsDetailTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold" id="tab-bot-btn" data-bs-toggle="tab" data-bs-target="#tab-bot" type="button">
                        <i class="bi bi-robot me-2"></i>إعدادات البوت والـ AI
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="tab-users-btn" data-bs-toggle="tab" data-bs-target="#tab-users" type="button">
                        <i class="bi bi-people me-2"></i>المستخدمين والملاك ({{ $workspace->users->count() }})
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="tab-kb-btn" data-bs-toggle="tab" data-bs-target="#tab-kb" type="button">
                        <i class="bi bi-database me-2"></i>المعرفة والقواعد
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="tab-edit-btn" data-bs-toggle="tab" data-bs-target="#tab-edit" type="button">
                        <i class="bi bi-gear me-2"></i>تعديل بيانات المتجر
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="wsDetailTabsContent">
                
                <!-- TAB 1: Bot & AI Tuning Form -->
                <div class="tab-pane fade show active" id="tab-bot" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-gold mb-0"><i class="bi bi-sliders me-1"></i>التحكم المباشر في المساعد الذكي للمتجر</h6>
                        <span class="badge {{ $bot->is_active ? 'bg-success' : 'bg-danger' }} bg-opacity-25 {{ $bot->is_active ? 'text-success' : 'text-danger' }}">
                            {{ $bot->is_active ? 'البوت مفعل' : 'البوت معطل' }}
                        </span>
                    </div>

                    <form action="{{ route('admin.workspaces.update-bot', $workspace->id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-white small">اسم البوت:</label>
                                <input type="text" name="name" class="form-control bg-dark text-white border-secondary" value="{{ $bot->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white small">مزود الذكاء الاصطناعي (AI Provider):</label>
                                <select name="ai_provider" class="form-select bg-dark text-white border-secondary" required>
                                    <option value="gemini" {{ $bot->ai_provider == 'gemini' ? 'selected' : '' }}>Google Gemini</option>
                                    <option value="openai" {{ $bot->ai_provider == 'openai' ? 'selected' : '' }}>OpenAI (ChatGPT)</option>
                                    <option value="anthropic" {{ $bot->ai_provider == 'anthropic' ? 'selected' : '' }}>Anthropic Claude</option>
                                    <option value="openai_compatible" {{ $bot->ai_provider == 'openai_compatible' ? 'selected' : '' }}>OpenAI Compatible</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label text-white small mb-0">معرف النموذج (Model ID):</label>
                                    <button type="button" class="btn btn-link text-gold p-0 fs-8 text-decoration-none" onclick="fetchAdminModels()">
                                        <i class="bi bi-arrow-repeat me-1"></i> جلب النماذج
                                    </button>
                                </div>
                                <div id="adminModelInputContainer">
                                    <input type="text" name="model_type" id="admin_model_type" class="form-control bg-dark text-white border-secondary" value="{{ $bot->model_type ?? 'gemini-1.5-flash' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white small">نبرة الرد (Bot Tone):</label>
                                <select name="bot_tone" class="form-select bg-dark text-white border-secondary" required>
                                    <option value="friendly" {{ ($bot->bot_tone ?? 'friendly') == 'friendly' ? 'selected' : '' }}>ودودة وترحيبية (Friendly)</option>
                                    <option value="formal" {{ ($bot->bot_tone ?? '') == 'formal' ? 'selected' : '' }}>رسمية واحترافية (Formal)</option>
                                    <option value="sales" {{ ($bot->bot_tone ?? '') == 'sales' ? 'selected' : '' }}>تسويقية تشجع على الشراء (Sales)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white small">درجة الإبداع (Temperature):</label>
                                <input type="number" step="0.05" min="0" max="1" name="temperature" class="form-control bg-dark text-white border-secondary" value="{{ $bot->temperature ?? 0.7 }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white small">الحد الأقصى للرموز (Max Tokens):</label>
                                <input type="number" step="50" min="50" max="4000" name="max_tokens" class="form-control bg-dark text-white border-secondary" value="{{ $bot->max_tokens ?? 1000 }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-white small">التوجيه المخصص (System Prompt):</label>
                                <textarea name="system_prompt" rows="4" class="form-control bg-dark text-white border-secondary font-monospace" required>{{ $bot->system_prompt }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="botIsActive" {{ $bot->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label text-white fs-8" for="botIsActive">تفعيل الرد التلقائي للبوت</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_rag" id="botEnableRag" {{ ($bot->enable_rag ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label text-white fs-8" for="botEnableRag">تفعيل استرجاع المعرفة (RAG)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_auto_rules" id="botEnableRules" {{ ($bot->enable_auto_rules ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label text-white fs-8" for="botEnableRules">تفعيل القواعد الفورية (FAQ)</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">
                                    <i class="bi bi-save me-1"></i> حفظ وتحديث إعدادات البوت
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: Users & Owners -->
                <div class="tab-pane fade" id="tab-users" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-gold mb-0"><i class="bi bi-people me-1"></i>قائمة المستخدمين والموظفين التابعين للمتجر</h6>
                    </div>

                    <div class="table-responsive">
                        <table class="table custom-dark-table align-middle">
                            <thead>
                                <tr>
                                    <th>المستخدم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الدور</th>
                                    <th>الهاتف</th>
                                    <th class="text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($workspace->users as $u)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                {{ mb_substr($u->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-white fs-8">{{ $u->name }}</div>
                                                <div class="text-white-50 fs-9">{{ $u->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-white-50 fs-8">{{ $u->email }}</td>
                                    <td>
                                        <span class="badge bg-dark border border-secondary text-gold fs-9 text-uppercase">{{ $u->role }}</span>
                                    </td>
                                    <td class="text-white-50 fs-8 phone-num" dir="ltr">{{ $u->phone ?? '—' }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-3 py-1 fs-9" data-bs-toggle="modal" data-bs-target="#resetUserPassModal{{ $u->id }}">
                                            <i class="bi bi-key me-1"></i> إعادة تعيين كلمة المرور
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-white-50 py-3">لا يوجد مستخدمين مسجلين لهذه الشركة</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: Knowledge Base & Rules Preview -->
                <div class="tab-pane fade" id="tab-kb" role="tabpanel">
                    <h6 class="fw-bold text-gold mb-3"><i class="bi bi-file-earmark-text me-2"></i>المستندات المدربة (Knowledge Base)</h6>
                    <div class="d-flex flex-column gap-2 mb-4">
                        @forelse($bot->knowledgeBases as $kb)
                        <div class="p-3 rounded bg-dark border border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold text-white small"><i class="bi bi-filetype-pdf text-gold me-2"></i>{{ $kb->title ?? 'مستند بدون عنوان' }}</div>
                                <div class="text-white-50 fs-9">{{ $kb->created_at->format('Y-m-d') }} | عدد المقاطع: {{ is_array($kb->chunks) ? count($kb->chunks) : 1 }} مقطع</div>
                            </div>
                            <span class="badge bg-success bg-opacity-25 text-success">مفعل</span>
                        </div>
                        @empty
                        <div class="text-center py-4 px-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(212,175,55,0.25); color: rgba(255,255,255,0.85); font-size: 0.9rem;">
                            <i class="bi bi-folder-x text-gold me-1 fs-5 d-block mb-1"></i> لا توجد ملفات مدربة مرفوعة لهذا المتجر حتى الآن.
                        </div>
                        @endforelse
                    </div>

                    <h6 class="fw-bold text-gold mb-3"><i class="bi bi-lightning-charge me-2"></i>القواعد التلقائية الفورية (Auto-Rules)</h6>
                    <div class="d-flex flex-column gap-2">
                        @forelse($bot->autoRules as $rule)
                        <div class="p-3 rounded bg-dark border border-secondary border-opacity-25">
                            <div class="fw-bold text-white small mb-1">س: {{ $rule->question }}</div>
                            <div class="text-white-50 fs-9">ج: {{ $rule->reply_template }}</div>
                        </div>
                        @empty
                        <div class="text-center py-4 px-3 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(212,175,55,0.25); color: rgba(255,255,255,0.85); font-size: 0.9rem;">
                            <i class="bi bi-question-circle text-gold me-1 fs-5 d-block mb-1"></i> لا توجد قواعد فورية مضافة لهذا المتجر.
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- TAB 4: Edit Store Info -->
                <div class="tab-pane fade" id="tab-edit" role="tabpanel">
                    <h6 class="fw-bold text-gold mb-3">تعديل البيانات الأساسية للشركة / المتجر</h6>
                    <form action="{{ route('admin.workspaces.update', $workspace->id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-white small">اسم المتجر / الشركة:</label>
                                <input type="text" name="company_name" class="form-control bg-dark text-white border-secondary" value="{{ $workspace->company_name }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-white small">حالة الحساب:</label>
                                <select name="status" class="form-select bg-dark text-white border-secondary" required>
                                    <option value="active" {{ $workspace->status == 'active' ? 'selected' : '' }}>نشطة (Active)</option>
                                    <option value="suspended" {{ $workspace->status == 'suspended' ? 'selected' : '' }}>موقوفة (Suspended)</option>
                                    <option value="trial" {{ $workspace->status == 'trial' ? 'selected' : '' }}>فترة تجريبية (Trial)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-white small">الخطة / الباقة:</label>
                                <select name="plan_id" class="form-select bg-dark text-white border-secondary" required>
                                    <option value="starter" {{ ($workspace->plan_id ?? '') == 'starter' ? 'selected' : '' }}>Starter</option>
                                    <option value="pro" {{ ($workspace->plan_id ?? '') == 'pro' ? 'selected' : '' }}>Pro</option>
                                    <option value="enterprise" {{ ($workspace->plan_id ?? '') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch p-2 rounded" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.2);">
                                    <input class="form-check-input ms-2" type="checkbox" name="allow_custom_api_key" id="allowCustomKey" {{ $workspace->allow_custom_api_key ? 'checked' : '' }}>
                                    <label class="form-check-label text-white fw-bold fs-8" for="allowCustomKey">
                                        السماح للمتجر باستخدام مفتاح API ونموذج مخصص خاص به (BYOK - Bring Your Own Key)
                                    </label>
                                    <div class="text-muted fs-9 ps-4">عند التعطيل، يتم قفل حقول المفتاح في إعدادات العميل وتشغيل الذكاء الاصطناعي عبر خوادم المنصة المركزية.</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">حفظ تعديلات المتجر والسياسات</button>
                            </div>
                        </div>
                    </form>

                    @if($workspace->id != 1)
                    <hr class="border-secondary border-opacity-25 my-4">
                    <div class="p-3 rounded" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3);">
                        <h6 class="text-danger fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>منطقة الخطر (Danger Zone)</h6>
                        <p class="text-white-50 small mb-3">حذف هذا المتجر سيؤدي لحذف جميع محادثاته ومستخدميه وبوتاته وقواعد معرفته بشكل نهائي لا يمكن التراجع عنه.</p>
                        <form action="{{ route('admin.workspaces.destroy', $workspace->id) }}" method="POST" onsubmit="return confirm('تحذير نهائي: هل أنت متأكد من رغبتك في حذف هذا المتجر بالكامل؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold">
                                <i class="bi bi-trash me-1"></i> حذف المتجر نهائياً
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Password Reset Modals (Placed Outside Table to Prevent Backdrop Stacking Issues) -->
@foreach($workspace->users as $u)
<div class="modal fade" id="resetUserPassModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-warning"><i class="bi bi-key-fill me-2"></i>تعيين كلمة مرور جديدة لـ: {{ $u->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.reset-password', $u->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white small">كلمة المرور الجديدة (6 أحرف على الأقل):</label>
                        <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required minlength="6" placeholder="******">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small">تأكيد كلمة المرور:</label>
                        <input type="password" name="password_confirmation" class="form-control bg-dark text-white border-secondary" required minlength="6" placeholder="******">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">تحديث كلمة المرور</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@section('scripts')
<script>
async function fetchAdminModels() {
    const providerSelect = document.querySelector('select[name="ai_provider"]');
    const container = document.getElementById('adminModelInputContainer');
    const currentModel = document.getElementById('admin_model_type')?.value || '';
    const provider = providerSelect ? providerSelect.value : 'gemini';

    container.innerHTML = '<div class="text-warning small py-1"><span class="spinner-border spinner-border-sm me-1"></span> جاري جلب النماذج...</div>';

    try {
        const res = await fetch("{{ route('admin.workspaces.fetch-models') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ ai_provider: provider })
        });

        const data = await res.json();
        if (data.success && data.models && data.models.length > 0) {
            let optionsHtml = '';
            data.models.forEach(m => {
                const isSelected = m === currentModel ? 'selected' : '';
                optionsHtml += `<option value="${m}" ${isSelected}>${m}</option>`;
            });

            container.innerHTML = `
                <select name="model_type" id="admin_model_type" class="form-select bg-dark text-white border-secondary" required>
                    ${optionsHtml}
                </select>
            `;
        } else {
            container.innerHTML = `<input type="text" name="model_type" id="admin_model_type" class="form-control bg-dark text-white border-secondary" value="${currentModel}" required>`;
            alert(data.message || 'تعذر جلب النماذج.');
        }
    } catch (e) {
        container.innerHTML = `<input type="text" name="model_type" id="admin_model_type" class="form-control bg-dark text-white border-secondary" value="${currentModel}" required>`;
        alert('خطأ في جلب النماذج: ' + e.message);
    }
}
</script>
@endsection
@endsection
