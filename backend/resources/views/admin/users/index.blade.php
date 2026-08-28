@extends('admin.layouts.app')

@section('title', 'إدارة المستخدمين والملاك')
@section('page_title', 'دليل المستخدمين وملاك المتاجر')

@section('content')
@php $workspaces = $workspaces ?? \App\Models\Workspace::orderBy('company_name')->get(); @endphp
<!-- Search & Filter Card -->
<div class="card card-custom p-4 mb-4">
    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-center">
        <div class="col-12 col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control bg-dark text-white border-secondary" 
                       placeholder="ابحث بالاسم، البريد، الهاتف، أو اسم المتجر..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-12 col-md-3">
            <select name="role" class="form-select bg-dark text-white border-secondary">
                <option value="">جميع الأدوار</option>
                <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>مالك متجر (Owner)</option>
                <option value="agent" {{ request('role') == 'agent' ? 'selected' : '' }}>وكيل / موظف دعم (Agent)</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>مدير نظام (Admin)</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <select name="workspace_id" class="form-select bg-dark text-white border-secondary">
                <option value="">جميع المتاجر والشركات</option>
                @foreach($workspaces as $w)
                    <option value="{{ $w->id }}" {{ request('workspace_id') == $w->id ? 'selected' : '' }}>{{ $w->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-3 px-3 fw-bold w-100">
                <i class="bi-funnel me-1"></i> تصفية
            </button>
            @if(request()->hasAny(['search', 'role', 'workspace_id']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-3 px-2" title="إلغاء التصفية">
                    <i class="bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Users Table Card -->
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">قائمة الحسابات المسجلة ({{ $users->total() }})</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-dark-custom mb-0 align-middle">
            <thead>
                <tr>
                    <th>المستخدم</th>
                    <th>بيانات الاتصال</th>
                    <th>الشركة / المتجر التابع له</th>
                    <th>الدور والصلاحية</th>
                    <th>تاريخ الانضمام</th>
                    <th class="text-center">الإجراءات والتحكم</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--gold-dark), var(--gold)); color: #000; font-weight: bold; display: flex; align-items: center; justify-content: center;">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold text-white fs-6">{{ $user->name }}</div>
                                <div class="small text-muted">ID: #{{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-white small mb-1"><i class="bi bi-envelope me-1 text-gold"></i>{{ $user->email }}</div>
                        <div class="text-muted small phone-num" dir="ltr"><i class="bi bi-telephone me-1 text-gold"></i>{{ $user->phone ?? 'غير محدد' }}</div>
                    </td>
                    <td>
                        @if($user->workspace)
                            <a href="{{ route('admin.workspaces.show', $user->workspace_id) }}" class="badge bg-secondary bg-opacity-50 text-white text-decoration-none px-3 py-2 rounded-pill">
                                <i class="bi bi-building me-1 text-gold"></i> {{ $user->workspace->company_name }}
                            </a>
                        @else
                            <span class="badge bg-danger bg-opacity-25 text-danger">بدون مساحة عمل</span>
                        @endif
                    </td>
                    <td>
                        @if($user->role === 'admin')
                            <span class="badge bg-danger bg-opacity-25 text-danger px-3 py-2 rounded-pill fw-bold">مدير نظام (Super Admin)</span>
                        @elseif($user->role === 'owner')
                            <span class="badge bg-warning bg-opacity-25 text-warning px-3 py-2 rounded-pill fw-bold">مالك متجر (Owner)</span>
                        @else
                            <span class="badge bg-info bg-opacity-25 text-info px-3 py-2 rounded-pill">وكيل دعم (Agent)</span>
                        @endif
                    </td>
                    <td class="text-muted small">
                        {{ $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A' }}
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <!-- Edit User Button -->
                            <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3" 
                                    data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="تعديل بيانات الحساب">
                                <i class="bi bi-pencil-square me-1"></i> تعديل
                            </button>

                            <!-- Reset Password Button -->
                            <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-3" 
                                    data-bs-toggle="modal" data-bs-target="#resetPassModal{{ $user->id }}" title="إعادة تعيين كلمة المرور">
                                <i class="bi bi-key me-1"></i> كلمة المرور
                            </button>

                            @if($user->id !== auth()->id())
                            <!-- Delete User Form -->
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا المستخدم نهائياً؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2" title="حذف الحساب">
                                <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-white-50 py-5">
                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                        لا توجد حسابات تطابق معايير البحث الحالية.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- الترقيم والصفحات -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modals Container (Placed Outside Table to Prevent Backdrop Stacking Issues) -->
@foreach($users as $user)
<!-- Modal: Edit User Details -->
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-gold"><i class="bi bi-person-gear me-2"></i>تعديل بيانات: {{ $user->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white small">الاسم الكامل:</label>
                        <input type="text" name="name" class="form-control bg-dark text-white border-secondary" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small">البريد الإلكتروني:</label>
                        <input type="email" name="email" class="form-control bg-dark text-white border-secondary" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small">رقم الهاتف:</label>
                        <input type="text" name="phone" class="form-control bg-dark text-white border-secondary" value="{{ $user->phone }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small">الدور والصلاحية:</label>
                        <select name="role" class="form-select bg-dark text-white border-secondary" required>
                            <option value="owner" {{ $user->role === 'owner' ? 'selected' : '' }}>مالك متجر (Owner)</option>
                            <option value="agent" {{ $user->role === 'agent' ? 'selected' : '' }}>وكيل دعم وموظف (Agent)</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>مدير نظام أعلى (Super Admin)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small">المتجر / مساحة العمل المرتبط بها:</label>
                        <select name="workspace_id" class="form-select bg-dark text-white border-secondary" required>
                            @foreach($workspaces as $w)
                                <option value="{{ $w->id }}" {{ $user->workspace_id == $w->id ? 'selected' : '' }}>{{ $w->company_name }}</option>
                            @endforeach
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

<!-- Modal: Reset Password -->
<div class="modal fade" id="resetPassModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-warning"><i class="bi bi-key-fill me-2"></i>تعيين كلمة مرور جديدة لـ: {{ $user->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST">
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
@endsection
