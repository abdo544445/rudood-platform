@extends('admin.layouts.app')

@section('title', 'سجل تدقيق الأنشطة والعمليات | الإدارة العليا')
@section('header_title', 'سجل تدقيق الأنشطة (Audit Trail)')

@section('content')
<div class="row g-3 mb-4">
    <!-- Header Summary Card -->
    <div class="col-12">
        <div class="card-custom p-3 d-flex flex-row justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold text-white mb-1"><i class="bi bi-shield-check text-gold me-2"></i>سجل الحركات والأمان المؤسسي</h5>
                <p class="text-white-50 fs-8 mb-0">توثيق كامل لكافة التغييرات والإجراءات الإدارية وتدخلات الشات وتعديلات البوتات.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-gold text-dark px-3 py-2 fw-bold fs-8 rounded-pill">
                    <i class="bi bi-list-check me-1"></i> إجمالي العمليات: {{ $logs->total() }}
                </span>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="col-12">
        <div class="card-custom p-3">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="action" class="form-control" placeholder="بحث بنوع الإجراء..." value="{{ request('action') }}">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">جميع الأقسام</option>
                        <option value="system" {{ request('category') == 'system' ? 'selected' : '' }}>النظام (System)</option>
                        <option value="chat" {{ request('category') == 'chat' ? 'selected' : '' }}>المحادثات (Chat & Takeover)</option>
                        <option value="bot" {{ request('category') == 'bot' ? 'selected' : '' }}>البوتات (Bots & AI)</option>
                        <option value="user" {{ request('category') == 'user' ? 'selected' : '' }}>المستخدمين (Users & Passwords)</option>
                        <option value="security" {{ request('category') == 'security' ? 'selected' : '' }}>الأمان (Security)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="workspace_id" class="form-select">
                        <option value="">جميع المتاجر</option>
                        @foreach($workspaces as $w)
                            <option value="{{ $w->id }}" {{ request('workspace_id') == $w->id ? 'selected' : '' }}>{{ $w->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-gold flex-grow-1"><i class="bi bi-funnel-fill me-1"></i> تصفية</button>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
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
                            <th class="ps-3">#</th>
                            <th>المتجر / الشركة</th>
                            <th>المستخدم</th>
                            <th>الإجراء (Action)</th>
                            <th>التفاصيل</th>
                            <th>عنوان IP</th>
                            <th class="pe-3">الوقت والتاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-3 text-white-50 fs-8">#{{ $log->id }}</td>
                            <td>
                                @if($log->workspace)
                                    <span class="badge bg-secondary bg-opacity-25 text-gold border border-secondary border-opacity-25">
                                        <i class="bi bi-shop me-1"></i>{{ $log->workspace->company_name }}
                                    </span>
                                @else
                                    <span class="text-white-50 fs-8">عام / النظام</span>
                                @endif
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm rounded-circle bg-gold bg-opacity-20 text-gold fw-bold d-flex align-items-center justify-content-center" style="width:26px;height:26px;font-size:0.75rem;">
                                            {{ mb_substr($log->user->name, 0, 1) }}
                                        </div>
                                        <span class="fs-8 text-white fw-bold">{{ $log->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-white-50 fs-8">النظام الآلي</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-dark border border-secondary text-white font-monospace fs-8">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="fs-8 text-white-50">
                                {{ $log->description }}
                                @if($log->metadata)
                                    <details class="mt-1">
                                        <summary class="text-gold cursor-pointer" style="cursor:pointer; font-size:0.72rem;">عرض البيانات (JSON)</summary>
                                        <pre class="bg-black bg-opacity-50 p-2 rounded text-white-50 mt-1 fs-9 mb-0" dir="ltr">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </details>
                                @endif
                            </td>
                            <td class="text-white-50 fs-8 font-monospace">{{ $log->ip_address ?? '-' }}</td>
                            <td class="pe-3 text-white-50 fs-8">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-white-50">
                                <i class="bi bi-shield-x display-5 d-block mb-2 opacity-50"></i>
                                لا توجد سجلات مسجلة تطابق معايير البحث
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
            <div class="p-3 border-top border-secondary border-opacity-25 d-flex justify-content-center">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
