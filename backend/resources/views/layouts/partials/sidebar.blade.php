<aside class="sidebar d-flex flex-column justify-content-between py-3">
  <div>
    <div class="px-4 mb-3 text-center">
      <a href="{{ url('/') }}">
        <img src="{{ asset('images/img.png') }}" alt="شعار منصة ردود" style="max-height: 45px;">
      </a>
    </div>

    @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->role === 'admin'))
    <!-- لوحة تحكم السوبر إدمن ومحول المتاجر السريع -->
    <div class="px-3 mb-3">
      <div class="p-2 rounded-3" style="background: rgba(212,175,55,0.1); border: 1px solid rgba(212,175,55,0.4);">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-gold fw-bold fs-8"><i class="bi bi-shield-lock-fill me-1"></i> مدير النظام (Super Admin)</span>
          <a href="{{ route('admin.dashboard') }}" class="badge bg-warning text-dark text-decoration-none fs-9 fw-bold">اللوحة الرئيسية</a>
        </div>
        
        @php
          $allWorkspaces = \App\Models\Workspace::orderBy('company_name')->get();
          $currentWsId = auth()->user()->workspace_id;
        @endphp
        <form action="{{ route('admin.workspaces.switch') }}" method="POST" id="sidebarSwitchForm">
          @csrf
          <label class="text-white-50 fs-9 d-block mb-1">🏢 تصفح بصفتك متجر:</label>
          <select name="workspace_id" class="form-select form-select-sm bg-dark text-white border-warning fs-8" onchange="this.form.submit()">
            @foreach($allWorkspaces as $ws)
              <option value="{{ $ws->id }}" {{ $ws->id == $currentWsId ? 'selected' : '' }}>
                {{ $ws->company_name }} {{ $ws->id == 1 ? '(الرئيسي)' : '' }}
              </option>
            @endforeach
          </select>
        </form>
      </div>
    </div>
    @endif

    <ul class="nav nav-pills flex-column px-2">
      <div class="nav-section-label">لوحة المتجر الحالي</div>
      <li class="nav-item">
        <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }} d-flex align-items-center gap-3">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>الرئيسية</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/live-chat') }}" class="nav-link {{ request()->is('live-chat*') ? 'active' : '' }} d-flex align-items-center gap-3">
          <i class="bi bi-chat-dots-fill"></i>
          <span>المحادثات المباشرة</span>
        </a>
      </li>
      
      <div class="nav-section-label">الذكاء الاصطناعي والتدريب</div>
      <li class="nav-item">
        <a href="{{ url('/ai-manage') }}" class="nav-link {{ request()->is('ai-manage') ? 'active' : '' }} d-flex align-items-center gap-3">
          <i class="bi bi-cpu-fill"></i>
          <span>تدريب الذكاء الاصطناعي</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/playground') }}" class="nav-link {{ request()->is('playground*') ? 'active' : '' }} d-flex align-items-center gap-3">
          <i class="bi bi-robot"></i>
          <span>اختبار البوت (Playground)</span>
        </a>
      </li>
      
      <div class="nav-section-label">القنوات والتكاملات</div>
      <li class="nav-item">
        <a href="{{ url('/channels') }}" class="nav-link {{ request()->is('channels*') ? 'active' : '' }} d-flex align-items-center gap-3">
          <i class="bi bi-diagram-3-fill text-gold"></i>
          <span>ربط القنوات والتكاملات</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/settings') }}" class="nav-link {{ request()->is('settings*') ? 'active' : '' }} d-flex align-items-center gap-3">
          <i class="bi bi-gear-fill"></i>
          <span>إعدادات البوت والذكاء</span>
        </a>
      </li>

      @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->role === 'admin'))
      <div class="nav-section-label">لوحة الإدارة العليا</div>
      <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link d-flex align-items-center gap-3" style="color: var(--gold) !important; border: 1px dashed rgba(212,175,55,0.4);">
          <i class="bi bi-shield-lock-fill"></i>
          <span>لوحة Super Admin المركزية</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('admin.workspaces.index') }}" class="nav-link d-flex align-items-center gap-3" style="color: #6ee7b7 !important;">
          <i class="bi bi-building"></i>
          <span>إدارة جميع المتاجر ({{ \App\Models\Workspace::count() }})</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('admin.users.index') }}" class="nav-link d-flex align-items-center gap-3" style="color: #93c5fd !important;">
          <i class="bi bi-people"></i>
          <span>إدارة جميع المستخدمين ({{ \App\Models\User::count() }})</span>
        </a>
      </li>
      @endif
    </ul>
  </div>

  <div class="px-3">
    <form action="{{ url('/logout') }}" method="POST">
      @csrf
      <button type="submit" class="btn btn-outline-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-2 py-2">
        <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
      </button>
    </form>
  </div>
</aside>
