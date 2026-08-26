@extends('admin.layouts.app')

@section('title', 'لوحة الإحصائيات الكاملة')
@section('page_title', 'لوحة الإحصائيات الشاملة والمراقبة المباشرة (Platform Statistics & Telemetry)')

@section('content')
<!-- شريط التحديث المباشر والمعلومات العامة -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-3 rounded-4" style="background: rgba(30, 41, 59, 0.6); border: 1px solid var(--card-border);">
    <div class="d-flex align-items-center gap-3">
        <div class="stat-card-icon">
            <i class="bi bi-broadcast"></i>
        </div>
        <div>
            <div class="fw-bold text-white fs-6">مركز المراقبة والتحليلات الشاملة</div>
            <div class="text-muted small">مراقبة حية لحظية للشركات، البوتات، الرسائل، قرارات الذكاء الاصطناعي، وطوابير المعالجة</div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span id="liveSocketBadge" class="badge bg-secondary bg-opacity-25 text-light border border-secondary px-3 py-2 rounded-pill small">
            <i class="bi-arrow-repeat me-1"></i> جاري فحص الاتصال بالخادم...
        </span>
        <button class="btn btn-sm btn-outline-light rounded-pill px-3 py-2" onclick="window.location.reload();">
            <i class="bi-arrow-clockwise me-1"></i> تحديث يدوي
        </button>
    </div>
</div>

<!-- A.2.1: Revenue & Subscriptions (NEW Row) -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">إجمالي الاشتراكات النشطة</div>
                    <div class="stat-val mt-1 text-gold">{{ number_format($subscriptionStats['active_subscriptions'] ?? 0) }}</div>
                    <div class="small mt-1 text-muted">
                        {{ $subscriptionStats['trial_count'] ?? 0 }} فترة تجريبية
                    </div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-credit-card"></i></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">الإيرادات الشهرية المتوقعة MRR</div>
                    <div class="stat-val mt-1 text-gold">${{ number_format($subscriptionStats['estimated_mrr'] ?? 0) }}</div>
                    <div class="small mt-1 text-muted">شهرياً</div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-cash-coin"></i></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">الإيرادات السنوية المتوقعة ARR</div>
                    <div class="stat-val mt-1 text-gold">${{ number_format($subscriptionStats['estimated_arr'] ?? 0) }}</div>
                    <div class="small mt-1 text-muted">سنوياً</div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-graph-up-arrow"></i></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">أكبر الاشتراكات</div>
                    <div class="mt-2" style="max-height: 50px; overflow:hidden;">
                        @foreach(($subscriptionStats['breakdown'] ?? collect())->take(2) as $sub)
                            <div class="small text-muted mb-1">{{ $sub['workspace']['company_name'] ?? 'Unknown' }}: <span class="text-white">${{ $sub['price'] }}</span></div>
                        @endforeach
                    </div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-trophy"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Row 1 — Global KPIs -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">إجمالي مساحات العمل</div>
                    <div class="stat-val mt-1">{{ number_format($stats['total_workspaces']) }}</div>
                    <div class="small mt-1 text-success">
                        <i class="bi-check-circle-fill me-1"></i> {{ $stats['active_workspaces'] }} نشطة
                        @if($stats['suspended_workspaces'] > 0)
                            <span class="text-danger ms-1">({{ $stats['suspended_workspaces'] }} معلقة)</span>
                        @endif
                    </div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-building"></i></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">أسطول البوتات الذكية</div>
                    <div class="stat-val mt-1">{{ number_format($stats['total_bots']) }}</div>
                    <div class="small mt-1 text-info">
                        <i class="bi-cpu me-1"></i> {{ $stats['active_bots'] }} تعمل حالياً ({{ $stats['total_auto_rules'] }} قاعدة تلقائية)
                    </div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-robot"></i></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">إجمالي المحادثات</div>
                    <div class="stat-val mt-1">{{ number_format($stats['total_conversations']) }}</div>
                    <div class="small mt-1 text-warning">
                        <i class="bi-lightning-charge me-1"></i> {{ $stats['global_resolution_rate'] }}% نسبة الرد الذكي
                    </div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-chat-dots"></i></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">إجمالي الرسائل المتداولة</div>
                    <div class="stat-val mt-1">{{ number_format($stats['total_messages']) }}</div>
                    <div class="small mt-1 text-success">
                        <i class="bi-robot me-1"></i> {{ number_format($stats['bot_messages']) }} بوت / {{ number_format($stats['human_messages']) }} بشر
                    </div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-chat-text"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2 — Time Series Charts -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h5 class="fw-bold text-white mb-1">
                        <i class="bi-graph-up text-gold me-2"></i>نشاط المحادثات والرسائل خلال 14 يوماً
                    </h5>
                    <div class="text-muted small">متابعة حجم الرسائل البشرية مقابل ردود الذكاء الاصطناعي والمحادثات المنشأة</div>
                </div>
            </div>
            <div id="fullActivityTimelineChart" style="min-height: 330px;"></div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card card-custom p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold text-white mb-1">
                    <i class="bi-cpu text-gold me-2"></i>أسطول نماذج الذكاء الاصطناعي
                </h5>
                <div class="text-muted small mb-3">توزيع مزودي الذكاء الاصطناعي المشغلين للبوتات</div>
                <div id="statsAiProvidersChart" style="min-height: 220px;"></div>
            </div>
            
            <div class="mt-3 pt-3 border-top border-secondary border-opacity-25">
                <div class="fw-bold text-white small mb-2"><i class="bi-list-check me-1 text-gold"></i> قرارات لكل مزود:</div>
                @foreach($providerUsage ?? [] as $provider => $count)
                <div class="d-flex justify-content-between py-1 small">
                    <span class="text-muted"><i class="bi-circle-fill text-gold me-1"></i> {{ strtoupper($provider) }}</span>
                    <span class="fw-bold text-light">{{ number_format($count) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- A.2.2 & A.2.4: Live Activity Feed & Daily Ops Trend -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0">
                    <i class="bi-broadcast text-success me-2"></i>آخر النشاطات اللحظية
                </h5>
                <span class="badge bg-success bg-opacity-25 text-success small px-2 py-1"><i class="bi-circle-fill-dot me-1"></i>Live</span>
            </div>
            <div id="liveFeedContainer" class="d-flex flex-column gap-2 pe-2" style="max-height: 350px; overflow-y: auto;">
                <div class="text-center text-muted py-4 small" id="liveFeedLoading">
                    <i class="bi-arrow-repeat fs-4 mb-2 d-block opacity-50"></i>
                    جاري تحميل النشاط اللحظي...
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-white mb-3">
                <i class="bi-graph-up-arrow text-gold me-2"></i>العمليات والأخطاء (14 يوم)
            </h5>
            <div id="dailyOpsChart" style="min-height: 300px;"></div>
        </div>
    </div>
</div>

<!-- Row 3: البنية التحتية، الطوابير، والأخطاء -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-white mb-3">
                <i class="bi-hdd-network text-success me-2"></i>حالة الخدمات الأساسية
            </h5>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi-database text-gold fs-5"></i>
                        <div>
                            <div class="fw-bold text-white small">قاعدة البيانات الرئيسية</div>
                            <div class="text-muted fs-8">Engine ({{ ucfirst(\Illuminate\Support\Facades\DB::getDriverName()) }})</div>
                        </div>
                    </div>
                    @if($systemHealth['database'])
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-2 py-1 rounded-pill">متصل ✓</span>
                    @else
                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 px-2 py-1 rounded-pill">خطأ ✗</span>
                    @endif
                </div>

                <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi-lightning-charge text-danger fs-5"></i>
                        <div>
                            <div class="fw-bold text-white small">خادم الذاكرة Redis</div>
                        </div>
                    </div>
                    @if($systemHealth['redis'])
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-2 py-1 rounded-pill">متصل ✓</span>
                    @else
                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 px-2 py-1 rounded-pill">غير مفعل</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 2. مراقبة طوابير المعالجة & Failed Jobs -->
    <div class="col-12 col-md-6 col-lg-8">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0">
                    <i class="bi-list-check text-warning me-2"></i>طوابير المعالجة والأخطاء
                </h5>
                @if($queueStats['failed_jobs'] > 0)
                <form action="{{ route('admin.statistics.prune-failed') }}" method="POST" onsubmit="return confirm('تأكيد مسح كافة المهام المتعثرة؟')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi-trash me-1"></i> تفريغ/مسح الأخطاء
                    </button>
                </form>
                @endif
            </div>
            
            <div class="d-flex gap-3 mb-3">
                <div class="p-3 rounded-3 text-center flex-fill" style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border);">
                    <div class="text-muted small">المهام في الانتظار</div>
                    <div class="fs-3 fw-bold text-white mt-1">{{ number_format($queueStats['pending_jobs']) }}</div>
                </div>
                <div class="p-3 rounded-3 text-center flex-fill" style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border);">
                    <div class="text-muted small">المهام المتعثرة</div>
                    <div class="fs-3 fw-bold {{ $queueStats['failed_jobs'] > 0 ? 'text-danger' : 'text-success' }} mt-1">
                        {{ number_format($queueStats['failed_jobs']) }}
                    </div>
                </div>
            </div>

            @if($queueStats['failed_jobs'] > 0)
            <div style="max-height: 150px; overflow-y: auto;" class="border border-danger border-opacity-25 rounded p-2">
                @foreach($failedJobs ?? [] as $fj)
                <div class="small text-muted mb-1 text-truncate" title="{{ $fj->exception }}">
                    <span class="text-danger">[{{ $fj->failed_at }}]</span> {{ $fj->queue }} - {{ Str::limit($fj->exception, 80) }}
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<!-- A.2.5 & A.2.6: KB Health & Channels Matrix -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-4">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-white mb-3">
                <i class="bi-file-text text-gold me-2"></i>صحة مستندات المعرفة
            </h5>
            <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                <span class="text-muted">إجمالي المستندات:</span>
                <span class="fw-bold text-white">{{ number_format($knowledgeHealth['total_docs'] ?? 0) }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                <span class="text-muted">المستندات المهجورة (Orphaned):</span>
                <span class="fw-bold {{ ($knowledgeHealth['orphaned_docs'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($knowledgeHealth['orphaned_docs'] ?? 0) }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                <span class="text-muted">إجمالي القواعد التلقائية:</span>
                <span class="fw-bold text-white">{{ number_format($knowledgeHealth['total_rules'] ?? 0) }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-8">
        <div class="card card-custom p-0 overflow-hidden h-100">
            <div class="p-4 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                <h5 class="fw-bold text-white mb-0"><i class="bi-diagram-3 text-gold me-2"></i>حالة ربط القنوات للمساحات</h5>
            </div>
            <div class="table-responsive" style="max-height: 250px;">
                <table class="table table-dark-custom mb-0 align-middle">
                    <thead style="position: sticky; top: 0; z-index: 1;">
                        <tr>
                            <th>الشركة</th>
                            <th>المنصة</th>
                            <th>الحالة</th>
                            <th>آخر خطأ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($channelConnectivity ?? [] as $ch)
                        <tr>
                            <td class="text-white">{{ $ch->company_name }}</td>
                            <td>{{ ucfirst($ch->platform) }}</td>
                            <td>
                                @if($ch->is_connected)
                                    <span class="badge bg-success bg-opacity-25 text-success rounded-pill">متصل ✓</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-25 text-danger rounded-pill">مفصول ✗</span>
                                @endif
                            </td>
                            <td class="text-muted small text-truncate" style="max-width: 150px;" title="{{ $ch->last_error }}">{{ $ch->last_error ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">لا توجد قنوات مسجلة</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Row 4 — Per-Workspace Telemetry Table -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h5 class="fw-bold text-white mb-1">
                <i class="bi-table text-gold me-2"></i>أداء ومراقبة أسطول الشركات (Workspace Fleet)
            </h5>
            <div class="text-muted small">ترتيب أكبر 5 شركات، وعرض تفصيلي لجميع المساحات</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-dark-custom mb-0 align-middle">
            <thead>
                <tr>
                    <th>الشركة / المساحة</th>
                    <th>الحالة</th>
                    <th>المستخدمين</th>
                    <th>البوتات</th>
                    <th>المحادثات</th>
                    <th>إجمالي الرسائل</th>
                    <th>نسبة الرد الذكي</th>
                    <th>مزود الـ AI</th>
                    <th>الباقة</th>
                    <th>آخر نشاط</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <!-- Sort workspaces by conversations desc to show top first implicitly -->
                @php 
                    $sortedWorkspaces = collect($workspaces)->sortByDesc('conversations_count')->values(); 
                @endphp
                @forelse($sortedWorkspaces as $index => $ws)
                    <tr class="{{ $index < 5 ? 'border-start border-4 border-warning' : '' }}">
                        <td>
                            <div class="fw-bold text-white">{{ $ws['company_name'] }}</div>
                            <div class="text-muted fs-8">ID: #{{ $ws['id'] }}</div>
                        </td>
                        <td>
                            @if($ws['status'] === 'active')
                                <span class="badge bg-success bg-opacity-25 text-success rounded-pill">نشطة</span>
                            @elseif($ws['status'] === 'suspended')
                                <span class="badge bg-danger bg-opacity-25 text-danger rounded-pill">معلقة</span>
                            @else
                                <span class="badge bg-warning bg-opacity-25 text-warning rounded-pill">{{ $ws['status'] }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-25 text-light px-2 py-1">
                                <i class="bi-people me-1 text-muted"></i> {{ $ws['users_count'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-25 text-info px-2 py-1">
                                <i class="bi-robot me-1"></i> {{ $ws['bots_count'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-gold text-dark fw-bold px-2 py-1">
                                <i class="bi-chat-dots me-1"></i> {{ $ws['conversations_count'] }}
                            </span>
                        </td>
                        <td class="fw-bold text-white">
                            {{ number_format($ws['messages_count']) }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px; background: rgba(255,255,255,0.1); width: 60px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $ws['resolution_rate'] }}%"></div>
                                </div>
                                <span class="small fw-bold text-light">{{ $ws['resolution_rate'] }}%</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-dark border border-secondary text-info px-2 py-1">
                                {{ strtoupper($ws['ai_provider']) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-25 text-light px-2 py-1">
                                {{ $ws['plan'] }}
                            </span>
                        </td>
                        <td class="text-muted small">
                            {{ $ws['last_activity'] }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.workspaces.show', $ws['id']) }}" class="btn btn-sm btn-outline-info px-2 py-1 rounded-2" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.workspaces.impersonate', $ws['id']) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning px-2 py-1 rounded-2" title="تسجيل الدخول كمالك">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            لا توجد شركات مسجلة في المنصة حالياً.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ─── Live Activity Polling ───────────────────────────────────────────
    function fetchLiveFeed() {
        fetch('{{ route('admin.statistics.live') }}')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('liveFeedContainer');
                if(!data.success || data.data.length === 0) {
                    container.innerHTML = '<div class="text-center text-muted py-4 small">لا توجد نشاطات في الـ 15 دقيقة الماضية</div>';
                    return;
                }
                container.innerHTML = '';
                data.data.forEach(msg => {
                    const custName = msg.conversation?.customer?.name || 'مجهول';
                    const platform = msg.conversation?.customer?.platform || 'web';
                    container.innerHTML += `
                        <div class="p-2 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px solid var(--card-border);">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold text-white small">${custName} <span class="badge bg-secondary bg-opacity-25 text-light fs-9 ms-1">${platform}</span></span>
                                <span class="text-muted fs-8">${new Date(msg.created_at).toLocaleTimeString('ar-EG')}</span>
                            </div>
                            <div class="small text-muted text-truncate">${msg.content}</div>
                        </div>
                    `;
                });
            })
            .catch(err => console.error('Live feed err:', err));
    }
    
    // Initial fetch and poll every 10s
    fetchLiveFeed();
    setInterval(fetchLiveFeed, 10000);

    // ─── Time Series Chart ──────────────────────────────────
    const dailyLabels = @json($dailyMessages['labels'] ?? []);
    const botMessages = @json($dailyMessages['bot'] ?? []);
    const humanMessages = @json($dailyMessages['human'] ?? []);
    const conversationsData = @json($dailyConversations['data'] ?? []);

    const timelineOptions = {
        series: [
            { name: 'ردود الذكاء الاصطناعي', type: 'area', data: botMessages.length ? botMessages : [0] },
            { name: 'رسائل العملاء والموظفين', type: 'area', data: humanMessages.length ? humanMessages : [0] },
            { name: 'المحادثات المنشأة', type: 'line', data: conversationsData.length ? conversationsData : [0] }
        ],
        chart: { height: 330, type: 'line', toolbar: { show: false }, background: 'transparent', fontFamily: 'var(--font)' },
        colors: ['#d4af37', '#0ea5e9', '#10b981'],
        fill: { type: ['gradient', 'gradient', 'solid'], gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 95, 100] } },
        stroke: { curve: 'smooth', width: [3, 3, 2], dashArray: [0, 0, 4] },
        xaxis: { categories: dailyLabels.length ? dailyLabels : ['اليوم'], labels: { style: { colors: '#94a3b8', fontSize: '11px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '11px' } } },
        grid: { borderColor: '#334155', strokeDashArray: 4 },
        theme: { mode: 'dark' },
        tooltip: { theme: 'dark', shared: true, intersect: false },
        legend: { position: 'top', horizontalAlign: 'right', labels: { colors: '#f8fafc' } }
    };
    new ApexCharts(document.querySelector("#fullActivityTimelineChart"), timelineOptions).render();

    // ─── Daily Ops Trend Chart ──────────────────────────────────
    const opsLabels = @json($dailyOperations['labels'] ?? []);
    const failures = @json($dailyOperations['failures'] ?? []);
    const opsOptions = {
        series: [{ name: 'أخطاء مهام فاشلة', data: failures.length ? failures : [0] }],
        chart: { height: 300, type: 'area', toolbar: { show: false }, background: 'transparent', fontFamily: 'var(--font)' },
        colors: ['#ef4444'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: { categories: opsLabels, labels: { style: { colors: '#94a3b8' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#94a3b8' } } },
        grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4 },
        theme: { mode: 'dark' }
    };
    new ApexCharts(document.querySelector("#dailyOpsChart"), opsOptions).render();

    // ─── AI Providers Donut Chart ─────────────────────────────────────────
    const providerCounts = [
        {{ $providerStats['gemini'] ?? 0 }},
        {{ $providerStats['openai'] ?? 0 }},
        {{ $providerStats['anthropic'] ?? 0 }},
        {{ $providerStats['openai_compatible'] ?? 0 }}
    ];
    const hasProviders = providerCounts.some(v => v > 0);
    const providerOptions = {
        series: hasProviders ? providerCounts : [1, 0, 0, 0],
        chart: { type: 'donut', height: 220, background: 'transparent', fontFamily: 'var(--font)' },
        labels: ['Google Gemini', 'OpenAI', 'Anthropic', 'OpenAI Compatible'],
        colors: ['#d4af37', '#10b981', '#f59e0b', '#f43f5e'],
        dataLabels: { enabled: false },
        stroke: { show: false },
        plotOptions: { pie: { donut: { size: '72%' } } },
        legend: { show: false },
        theme: { mode: 'dark' },
        tooltip: { theme: 'dark' }
    };
    new ApexCharts(document.querySelector("#statsAiProvidersChart"), providerOptions).render();
});
</script>
@endsection
