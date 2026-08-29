@extends('admin.layouts.app')

@section('title', 'حالة النظام والبنية التحتية')
@section('page_title', 'مراقبة البنية التحتية والخدمات الحية')

@section('content')
<!-- بطاقة وضع الصيانة العام -->
<div class="card card-custom p-4 mb-4 border {{ ($maintenance['is_active'] ?? false) ? 'border-danger' : 'border-warning border-opacity-30' }}" style="background: linear-gradient(145deg, {{ ($maintenance['is_active'] ?? false) ? 'rgba(239, 68, 68, 0.08)' : 'rgba(212, 175, 55, 0.05)' }} 0%, rgba(15, 23, 42, 0.95) 100%);">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge {{ ($maintenance['is_active'] ?? false) ? 'bg-danger text-white' : 'bg-dark border border-secondary border-opacity-40 text-gold' }} px-3 py-1 fs-8 fw-bold">
                    <i class="bi {{ ($maintenance['is_active'] ?? false) ? 'bi-exclamation-octagon-fill' : 'bi-tools' }} me-1"></i>
                    {{ ($maintenance['is_active'] ?? false) ? 'وضع الصيانة: نشط حالياً' : 'وضع الصيانة: غير مفعل (النظام متاح للجميع)' }}
                </span>
                @if(!empty($maintenance['scheduled_ends_at']))
                    <span class="badge bg-dark border border-secondary border-opacity-40 text-info fs-8">
                        <i class="bi bi-clock-history me-1"></i> موعد الانتهاء: {{ date('Y-m-d H:i', strtotime($maintenance['scheduled_ends_at'])) }}
                    </span>
                @endif
            </div>
            <h4 class="fw-bold text-white mb-1">{{ $maintenance['title'] ?? 'أعمال صيانة وتطوير مجدولة' }}</h4>
            <p class="text-white-50 fs-8 mb-0" style="max-width: 650px;">{{ $maintenance['message'] ?? 'نقوم حالياً بإجراء تحديثات دورية وتطويرات هامة على أنظمة منصة ردود...' }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="/maintenance" target="_blank" class="btn btn-sm btn-outline-secondary text-white-50 rounded-pill px-3 py-2 fs-8">
                <i class="bi bi-eye me-1"></i> معاينة شاشة الصيانة
            </a>
            <button type="button" class="btn btn-sm btn-gold rounded-pill px-4 py-2 fw-bold fs-8 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#maintenanceControlModal">
                <i class="bi bi-sliders"></i>
                <span>تعديل إعدادات الصيانة</span>
            </button>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- 1. حالة قاعدة البيانات -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">{{ $db_driver_name ?? 'قاعدة البيانات (DB)' }}</div>
                    <div class="stat-val mt-1 fs-4 text-white">{{ $db_size_mb }} MB</div>
                    <div class="small mt-1 {{ str_contains($db_status, 'متصل') ? 'text-success' : 'text-danger' }}">
                        <i class="bi bi-circle-fill"></i> {{ $db_status }}
                    </div>
                </div>
                <div class="stat-card-icon" style="background: rgba(212, 175, 55, 0.15); color: var(--gold);">
                    <i class="bi bi-database"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. حالة خادم Redis -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">خادم Redis</div>
                    <div class="stat-val mt-1 fs-4 text-white">{{ $redis_memory }}</div>
                    <div class="small mt-1 text-info">
                        <i class="bi bi-key"></i> {{ $redis_keys_count }} مفتاح مخزن
                    </div>
                </div>
                <div class="stat-card-icon" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">
                    <i class="bi bi-memory"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. خادم WebSocket -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">WebSocket Gateway</div>
                    <div class="stat-val mt-1 fs-4 text-white">Socket.io</div>
                    <div class="small mt-1 {{ str_contains($websocket_status, 'يعمل') ? 'text-success' : 'text-danger' }}">
                        <i class="bi bi-broadcast"></i> {{ $websocket_status }}
                    </div>
                </div>
                <div class="stat-card-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                    <i class="bi bi-lightning-charge"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. طوابير العمليات (Queues) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-lbl">طابور مهام الـ AI (Jobs)</div>
                    <div class="stat-val mt-1 fs-4 text-white">{{ $pending_jobs_count }} قيد الانتظار</div>
                    <div class="small mt-1 {{ $failed_jobs_count > 0 ? 'text-danger' : 'text-success' }}">
                        <i class="bi bi-exclamation-triangle"></i> {{ $failed_jobs_count }} وظيفة فاشلة
                    </div>
                </div>
                <div class="stat-card-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                    <i class="bi bi-list-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- تفاصيل بيئة التشغيل -->
    <div class="col-12 col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi-sliders text-primary me-2"></i>بيئة النظام والتشغيل</h5>
            
            <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                <span class="text-muted">إصدار PHP:</span>
                <span class="fw-bold text-white">{{ $php_version }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                <span class="text-muted">إصدار Laravel:</span>
                <span class="fw-bold text-white">{{ $laravel_version }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                <span class="text-muted">بيئة التطبيق (Environment):</span>
                <span class="badge bg-info bg-opacity-25 text-info text-uppercase">{{ $environment }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                <span class="text-muted">وضع التنقيح (Debug Mode):</span>
                <span class="fw-bold {{ config('app.debug') ? 'text-warning' : 'text-success' }}">{{ $debug_mode }}</span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">حجم مجلد التخزين (Storage):</span>
                <span class="fw-bold text-white">{{ $storage_size_mb }} MB</span>
            </div>
        </div>
    </div>

    <!-- جداول قاعدة البيانات -->
    <div class="col-12 col-lg-7">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi-table text-info me-2"></i>أحجام جداول قاعدة البيانات</h5>
            
            <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                <table class="table table-dark-custom mb-0 table-sm">
                    <thead>
                        <tr>
                            <th>اسم الجدول</th>
                            <th>عدد الصفوف التقريبي</th>
                            <th>الحجم (MB)</th>
                            <th>المحرك</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tables_info as $tbl)
                        <tr>
                            <td class="fw-bold text-white font-monospace">{{ $tbl['name'] }}</td>
                            <td>{{ number_format($tbl['rows']) }}</td>
                            <td class="text-info">{{ $tbl['size'] }}</td>
                            <td class="text-muted small">{{ $tbl['engine'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">لا تتوفر معلومات الجداول</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- سجلات النظام الحية (Logs) -->
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0"><i class="bi-terminal text-warning me-2"></i>سجل أحداث النظام (Recent Logs)</h5>
            <div class="text-muted small">آخر 30 سطر من ملف storage/logs/laravel.log</div>
        </div>
        <button onclick="window.location.reload()" class="btn btn-sm btn-outline-light rounded-pill px-3">
            <i class="bi-arrow-repeat me-1"></i> تحديث السجل
        </button>
    </div>

    <div class="p-3 rounded-3" style="background: #090d16; border: 1px solid var(--admin-border); max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 0.85rem; line-height: 1.6;">
        @forelse($recent_logs as $logLine)
            @if(empty(trim($logLine))) @continue @endif
            <div class="{{ str_contains($logLine, 'ERROR') || str_contains($logLine, 'Exception') ? 'text-danger fw-bold' : (str_contains($logLine, 'WARNING') ? 'text-warning' : 'text-light') }} border-bottom border-secondary border-opacity-10 py-1">
                {{ $logLine }}
            </div>
        @empty
            <div class="text-muted text-center py-4">سجل الأحداث نظيف ولا توجد أخطاء مسجلة ✓</div>
        @endforelse
    </div>
</div>
@endsection
