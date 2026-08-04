@extends('layouts.app')

@section('title', 'لوحة التحكم | منصة ردود')

@section('styles')
<style>
  /* البطاقات الزجاجية */
  .stat-card {
    background: rgba(255, 255, 255, 0.03) !important;
    backdrop-filter: blur(12px);
    border: 1px solid rgba(212, 175, 55, 0.2) !important;
    border-radius: 16px;
    padding: 20px;
  }

  .icon-box-dash {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(212, 175, 55, 0.15);
    color: #d4af37;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
  }

  /* إصلاح الجدول */
  .custom-dark-table {
    width: 100%;
    border-collapse: collapse;
    color: #ffffff !important;
    background: transparent !important;
  }

  .custom-dark-table th {
    background: rgba(212, 175, 55, 0.15) !important;
    color: #d4af37 !important;
    padding: 15px;
    border-bottom: 1px solid rgba(212, 175, 55, 0.3);
    text-align: right;
  }

  .custom-dark-table td {
    background: rgba(15, 23, 42, 0.6) !important;
    color: #ffffff !important;
    padding: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  }

  .custom-dark-table tr:hover td {
    background: rgba(212, 175, 55, 0.08) !important;
  }

  /* حل مشكلة اختفاء النصوص داخل الشارات (Badges) */
  .badge-auto {
    background-color: #d4af37 !important;
    color: #0b0f19 !important;
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

<!-- كروت الإحصائيات -->
<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-chat-left-text-fill"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">إجمالي المحادثات</span>
        <h3 class="fw-bold text-white mb-0">{{ $stats['total_conversations'] ?? '1,248' }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-robot"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">ردود الذكاء الاصطناعي</span>
        <h3 class="fw-bold text-white mb-0">{{ $stats['resolution_rate'] ?? '94%' }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-people-fill"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">العملاء النشطون</span>
        <h3 class="fw-bold text-white mb-0">{{ $stats['new_inquiries'] ?? '312' }}</h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="stat-card d-flex align-items-center gap-3">
      <div class="icon-box-dash"><i class="bi bi-clock-history"></i></div>
      <div>
        <span class="text-white-50 d-block fs-7">متوسط سرعة الرد</span>
        <h3 class="fw-bold text-white mb-0">{{ $stats['avg_response_time'] ?? '1.2 ثانية' }}</h3>
      </div>
    </div>
  </div>
</div>

<!-- الجدول الداكن المعدل -->
<div class="mt-5">
  <h4 class="fw-bold text-white mb-3"><i class="bi bi-clock text-gold me-2"></i>آخر المحادثات التفاعلية</h4>
  <div class="rounded-4 overflow-hidden border border-warning border-opacity-25">
    <table class="custom-dark-table">
      <thead>
        <tr>
          <th>اسم العميل</th>
          <th>القناة</th>
          <th>آخر استفسار</th>
          <th>حالة الرد</th>
          <th>التاريخ والوقت</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($recent_conversations as $conv)
        <tr>
          <td class="fw-bold">{{ $conv->customer->name ?? 'عميل جديد' }}</td>
          <td>
            <i class="bi bi-{{ $conv->customer->platform === 'whatsapp' ? 'whatsapp text-success' : ($conv->customer->platform === 'instagram' ? 'instagram text-danger' : 'globe text-info') }} me-1"></i>
            {{ ucfirst($conv->customer->platform ?? 'web') }}
          </td>
          <td>{{ Str::limit($conv->messages->first()?->content ?? 'لا توجد رسائل', 45) }}</td>
          <td>
            @if ($conv->status === 'human_handling')
              <span class="badge-human">محول للموظف</span>
            @else
              <span class="badge-auto">تم الرد آلياً</span>
            @endif
          </td>
          <td class="text-white-50">{{ $conv->updated_at->diffForHumans() }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-4 text-white-50">
            <i class="bi bi-chat-square-x me-2 fs-5"></i> لا توجد محادثات حديثة بعد
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
