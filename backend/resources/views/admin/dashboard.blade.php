@extends('admin.layouts.app')

@section('title', 'نظرة عامة والتحليلات')
@section('page_title', 'لوحة المؤشرات والتحليلات المتقدمة')

@section('content')
<div class="row g-4 mb-4">
    <!-- 1. الشركات النشطة -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">إجمالي الشركات (Workspaces)</div>
                    <div class="stat-val mt-1">{{ number_format($stats['total_workspaces']) }}</div>
                    <div class="small text-success mt-1">
                        <i class="bi bi-check-circle-fill"></i> {{ $stats['active_workspaces'] }} نشطة حالياً
                    </div>
                </div>
                <div class="stat-card-icon" style="background: rgba(212, 175, 55, 0.15); color: var(--gold);">
                    <i class="bi bi-building"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. أسطول البوتات -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">أسطول البوتات الذكية</div>
                    <div class="stat-val mt-1">{{ number_format($stats['total_bots']) }}</div>
                    <div class="small text-info mt-1">
                        <i class="bi bi-robot"></i> {{ $stats['active_bots'] }} بوتات في وضع التشغيل
                    </div>
                </div>
                <div class="stat-card-icon" style="background: rgba(212, 175, 55, 0.15); color: var(--gold);">
                    <i class="bi bi-cpu"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. نسبة الحل التلقائي -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">نسبة الرد المؤتمت (AI)</div>
                    <div class="stat-val mt-1">{{ $stats['global_resolution_rate'] }}%</div>
                    <div class="small text-warning mt-1">
                        <i class="bi bi-lightning-charge"></i> {{ number_format($stats['bot_messages']) }} رد ذكي
                    </div>
                </div>
                <div class="stat-card-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                    <i class="bi bi-cpu"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. الإيراد الشهري المتوقع -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">الدخل الشهري التقديري (MRR)</div>
                    <div class="stat-val mt-1">${{ number_format($stats['estimated_mrr']) }}</div>
                    <div class="small text-success mt-1">
                        <i class="bi bi-graph-up-arrow"></i> {{ $stats['active_subscriptions'] }} اشتراكات نشطة
                    </div>
                </div>
                <div class="stat-card-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                    <i class="bi bi-wallet"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- الصف الثاني: الرسوم البيانية -->
<div class="row g-4 mb-4">
    <!-- الرسم البياني للرسائل (7 أيام) -->
    <div class="col-12 col-lg-8">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi-graph-up-arrow text-primary me-2"></i>حركة الرسائل خلال آخر 7 أيام</h5>
                    <div class="text-muted small">مقارنة بين ردود البوت والرسائل البشرية</div>
                </div>
                <span class="badge bg-primary bg-opacity-25 text-primary px-3 py-2 rounded-pill">
                    إجمالي {{ number_format($stats['total_messages']) }} رسالة
                </span>
            </div>
            <div id="messagesTimelineChart" style="min-height: 320px;"></div>
        </div>
    </div>

    <!-- الرسم البياني لمزودي الذكاء الاصطناعي -->
    <div class="col-12 col-lg-4">
        <div class="card card-custom p-4 h-100">
            <div class="mb-3">
                <h5 class="fw-bold mb-1"><i class="bi-pie-chart text-info me-2"></i>توزيع نماذج الـ AI</h5>
                <div class="text-muted small">نسبة استخدام مزودي الذكاء الاصطناعي للبوتات</div>
            </div>
            <div id="aiProvidersChart" style="min-height: 250px;"></div>
            
            <div class="mt-3 pt-3 border-top border-secondary border-opacity-25">
                <div class="d-flex justify-content-between py-1 small">
                    <span class="text-muted"><i class="bi-circle-fill text-gold me-1"></i> Google Gemini</span>
                    <span class="fw-bold">{{ $provider_stats['gemini'] }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 small">
                    <span class="text-muted"><i class="bi-circle-fill text-success me-1"></i> OpenAI (GPT-4o)</span>
                    <span class="fw-bold">{{ $provider_stats['openai'] }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 small">
                    <span class="text-muted"><i class="bi-circle-fill text-warning me-1"></i> Anthropic Claude</span>
                    <span class="fw-bold">{{ $provider_stats['anthropic'] }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 small">
                    <span class="text-muted"><i class="bi-circle-fill text-info me-1"></i> Custom / Compatible</span>
                    <span class="fw-bold">{{ $provider_stats['openai_compatible'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- الصف الثالث: الشركات الحديثة + حالة الخدمات الفورية -->
<div class="row g-4">
    <!-- قائمة أحدث الشركات -->
    <div class="col-12 col-lg-8">
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi-building text-primary me-2"></i>أحدث الشركات المسجلة</h5>
                <a href="{{ route('admin.workspaces.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                    عرض جميع الشركات <i class="bi-arrow-left ms-1"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-dark-custom mb-0">
                    <thead>
                        <tr>
                            <th>الشركة / المساحة</th>
                            <th>المستخدمين</th>
                            <th>البوتات</th>
                            <th>المحادثات</th>
                            <th>الحالة</th>
                            <th>تاريخ الانضمام</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_workspaces as $ws)
                        <tr>
                            <td>
                                <div class="fw-bold text-white">{{ $ws->company_name }}</div>
                                <div class="small text-muted">ID: #{{ $ws->id }}</div>
                            </td>
                            <td><span class="badge bg-dark border border-secondary text-white-50 px-2.5 py-1">{{ $ws->users_count }}</span></td>
                            <td><span class="badge badge-custom-info px-2.5 py-1">{{ $ws->bots_count }}</span></td>
                            <td><span class="badge badge-custom-gold px-2.5 py-1">{{ $ws->conversations_count }}</span></td>
                            <td>
                                @if($ws->status === 'active')
                                    <span class="badge badge-custom-success px-2.5 py-1">نشطة</span>
                                @elseif($ws->status === 'suspended')
                                    <span class="badge badge-custom-danger px-2.5 py-1">موقوفة</span>
                                @else
                                    <span class="badge badge-custom-warning px-2.5 py-1">{{ $ws->status }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $ws->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">لا توجد شركات مسجلة بعد</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- حالة البنية التحتية والمستندات -->
    <div class="col-12 col-lg-4">
        <!-- حالة الخدمات -->
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="bi-hdd-network text-accent me-2"></i>حالة الخدمات الأساسية</h5>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: rgba(255,255,255,0.03);">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi-database fs-5 text-primary"></i>
                        <div>
                            <div class="fw-bold">قاعدة البيانات (Primary DB)</div>
                            <div class="small text-muted">PostgreSQL / Vector DB</div>
                        </div>
                    </div>
                    @if($system_health['database'])
                        <span class="badge badge-custom-success px-3 py-1.5 rounded-pill"><i class="bi-circle-fill me-1"></i> متصل</span>
                    @else
                        <span class="badge badge-custom-danger px-3 py-1.5 rounded-pill"><i class="bi-circle-fill me-1"></i> غير متصل</span>
                    @endif
                </div>

                <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: rgba(255,255,255,0.03);">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi-memory fs-5 text-danger"></i>
                        <div>
                            <div class="fw-bold">خادم Redis</div>
                            <div class="small text-muted">Queues & Pub/Sub</div>
                        </div>
                    </div>
                    @if($system_health['redis'])
                        <span class="badge badge-custom-success px-3 py-1.5 rounded-pill"><i class="bi-circle-fill me-1"></i> متصل</span>
                    @else
                        <span class="badge badge-custom-danger px-3 py-1.5 rounded-pill"><i class="bi-circle-fill me-1"></i> غير متصل</span>
                    @endif
                </div>

                <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: rgba(255,255,255,0.03);">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi-broadcast fs-5 text-warning"></i>
                        <div>
                            <div class="fw-bold">WebSocket Gateway</div>
                            <div class="small text-muted">Node.js / Socket.io :3000</div>
                        </div>
                    </div>
                    @if($system_health['websocket'])
                        <span class="badge badge-custom-success px-3 py-1.5 rounded-pill"><i class="bi-circle-fill me-1"></i> يعمل</span>
                    @else
                        <span class="badge badge-custom-danger px-3 py-1.5 rounded-pill"><i class="bi-circle-fill me-1"></i> معطل</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- إحصائيات تدريب الذكاء الاصطناعي -->
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi-book text-warning me-2"></i>قاعدة المعرفة والقواعد</h5>
            <div class="row text-center g-2">
                <div class="col-6">
                    <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.03);">
                        <div class="fs-4 fw-bold text-white">{{ number_format($stats['total_knowledge_docs']) }}</div>
                        <div class="text-muted small">مستند معرفي</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.03);">
                        <div class="fs-4 fw-bold text-white">{{ number_format($stats['total_auto_rules']) }}</div>
                        <div class="text-muted small">قاعدة رد فوري</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Messages Timeline Chart
    const dailyLabels = @json($daily_labels);
    const botData     = @json($daily_bot_data);
    const humanData   = @json($daily_human_data);

    const timelineOptions = {
        series: [
            { name: 'ردود الذكاء الاصطناعي (Bot)', data: botData },
            { name: 'الرسائل البشرية (Customers/Agents)', data: humanData }
        ],
        chart: {
            type: 'area',
            height: 320,
            toolbar: { show: false },
            fontFamily: 'Cairo, sans-serif',
            background: 'transparent'
        },
        colors: ['#d4af37', '#0ea5e9'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: dailyLabels,
            labels: { style: { colors: '#94a3b8' } },
            axisBorder: { color: '#334155' },
            axisTicks: { color: '#334155' }
        },
        yaxis: {
            labels: { style: { colors: '#94a3b8' } }
        },
        grid: { borderColor: '#334155', strokeDashArray: 4 },
        tooltip: { theme: 'dark' },
        legend: { labels: { colors: '#f8fafc' }, position: 'top', horizontalAlign: 'right' }
    };

    const timelineChart = new ApexCharts(document.querySelector("#messagesTimelineChart"), timelineOptions);
    timelineChart.render();

    // 2. AI Providers Donut Chart
    const providerStats = @json($provider_stats);
    const providerCounts = [
        providerStats.gemini || 0,
        providerStats.openai || 0,
        providerStats.anthropic || 0,
        providerStats.openai_compatible || 0
    ];

    const donutOptions = {
        series: providerCounts.every(v => v === 0) ? [1, 0, 0, 0] : providerCounts,
        labels: ['Google Gemini', 'OpenAI (GPT-4o)', 'Anthropic Claude', 'OpenAI Compatible'],
        chart: {
            type: 'donut',
            height: 250,
            fontFamily: 'Cairo, sans-serif',
            background: 'transparent'
        },
        colors: ['#d4af37', '#10b981', '#f59e0b', '#0ea5e9'],
        stroke: { show: false },
        dataLabels: { enabled: false },
        legend: { show: false },
        tooltip: { theme: 'dark' },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'إجمالي البوتات',
                            color: '#94a3b8',
                            formatter: function (w) {
                                return {{ $stats['total_bots'] }};
                            }
                        },
                        value: {
                            color: '#fff',
                            fontSize: '1.5rem',
                            fontWeight: 700
                        }
                    }
                }
            }
        }
    };

    const donutChart = new ApexCharts(document.querySelector("#aiProvidersChart"), donutOptions);
    donutChart.render();
});
</script>
@endsection
