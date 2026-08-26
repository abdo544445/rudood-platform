@extends('layouts.app')

@section('title', 'لوحة التحكم | منصة ردود')

@section('styles')
<!-- Styles are now mostly handled by theme.blade.php -->
<style>
  /* حل مشكلة اختفاء النصوص داخل الشارات (Badges) */
  .badge-auto {
    background-color: var(--gold) !important;
    color: var(--bg-dark) !important;
    font-weight: 700 !important;
    padding: 6px 14px !important;
    border-radius: 50px !important;
    font-size: 0.8rem !important;
    display: inline-block !important;
  }

  .badge-human {
    background-color: #0ea5e9 !important;
    color: #000000 !important;
    font-weight: 700 !important;
    padding: 6px 14px !important;
    border-radius: 50px !important;
    font-size: 0.8rem !important;
    display: inline-block !important;
  }

  /* شارة حالة البوت بالعلوي */
  .status-badge {
    background: rgba(46, 204, 113, 0.15) !important;
    color: #2ecc71 !important;
    border: 1px solid #2ecc71;
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
  }
</style>
@endsection

@section('content')
<!-- الشريط العلوي -->
<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
  <div>
    <h3 class="fw-bold text-white mb-1">أهلاً بك، {{ auth()->user()->name ?? 'متجر الأمجاد' }} 👋</h3>
    <p class="text-white-50 mb-0 fs-7">إليك ملخص أداء مساعدك الذكي لهذا اليوم</p>
  </div>
  <div>
    <span class="status-badge">
      <i class="bi bi-circle-fill me-1 fs-9"></i> البوت متصل الآن
    </span>
  </div>
</div>

<!-- Row 1: كروت الإحصائيات (Primary KPIs) -->
<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-chat-left-text-fill"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">إجمالي المحادثات</span>
        <h3 class="fw-bold text-white mb-0">{{ $stats['total_conversations'] ?? '0' }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-robot"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">ردود الذكاء الاصطناعي</span>
        <h3 class="fw-bold text-white mb-0">{{ $stats['resolution_rate'] ?? '0%' }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-people-fill"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">العملاء النشطون</span>
        <h3 class="fw-bold text-white mb-0">{{ $stats['new_inquiries'] ?? '0' }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-clock-history"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">متوسط سرعة الرد</span>
        <h3 class="fw-bold text-white mb-0">{{ $stats['avg_response_time'] ?? '—' }}</h3>
      </div>
    </div>
  </div>
</div>

<!-- Row 2: Secondary Cards -->
<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-cpu"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">البوتات النشطة</span>
        <h3 class="fw-bold text-white mb-0">{{ $secondary_stats['active_bots'] ?? '0' }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-person-badge"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">مستخدمو الفريق</span>
        <h3 class="fw-bold text-white mb-0">{{ $secondary_stats['team_users'] ?? '0' }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-file-earmark-text"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">مستندات المعرفة</span>
        <h3 class="fw-bold text-white mb-0">{{ $secondary_stats['knowledge_docs'] ?? '0' }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-plug"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">القنوات المتصلة</span>
        <h3 class="fw-bold text-white mb-0">{{ $secondary_stats['connected_channels'] ?? '0' }}</h3>
      </div>
    </div>
  </div>
</div>

<!-- Row 3: Charts -->
<div class="row g-4 mb-4">
  <div class="col-md-8">
    <div class="stat-card h-100 p-4">
      <h5 class="fw-bold text-white mb-4"><i class="bi bi-graph-up text-gold me-2"></i>نشاط الرسائل (آخر 7 أيام)</h5>
      <div id="messagesChart" style="min-height: 250px;"></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card h-100 p-4">
      <h5 class="fw-bold text-white mb-4"><i class="bi bi-pie-chart text-gold me-2"></i>توزيع القنوات</h5>
      <div id="channelsChart" style="min-height: 250px;"></div>
    </div>
  </div>
</div>

<!-- Row 4: Tables -->
<div class="row g-4 mb-4">
  <!-- Recent Conversations -->
  <div class="col-md-6">
    <div class="stat-card p-0 overflow-hidden h-100">
      <div class="p-4 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
        <h5 class="fw-bold text-white mb-0"><i class="bi bi-clock text-gold me-2"></i>آخر المحادثات</h5>
      </div>
      <table class="custom-dark-table mb-0">
        <thead>
          <tr>
            <th>العميل</th>
            <th>القناة</th>
            <th>حالة الرد</th>
            <th>الوقت</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($recent_conversations ?? [] as $conv)
          <tr>
            <td class="fw-bold">{{ $conv->customer?->name ?? 'عميل جديد' }}</td>
            <td>
              @php $platform = $conv->customer?->platform ?? 'web'; @endphp
              <i class="bi bi-{{ $platform === 'whatsapp' ? 'whatsapp text-success' : ($platform === 'instagram' ? 'instagram text-danger' : 'globe text-info') }} me-1"></i>
              {{ ucfirst($platform) }}
            </td>
            <td>
              @if ($conv->status === 'human_handling')
                <span class="badge-human">محول للموظف</span>
              @else
                <span class="badge-auto">آلي</span>
              @endif
            </td>
            <td class="text-white-50 fs-7">{{ $conv->updated_at->diffForHumans() }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center py-4 text-white-50">لا توجد محادثات</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Connected Channels -->
  <div class="col-md-6">
    <div class="stat-card p-0 overflow-hidden h-100">
      <div class="p-4 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
        <h5 class="fw-bold text-white mb-0"><i class="bi bi-link-45deg text-gold me-2"></i>حالة القنوات المتصلة</h5>
      </div>
      <table class="custom-dark-table mb-0">
        <thead>
          <tr>
            <th>القناة</th>
            <th>الحالة</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($channels ?? [] as $ch)
          <tr>
            <td class="fw-bold">
              <i class="bi bi-{{ $ch->platform === 'whatsapp' ? 'whatsapp text-success' : ($ch->platform === 'instagram' ? 'instagram text-danger' : 'globe text-info') }} me-1"></i>
              {{ ucfirst($ch->platform) }}
            </td>
            <td>
              @if($ch->is_connected)
                <span class="text-success"><i class="bi bi-check-circle me-1"></i> متصل</span>
              @else
                <span class="text-danger"><i class="bi bi-x-circle me-1"></i> مفصول</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="2" class="text-center py-4 text-white-50">لا توجد قنوات</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="row g-4 mb-5">
  <!-- Recent Rules -->
  <div class="col-md-6">
    <div class="stat-card p-0 overflow-hidden h-100">
      <div class="p-4 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
        <h5 class="fw-bold text-white mb-0"><i class="bi bi-list-check text-gold me-2"></i>أحدث القواعد المضافة</h5>
      </div>
      <table class="custom-dark-table mb-0">
        <thead>
          <tr>
            <th>السؤال/الكلمة الدلالية</th>
            <th>الإجابة المخصصة</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($recent_rules ?? [] as $rule)
          <tr>
            <td class="fw-bold">{{ Str::limit($rule->question ?? (is_array($rule->keywords) ? implode(', ', $rule->keywords) : 'قاعدة عامة'), 30) }}</td>
            <td class="text-white-50 fs-7">{{ Str::limit($rule->reply_template, 40) }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="2" class="text-center py-4 text-white-50">لا توجد قواعد مسجلة</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recent AI Decisions -->
  <div class="col-md-6">
    <div class="stat-card p-0 overflow-hidden h-100">
      <div class="p-4 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
        <h5 class="fw-bold text-white mb-0"><i class="bi bi-cpu text-gold me-2"></i>أحدث قرارات الـ AI</h5>
      </div>
      <table class="custom-dark-table mb-0">
        <thead>
          <tr>
            <th>نوع الرد</th>
            <th>وقت المعالجة</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($recent_decisions ?? [] as $decision)
          <tr>
            <td class="fw-bold">
              @if($decision->trigger === 'auto_rule') <span class="badge bg-warning text-dark px-2">قاعدة</span>
              @elseif($decision->trigger === 'ai_api') <span class="badge bg-primary px-2">ذكاء RAG</span>
              @else <span class="badge bg-secondary px-2">احتياطي</span> @endif
            </td>
            <td class="text-white-50 fs-7">{{ $decision->response_time_ms }} ms</td>
          </tr>
          @empty
          <tr>
            <td colspan="2" class="text-center py-4 text-white-50">لا توجد سجلات قرارات</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Messages Chart (Line)
    const labels = @json($chart_labels ?? []);
    const messages = @json($chart_messages ?? []);
    
    if (labels.length > 0) {
        const msgOptions = {
            series: [{ name: 'رسائل', data: messages }],
            chart: {
                type: 'area',
                height: 250,
                toolbar: { show: false },
                background: 'transparent',
                fontFamily: 'var(--font)'
            },
            colors: ['#d4af37'],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] }
            },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: labels,
                labels: { style: { colors: '#94a3b8' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { labels: { style: { colors: '#94a3b8' } } },
            grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4 },
            theme: { mode: 'dark' }
        };
        new ApexCharts(document.querySelector("#messagesChart"), msgOptions).render();
    }

    // 2. Channels Donut
    const donutData = @json($channel_donut ?? []);
    const donutLabels = Object.keys(donutData);
    const donutValues = Object.values(donutData);
    
    if (donutLabels.length > 0) {
        const donutOptions = {
            series: donutValues,
            chart: {
                type: 'donut',
                height: 250,
                background: 'transparent',
                fontFamily: 'var(--font)'
            },
            labels: donutLabels.map(l => l.toUpperCase()),
            colors: ['#d4af37', '#10b981', '#0ea5e9', '#f43f5e'],
            stroke: { show: false },
            plotOptions: { pie: { donut: { size: '75%' } } },
            legend: { position: 'bottom', labels: { colors: '#fff' } },
            theme: { mode: 'dark' }
        };
        new ApexCharts(document.querySelector("#channelsChart"), donutOptions).render();
    }
});
</script>
@endsection
