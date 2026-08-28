@extends('admin.layouts.app')

@section('page_title', 'إدارة طلبات المشتركين وتفعيل المتاجر')

@section('content')

@if(session('welcome_notice'))
<div class="alert alert-success card-custom border border-success border-opacity-40 p-4 mb-4 d-flex align-items-start gap-3">
    <div class="fs-2 text-success"><i class="bi bi-send-check-fill"></i></div>
    <div class="flex-grow-1">
        <h5 class="fw-bold text-white mb-1">تم إرسال رسالة الترحيب للمشترك بنجاح! 📨</h5>
        <p class="text-white-50 fs-8 mb-2">نص الرسالة المعتمدة للمشترك:</p>
        <div class="p-3 rounded-3 bg-dark border border-secondary border-opacity-30 text-gold fw-semibold fs-8">
            {{ session('welcome_notice') }}
        </div>
    </div>
</div>
@endif

<!-- إحصائيات سريعة للطلبات -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-lbl">إجمالي طلبات المشتركين</div>
                    <div class="stat-val mt-1 text-white">{{ $stats['total'] }}</div>
                </div>
                <div class="stat-card-icon" style="background: rgba(14, 165, 233, 0.15); color: #38bdf8;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 h-100 border border-warning border-opacity-30">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-lbl text-gold">بانتظار المراجعة والاتفاق</div>
                    <div class="stat-val mt-1 text-gold">{{ $stats['pending'] }}</div>
                </div>
                <div class="stat-card-icon" style="background: rgba(212, 175, 55, 0.15); color: #d4af37;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-lbl">المشتركين المعتمدين</div>
                    <div class="stat-val mt-1 text-success">{{ $stats['approved'] }}</div>
                </div>
                <div class="stat-card-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-lbl">الطلبات المرفوضة</div>
                    <div class="stat-val mt-1 text-danger">{{ $stats['rejected'] }}</div>
                </div>
                <div class="stat-card-icon" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- شريط الأدوات والفلترة -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        
        <!-- التبويبات -->
        <div class="btn-group" role="group">
            <a href="{{ route('admin.subscribers.index', ['status' => 'all', 'search' => $search]) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-gold' : 'btn-dark border border-secondary border-opacity-40 text-white-50' }} px-3">
                الكل ({{ $stats['total'] }})
            </a>
            <a href="{{ route('admin.subscribers.index', ['status' => 'pending', 'search' => $search]) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-gold' : 'btn-dark border border-secondary border-opacity-40 text-white-50' }} px-3">
                بانتظار الاتفاق <span class="badge bg-danger ms-1">{{ $stats['pending'] }}</span>
            </a>
            <a href="{{ route('admin.subscribers.index', ['status' => 'approved', 'search' => $search]) }}" class="btn btn-sm {{ $status === 'approved' ? 'btn-gold' : 'btn-dark border border-secondary border-opacity-40 text-white-50' }} px-3">
                المعتمدين ({{ $stats['approved'] }})
            </a>
        </div>

        <!-- البحث وزر الإضافة اليدوية -->
        <div class="d-flex align-items-center gap-3">
            <form action="{{ route('admin.subscribers.index') }}" method="GET" class="d-flex gap-2">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="بحث بالاسم، الإيميل، المتجر..." value="{{ $search }}" style="min-width: 220px;">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <a href="{{ route('admin.subscribers.create') }}" class="btn btn-gold btn-sm rounded-pill px-4 fw-bold d-flex align-items-center gap-2 flex-shrink-0">
                <i class="bi bi-person-plus-fill"></i>
                <span>إضافة مشترك جديد يدوياً</span>
            </a>
        </div>

    </div>
</div>

<!-- جدول طلبات المشتركين -->
<div class="card card-custom p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-custom mb-0 align-middle">
            <thead>
                <tr>
                    <th class="ps-4">المشترك</th>
                    <th>المتجر / الشركة</th>
                    <th>الباقة المختارة</th>
                    <th>وسائل التواصل</th>
                    <th>الحالة</th>
                    <th>تاريخ التقديم</th>
                    <th class="text-end pe-4">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                {{ mb_substr($req->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold text-white fs-8">{{ $req->name }}</div>
                                <div class="text-white-50 fs-9">{{ $req->email }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span class="fw-bold text-white fs-8">{{ $req->company_name ?: 'غير محدد' }}</span>
                    </td>

                    <td>
                        @if($req->selected_plan === 'enterprise')
                            <span class="badge bg-purple text-white fs-9"><i class="bi bi-gem me-1"></i> الشركات الكبرى</span>
                        @elseif($req->selected_plan === 'professional')
                            <span class="badge bg-gold text-dark fw-bold fs-9"><i class="bi bi-star-fill me-1"></i> الاحترافية</span>
                        @else
                            <span class="badge bg-dark border border-secondary text-info fs-9">البداية (Starter)</span>
                        @endif
                    </td>

                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $req->phone) }}" target="_blank" class="badge bg-success text-white text-decoration-none py-1 px-2">
                                <i class="bi bi-whatsapp me-1"></i> {{ $req->phone }}
                            </a>
                        </div>
                    </td>

                    <td>
                        @if($req->status === 'approved')
                            <span class="badge bg-success text-white fs-9"><i class="bi bi-check-circle-fill me-1"></i> معتمد ومفعل</span>
                        @elseif($req->status === 'pending')
                            <span class="badge bg-warning text-dark fw-bold fs-9"><i class="bi bi-clock-history me-1"></i> بانتظار الاتفاق</span>
                        @else
                            <span class="badge bg-danger text-white fs-9"><i class="bi bi-x-circle me-1"></i> مرفوض</span>
                        @endif
                    </td>

                    <td class="text-white-50 fs-9">
                        {{ $req->created_at ? $req->created_at->diffForHumans() : '-' }}
                    </td>

                    <td class="text-end pe-4">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            @if($req->status === 'pending')
                                <form action="{{ route('admin.subscribers.approve', $req->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-gold rounded-pill px-3 fw-bold fs-9" onclick="return confirm('هل أنت متأكد من اعتماد البريد وتفعيل مساحة العمل والبوت للمشترك وإرسال رسالة الترحيب؟')">
                                        <i class="bi bi-check-lg me-1"></i> اعتماد وتفعيل
                                    </button>
                                </form>

                                <form action="{{ route('admin.subscribers.reject', $req->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 fs-9" onclick="return confirm('تأكيد رفض أو إغلاق هذا الطلب؟')">
                                        رفض
                                    </button>
                                </form>
                            @elseif($req->status === 'approved' && $req->created_user_id)
                                <form action="{{ route('admin.workspaces.impersonate', $req->createdUser?->workspace_id ?? 1) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info rounded-pill px-3 fs-9">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> دخول كمتجر
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.subscribers.destroy', $req->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-dark text-danger border-0 p-1" onclick="return confirm('حذف هذا الطلب نهائياً؟')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-white-50">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-white-50"></i>
                        لا توجد طلبات اشتراك في هذه القائمة حالياً.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
    <div class="p-3 border-top border-secondary border-opacity-25">
        {{ $requests->links() }}
    </div>
    @endif
</div>

@endsection
