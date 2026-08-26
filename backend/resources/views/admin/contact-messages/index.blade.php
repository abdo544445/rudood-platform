@extends('admin.layouts.app')

@section('title', 'رسائل واستفسارات تواصل معنا | الإدارة العليا')
@section('header_title', 'رسائل واستفسارات تواصل معنا (Contact Us Inquiries)')

@section('content')
<div class="row g-3 mb-4">
    <!-- Header Summary Card -->
    <div class="col-12">
        <div class="card-custom p-3 d-flex flex-row justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold text-white mb-1"><i class="bi bi-envelope-paper-heart text-gold me-2"></i>صندوق استفسارات العملاء والزوار</h5>
                <p class="text-white-50 fs-8 mb-0">متابعة كافة الرسائل والاستفسارات الواردة عبر صفحة "تواصل معنا" مع إمكانية تحديث الحالة والرد.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-gold text-dark px-3 py-2 fw-bold fs-8 rounded-pill">
                    <i class="bi bi-inbox me-1"></i> إجمالي الرسائل: {{ $stats['total'] }}
                </span>
            </div>
        </div>
    </div>

    <!-- Stat KPI Cards -->
    <div class="col-sm-6 col-lg-3">
        <div class="card-custom p-3 text-center border-start border-4 border-info">
            <span class="text-white-50 fs-8">إجمالي الوارد</span>
            <h3 class="fw-bold text-white mb-0 mt-1">{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-custom p-3 text-center border-start border-4 border-danger">
            <span class="text-white-50 fs-8">رسائل جديدة (لم تُعالج)</span>
            <h3 class="fw-bold text-danger mb-0 mt-1">{{ $stats['new'] }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-custom p-3 text-center border-start border-4 border-warning">
            <span class="text-white-50 fs-8">قيد المتابعة والتواصل</span>
            <h3 class="fw-bold text-gold mb-0 mt-1">{{ $stats['in_progress'] }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-custom p-3 text-center border-start border-4 border-success">
            <span class="text-white-50 fs-8">تم الحل والرد</span>
            <h3 class="fw-bold text-success mb-0 mt-1">{{ $stats['resolved'] }}</h3>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="col-12">
        <div class="card-custom p-3">
            <form method="GET" action="{{ route('admin.contacts.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="بحث بالاسم، البريد، العنوان، أو المحتوى..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديدة (New)</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد المتابعة (In Progress)</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>تم الحل (Resolved)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" placeholder="من تاريخ" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-gold flex-grow-1"><i class="bi bi-funnel-fill me-1"></i> تصفية</button>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="col-12">
        <div class="card-custom p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-dark-custom mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 70px;">#ID</th>
                            <th>المرسل والمعلومات</th>
                            <th>موضوع الرسالة</th>
                            <th>مقتطف الرسالة</th>
                            <th>الحالة</th>
                            <th>تاريخ الاستلام</th>
                            <th class="pe-3 text-end">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $msg)
                        <tr>
                            <td class="ps-3 text-white-50 fs-8">#{{ $msg->id }}</td>
                            <td>
                                <div class="fw-bold text-white fs-8">{{ $msg->name }}</div>
                                <a href="mailto:{{ $msg->email }}" class="text-gold fs-9 text-decoration-none d-flex align-items-center gap-1">
                                    <i class="bi bi-envelope"></i> {{ $msg->email }}
                                </a>
                                @if($msg->ip_address)
                                    <span class="text-white-50 fs-9 opacity-50" title="IP Address">IP: {{ $msg->ip_address }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-25 text-white border border-secondary border-opacity-25 fs-8">
                                    {{ $msg->subject ?: 'استفسار عام' }}
                                </span>
                            </td>
                            <td style="max-width: 320px;">
                                <div class="text-white-50 fs-8 text-truncate">{{ $msg->message }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $msg->status_badge_class }} rounded-pill px-2.5 py-1 fs-9">
                                    {{ $msg->status_label }}
                                </span>
                            </td>
                            <td>
                                <div class="text-white fs-8">{{ $msg->created_at->format('Y-m-d') }}</div>
                                <div class="text-white-50 fs-9">{{ $msg->created_at->format('H:i A') }} ({{ $msg->created_at->diffForHumans() }})</div>
                            </td>
                            <td class="pe-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    <!-- View Modal Trigger Button -->
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#msgModal{{ $msg->id }}" title="قراءة الرسالة">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <!-- Quick Reply Button -->
                                    <a href="mailto:{{ $msg->email }}?subject={{ rawurlencode('رد على استفسارك: ' . ($msg->subject ?: 'منصة ردود')) }}" class="btn btn-outline-info" title="الرد بالبريد الإلكتروني">
                                        <i class="bi bi-reply"></i>
                                    </a>

                                    <!-- Delete Form -->
                                    <form action="{{ route('admin.contacts.destroy', $msg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة نهائياً؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal for Full Message Details & Status Update -->
                                <div class="modal fade text-start" id="msgModal{{ $msg->id }}" tabindex="-1" aria-labelledby="msgModalLabel{{ $msg->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content bg-dark border border-secondary border-opacity-50 text-white rounded-4 shadow-lg">
                                            <div class="modal-header border-secondary border-opacity-25 pb-2">
                                                <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2" id="msgModalLabel{{ $msg->id }}">
                                                    <i class="bi bi-envelope-open text-gold"></i>
                                                    رسالة استفسار #{{ $msg->id }} — {{ $msg->name }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <span class="text-white-50 fs-9 d-block">اسم المرسل:</span>
                                                        <strong class="text-white fs-8">{{ $msg->name }}</strong>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <span class="text-white-50 fs-9 d-block">البريد الإلكتروني:</span>
                                                        <a href="mailto:{{ $msg->email }}" class="text-gold fs-8">{{ $msg->email }}</a>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <span class="text-white-50 fs-9 d-block">موضوع الاستفسار:</span>
                                                        <span class="text-white fs-8">{{ $msg->subject ?: 'استفسار عام' }}</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <span class="text-white-50 fs-9 d-block">تاريخ الإرسال:</span>
                                                        <span class="text-white-50 fs-8">{{ $msg->created_at->format('Y-m-d H:i:s') }}</span>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <span class="text-white-50 fs-9 d-block mb-1">نص الرسالة الكامل:</span>
                                                    <div class="p-3 rounded-3 bg-black bg-opacity-50 border border-secondary border-opacity-25 text-white fs-8 lh-lg" style="white-space: pre-wrap;">{{ $msg->message }}</div>
                                                </div>

                                                <!-- Status Update Form -->
                                                <form action="{{ route('admin.contacts.update-status', $msg->id) }}" method="POST">
                                                    @csrf
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label text-white-50 fs-8">تحديث حالة المعالجة</label>
                                                            <select name="status" class="form-select fs-8 bg-dark border-secondary border-opacity-50 text-white">
                                                                <option value="new" {{ $msg->status == 'new' ? 'selected' : '' }}>جديدة (New)</option>
                                                                <option value="in_progress" {{ $msg->status == 'in_progress' ? 'selected' : '' }}>قيد المتابعة (In Progress)</option>
                                                                <option value="resolved" {{ $msg->status == 'resolved' ? 'selected' : '' }}>تم الحل والرد (Resolved)</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <label class="form-label text-white-50 fs-8">ملاحظات المشرف الإدارية (داخلية)</label>
                                                            <input type="text" name="admin_notes" class="form-control fs-8 bg-dark border-secondary border-opacity-50 text-white" value="{{ $msg->admin_notes }}" placeholder="مثال: تم التواصل مع العميل هاتفياً وإرسال العرض...">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-secondary border-opacity-25 mt-4 px-0 pb-0 d-flex justify-content-between">
                                                        <a href="mailto:{{ $msg->email }}?subject={{ rawurlencode('رد على استفسارك: ' . ($msg->subject ?: 'منصة ردود')) }}" class="btn btn-outline-info rounded-pill px-4 fs-8">
                                                            <i class="bi bi-reply-fill me-1"></i> الرد عبر البريد الإلكتروني
                                                        </a>
                                                        <button type="submit" class="btn btn-gold rounded-pill px-4 fs-8 fw-bold">
                                                            <i class="bi bi-check2-circle me-1"></i> حفظ التحديثات
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-white-50">
                                <i class="bi bi-inbox fs-1 d-block text-gold mb-2 opacity-50"></i>
                                لا توجد رسائل تواصل واردة مطابقة لمعايير البحث.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            @if($messages->hasPages())
            <div class="p-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-white-50 fs-8">
                    عرض {{ $messages->firstItem() }} إلى {{ $messages->lastItem() }} من أصل {{ $messages->total() }} رسالة
                </span>
                <div>
                    {{ $messages->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
