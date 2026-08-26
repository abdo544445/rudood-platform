@extends('admin.layouts.app')

@section('title', 'إدارة الشركات والمتاجر')
@section('page_title', 'دليل الشركات ومتاجر العملاء')

@section('content')
<!-- Search & Filter + Create Store Button Card -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h5 class="fw-bold text-white mb-1"><i class="bi bi-building text-gold me-2"></i>التحكم الشامل في المتاجر والشركات</h5>
            <p class="text-white-50 small mb-0">يمكنك إنشاء متاجر جديدة، تعديل الخطط والبيانات، تسجيل الدخول كمالك متجر، أو إدارة إعدادات البوت لكل عميل.</p>
        </div>
        <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createStoreModal">
            <i class="bi bi-plus-circle-fill"></i> إضافة متجر / شركة جديدة
        </button>
    </div>

    <form method="GET" action="{{ route('admin.workspaces.index') }}" class="row g-3 align-items-center pt-3 border-top border-secondary border-opacity-25">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control bg-dark text-white border-secondary" 
                       placeholder="ابحث باسم المتجر، بريد المالك، أو اسم المالك..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-12 col-md-3">
            <select name="status" class="form-select bg-dark text-white border-secondary">
                <option value="">جميع الحالات</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة (Active)</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>موقوفة (Suspended)</option>
                <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>تجريبية (Trial)</option>
            </select>
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold">
                <i class="bi-funnel me-1"></i> تصفية
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.workspaces.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                    إلغاء التصفية
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Modal: Create New Workspace & Owner Account -->
<div class="modal fade" id="createStoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-gold"><i class="bi bi-building-add me-2"></i>إنشاء متجر / مساحة عمل جديدة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.workspaces.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12"><h6 class="fw-bold text-warning border-bottom border-secondary pb-1">1. بيانات المتجر / الشركة</h6></div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">اسم المتجر / الشركة:</label>
                            <input type="text" name="company_name" class="form-control bg-dark text-white border-secondary" placeholder="مثال: متجر كي زون الرقمي" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">الخطة / الباقة:</label>
                            <select name="plan_id" class="form-select bg-dark text-white border-secondary" required>
                                <option value="starter">Starter ($19/mo)</option>
                                <option value="pro" selected>Pro ($49/mo)</option>
                                <option value="enterprise">Enterprise ($99/mo)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">حالة الحساب:</label>
                            <select name="status" class="form-select bg-dark text-white border-secondary" required>
                                <option value="active" selected>نشط (Active)</option>
                                <option value="trial">تجريبي (Trial)</option>
                                <option value="suspended">موقوف (Suspended)</option>
                            </select>
                        </div>

                        <div class="col-12 mt-4"><h6 class="fw-bold text-warning border-bottom border-secondary pb-1">2. بيانات حساب المالك (Store Owner)</h6></div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">اسم المالك الكامل:</label>
                            <input type="text" name="owner_name" class="form-control bg-dark text-white border-secondary" placeholder="مثال: خالد المطيري" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">البريد الإلكتروني لتسجيل الدخول:</label>
                            <input type="email" name="owner_email" class="form-control bg-dark text-white border-secondary" placeholder="owner@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">رقم الهاتف (اختياري):</label>
                            <input type="text" name="owner_phone" class="form-control bg-dark text-white border-secondary" placeholder="966500000000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">كلمة المرور المبدئية:</label>
                            <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required minlength="6" placeholder="******">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">إنشاء وتفعيل المتجر فوراً</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Workspaces Table Card -->
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">قائمة الشركات والمتاجر المسجلة ({{ $workspaces->total() }})</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-dark-custom mb-0 align-middle">
            <thead>
                <tr>
                    <th>المعرف</th>
                    <th>اسم الشركة / المتجر</th>
                    <th>المالك الرئيسي</th>
                    <th>إحصائيات الاستخدام</th>
                    <th>الخطة الحالية</th>
                    <th>الحالة</th>
                    <th>تاريخ التسجيل</th>
                    <th class="text-center">الإجراءات والتحكم</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workspaces as $ws)
                @php $owner = $ws->users->first(); @endphp
                <tr>
                    <td class="text-muted">#{{ $ws->id }}</td>
                    <td>
                        <div class="fw-bold text-white fs-6">{{ $ws->company_name }}</div>
                        <div class="small text-muted">{{ $ws->customers_count }} عميل مستهدف</div>
                    </td>
                    <td>
                        @if($owner)
                            <div class="fw-bold text-white small">{{ $owner->name }}</div>
                            <div class="small text-muted">{{ $owner->email }}</div>
                        @else
                            <span class="text-muted small">لا يوجد مالك مسجل</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <span class="badge bg-info bg-opacity-25 text-info" title="عدد البوتات">
                                <i class="bi-robot me-1"></i> {{ $ws->bots_count }} بوت
                            </span>
                            <span class="badge bg-primary bg-opacity-25 text-primary" title="عدد المحادثات">
                                <i class="bi-chat-dots me-1"></i> {{ $ws->conversations_count }}
                            </span>
                            <span class="badge bg-secondary bg-opacity-50" title="المستخدمين">
                                <i class="bi-people me-1"></i> {{ $ws->users_count }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-warning bg-opacity-25 text-warning fw-bold text-uppercase">
                            {{ $ws->plan_id ?? 'Starter' }}
                        </span>
                    </td>
                    <td>
                        @if($ws->status === 'active')
                            <span class="badge bg-success bg-opacity-25 text-success">نشطة</span>
                        @elseif($ws->status === 'suspended')
                            <span class="badge bg-danger bg-opacity-25 text-danger">موقوفة</span>
                        @else
                            <span class="badge bg-warning bg-opacity-25 text-warning">{{ $ws->status }}</span>
                        @endif
                    </td>
                    <td class="text-muted small">
                        {{ $ws->created_at ? $ws->created_at->format('Y-m-d') : 'N/A' }}
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <!-- رابط عرض وإدارة المتجر بالكامل -->
                            <a href="{{ route('admin.workspaces.show', $ws->id) }}" class="btn btn-sm btn-outline-info rounded-3" title="ملف المتجر والتحكم في البوت">
                                <i class="bi bi-eye me-1"></i> إدارة
                            </a>

                            <!-- زر تبديل الهوية (Impersonate) -->
                            <form action="{{ route('admin.workspaces.impersonate', $ws->id) }}" method="POST" onsubmit="return confirm('تسجيل الدخول والتصفح كمالك لمتجر ({{ $ws->company_name }})؟')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning rounded-3" title="تسجيل الدخول كمالك المتجر">
                                    <i class="bi bi-person-badge"></i>
                                </button>
                            </form>

                            <!-- زر تعديل المتجر عبر Modal -->
                            <button type="button" class="btn btn-sm btn-outline-light rounded-3" data-bs-toggle="modal" data-bs-target="#editWsModal{{ $ws->id }}" title="تعديل بيانات المتجر والخطة">
                                <i class="bi bi-pencil"></i>
                            </button>

                            @if($ws->id != 1)
                            <!-- حذف المتجر -->
                            <form action="{{ route('admin.workspaces.destroy', $ws->id) }}" method="POST" onsubmit="return confirm('تحذير: هل أنت متأكد من رغبتك في حذف متجر ({{ $ws->company_name }}) وجميع محادثاته وبياناته نهائياً؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="حذف المتجر">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>

                        <!-- Modal تعديل بيانات المتجر السريع -->
                        <div class="modal fade" id="editWsModal{{ $ws->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content bg-dark text-white border-secondary">
                                    <div class="modal-header border-secondary">
                                        <h5 class="modal-title fw-bold text-gold">تعديل متجر: {{ $ws->company_name }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.workspaces.update', $ws->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small">اسم المتجر / الشركة:</label>
                                                <input type="text" name="company_name" class="form-control bg-dark text-white border-secondary" value="{{ $ws->company_name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small">حالة الحساب:</label>
                                                <select name="status" class="form-select bg-dark text-white border-secondary" required>
                                                    <option value="active" {{ $ws->status == 'active' ? 'selected' : '' }}>نشطة (Active)</option>
                                                    <option value="suspended" {{ $ws->status == 'suspended' ? 'selected' : '' }}>إيقاف مؤقت (Suspended)</option>
                                                    <option value="trial" {{ $ws->status == 'trial' ? 'selected' : '' }}>فترة تجريبية (Trial)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small">الخطة / الباقة:</label>
                                                <select name="plan_id" class="form-select bg-dark text-white border-secondary" required>
                                                    <option value="starter" {{ $ws->plan_id == 'starter' ? 'selected' : '' }}>Starter</option>
                                                    <option value="pro" {{ $ws->plan_id == 'pro' ? 'selected' : '' }}>Pro</option>
                                                    <option value="enterprise" {{ $ws->plan_id == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-secondary">
                                            <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">حفظ التغييرات</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">لم يتم العثور على أي شركات أو متاجر تطابق نتائج البحث</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- الترقيم (Pagination) -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $workspaces->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
