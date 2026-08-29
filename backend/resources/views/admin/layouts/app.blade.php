<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة الإدارة العليا') - منصة ردود (Rudood Admin)</title>
    
    <!-- Google Fonts (Cairo & Tajawal) -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Reem+Kufi:wght@700;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Custom Platform CSS -->
    <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
    
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @include('layouts.partials.theme')

    <style>
        :root {
            --sidebar-width: 255px;
        }

        body {
            font-family: var(--font);
            background-color: var(--bg-dark);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .admin-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            right: 0;
            background: linear-gradient(180deg, rgba(15,23,42,0.96) 0%, var(--bg-dark) 100%);
            border-left: 1px solid var(--card-border);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--card-border);
        }

        .sidebar-brand .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--gold-dark), var(--gold));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            box-shadow: 0 3px 10px var(--gold-soft);
        }

        .sidebar-brand-text {
            font-weight: 700;
            font-size: 1.05rem;
            color: #fff;
            letter-spacing: -0.3px;
        }

        .sidebar-menu {
            padding: 0.85rem 0.75rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--gold);
            margin-top: 1rem;
            margin-bottom: 0.35rem;
            padding-right: 0.65rem;
            letter-spacing: 0.5px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.55rem 0.85rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.88rem;
            margin-bottom: 3px;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-link.active {
            color: #fff;
            background: linear-gradient(90deg, var(--gold-dark) 0%, var(--gold-soft) 100%);
            box-shadow: 0 3px 10px var(--gold-soft);
        }

        /* Main Content Wrapper */
        .admin-main-wrapper {
            margin-right: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            height: 60px;
            background: rgba(11, 15, 25, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 0 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 990;
        }

        .header-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            color: #fff;
        }

        .header-user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.05);
            padding: 4px 12px;
            border-radius: 25px;
            border: 1px solid var(--card-border);
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* Dashboard Cards & Layout */
        .admin-content {
            padding: 2rem;
            flex-grow: 1;
        }

        /* High Contrast and Selection Overrides */
        ::selection {
            background: #d4af37 !important;
            color: #0b0f19 !important;
        }
        ::-moz-selection {
            background: #d4af37 !important;
            color: #0b0f19 !important;
        }

        .text-muted, .text-secondary {
            color: rgba(255, 255, 255, 0.78) !important;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.82) !important;
        }

        .form-label, label {
            color: #ffffff !important;
            font-weight: 600;
        }

        .form-control, .form-select {
            background-color: rgba(15, 23, 42, 0.85) !important;
            border: 1px solid rgba(212, 175, 55, 0.3) !important;
            color: #ffffff !important;
        }

        .form-control:focus, .form-select:focus {
            background-color: rgba(15, 23, 42, 0.95) !important;
            border-color: #d4af37 !important;
            color: #ffffff !important;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.25) !important;
        }

        /* Modal Z-Index and Clickability Fix */
        .modal {
            z-index: 1060 !important;
        }
        .modal-backdrop {
            z-index: 1050 !important;
        }
        .modal-dialog {
            z-index: 1070 !important;
        }
        .modal-content {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6) !important;
        }

        /* ──────────────────────────────────────────────────────────────────
           Super Admin Universal Dark Table Styling
           ────────────────────────────────────────────────────────────────── */
        .table, .table-custom, .table-dark-custom, table {
            --bs-table-bg: transparent !important;
            --bs-table-color: #f8fafc !important;
            --bs-table-border-color: rgba(255, 255, 255, 0.08) !important;
            --bs-table-hover-bg: rgba(212, 175, 55, 0.08) !important;
            --bs-table-hover-color: #ffffff !important;
            color: #f8fafc !important;
            background-color: transparent !important;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th, .table-custom thead th, .table-dark-custom thead th {
            background: linear-gradient(180deg, rgba(212, 175, 55, 0.15) 0%, rgba(15, 23, 42, 0.95) 100%) !important;
            color: #d4af37 !important;
            font-weight: 700 !important;
            font-size: 0.82rem !important;
            padding: 14px 18px !important;
            border-bottom: 1px solid rgba(212, 175, 55, 0.3) !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .table tbody tr td, .table-custom tbody tr td, .table-dark-custom tbody tr td {
            background-color: rgba(15, 23, 42, 0.65) !important;
            color: #f8fafc !important;
            padding: 14px 18px !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
            vertical-align: middle !important;
            font-size: 0.85rem;
        }

        .table tbody tr:hover td, .table-custom tbody tr:hover td, .table-dark-custom tbody tr:hover td {
            background-color: rgba(212, 175, 55, 0.1) !important;
            color: #ffffff !important;
        }

        .table tbody tr:last-child td {
            border-bottom: none !important;
        }

        .stat-val {
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1.2;
            color: #fff;
        }

        .stat-lbl {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.78) !important;
            font-weight: 600;
        }

        .badge-admin-role {
            background: rgba(212, 175, 55, 0.15);
            color: var(--gold);
            border: 1px solid rgba(212, 175, 55, 0.3);
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 6px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <div class="logo-icon">
                <i class="bi bi-shield-fill"></i>
            </div>
            <div>
                <div class="sidebar-brand-text">ردود إدمن</div>
                <div style="font-size: 0.7rem; color: var(--text-muted);">Super Admin Center</div>
            </div>
        </div>

        <nav class="sidebar-menu">
            <div class="nav-section-label">لوحة التحكم الرئيسية</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-pie-chart"></i>
                <span>نظرة عامة والتحليلات</span>
            </a>
            <a href="{{ route('admin.statistics') }}" class="sidebar-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                <i class="bi bi-bar-chart"></i>
                <span>لوحة الإحصائيات الكاملة</span>
            </a>

            <div class="nav-section-label">إدارة المنصة والشركات</div>
            <a href="{{ route('admin.subscribers.index') }}" class="sidebar-link {{ request()->routeIs('admin.subscribers.*') ? 'active' : '' }}">
                <i class="bi bi-person-check-fill text-gold"></i>
                <span>طلبات المشتركين</span>
                @php $pendingCount = \App\Models\SubscriberRequest::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-danger ms-auto rounded-pill px-2 fs-9">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.workspaces.index') }}" class="sidebar-link {{ request()->routeIs('admin.workspaces.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>الشركات والمتاجر</span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>المستخدمين والملاك</span>
            </a>

            <a href="{{ route('admin.articles.index') }}" class="sidebar-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i>
                <span>إدارة المدونة والمقالات</span>
            </a>

            <a href="{{ route('admin.contacts.index') }}" class="sidebar-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                <i class="bi bi-envelope-paper-heart"></i>
                <span class="d-flex align-items-center justify-content-between flex-grow-1">
                    <span>رسائل تواصل معنا</span>
                    @php
                        $newContactsCount = \App\Models\ContactMessage::where('status', 'new')->count();
                    @endphp
                    @if($newContactsCount > 0)
                        <span class="badge bg-danger rounded-pill px-2 py-0 fs-9">{{ $newContactsCount }}</span>
                    @endif
                </span>
            </a>

            <div class="nav-section-label">البنية التحتية والنظام</div>
            <a href="{{ route('admin.system.index') }}" class="sidebar-link {{ request()->routeIs('admin.system.*') ? 'active' : '' }}">
                <i class="bi bi-hdd-network"></i>
                <span>حالة النظام والخدمات</span>
            </a>

            <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i>
                <span>سجل تدقيق الأنشطة</span>
            </a>

            <div class="nav-section-label">تنقل سريع</div>
            <a href="/dashboard" class="sidebar-link" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>لوحة المتجر العادية</span>
            </a>
        </nav>

        <div style="padding: 1rem; border-top: 1px solid var(--card-border);">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm rounded-3 py-2 fw-bold">
                    <i class="bi-box-arrow-right ms-1"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="admin-main-wrapper">
        <!-- Top Header -->
        <header class="admin-header">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-dark border border-warning border-opacity-40 text-gold btn-sm rounded-3 py-1 px-2.5 d-lg-none" id="adminMobileSidebarToggle" aria-label="القائمة الجانبية">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h1 class="header-title mb-0">@yield('page_title', 'لوحة التحكم والإدارة العليا')</h1>
                <button type="button" class="btn btn-sm btn-dark border border-warning border-opacity-40 text-white px-3 py-2 rounded-pill fs-8 fw-bold d-flex align-items-center gap-2 shadow-sm search-trigger-btn ms-md-2" data-bs-toggle="modal" data-bs-target="#commandPaletteModal" style="background: rgba(15, 23, 42, 0.85); min-width: 190px;">
                    <i class="bi bi-search text-gold fs-7"></i>
                    <span class="text-white-50">بحث سريع...</span>
                    <kbd class="bg-black text-gold border border-warning border-opacity-30 px-2 py-0.5 rounded fs-9 ms-auto font-monospace">⌘K</kbd>
                </button>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                @php
                    $maint = \App\Models\SystemSetting::getMaintenanceDetails();
                @endphp
                @if($maint['is_active'])
                    <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1 fs-8 fw-bold d-flex align-items-center gap-2 pulse-maintenance shadow-sm" data-bs-toggle="modal" data-bs-target="#maintenanceControlModal">
                        <span class="spinner-grow spinner-grow-sm" role="status"></span>
                        <i class="bi bi-tools"></i>
                        <span>وضع الصيانة: نشط الآن</span>
                    </button>
                @else
                    <button type="button" class="btn btn-sm btn-dark border border-secondary border-opacity-50 text-white-50 rounded-pill px-3 py-1 fs-8 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#maintenanceControlModal">
                        <i class="bi bi-tools text-gold"></i>
                        <span>وضع الصيانة: معطل</span>
                    </button>
                @endif

                <span class="badge-admin-role">
                    <i class="bi-gem me-1"></i> {{ auth()->user()->role ?? 'Super Admin' }}
                </span>

                <div class="header-user-badge">
                    <div class="user-avatar">
                        {{ mb_substr(auth()->user()?->name ?? 'A', 0, 1) }}
                    </div>
                    <div>
                        <div style="font-size: 0.9rem; font-weight: 700;">{{ auth()->user()?->name ?? 'مدير النظام الأعلى' }}</div>
                        <div style="font-size: 0.75rem; color: rgba(255,255,255,0.75);">{{ auth()->user()?->email ?? 'admin@rudood.com' }}</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dynamic Content -->
        <main class="admin-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4)!important;">
                    <i class="bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4)!important;">
                    <i class="bi-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Super Admin Maintenance Control Modal -->
    <div class="modal fade" id="maintenanceControlModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-custom border border-warning border-opacity-30">
                <div class="modal-header border-bottom border-secondary border-opacity-25 p-3">
                    <h5 class="modal-title text-white fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-tools text-gold"></i>
                        <span>التحكم بوضع الصيانة والجدولة</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.system.maintenance') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="form-check form-switch p-3 rounded-3 mb-3 border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.03);">
                            <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="switchMaintenance" name="is_active" value="1" {{ ($maint['is_active'] ?? false) ? 'checked' : '' }} style="cursor: pointer; transform: scale(1.2);">
                            <label class="form-check-label text-white fw-bold" for="switchMaintenance" style="cursor: pointer;">
                                تفعيل وضع الصيانة العام للمنصة
                            </label>
                            <div class="text-white-50 fs-9 mt-1">عند التفعيل، سيتم تحويل جميع لوحات المتاجر وصفحات التسجيل والدخول إلى صفحة الصيانة والعد التنازلي.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fs-8">عنوان رسالة الصيانة</label>
                            <input type="text" name="title" class="form-control" value="{{ $maint['title'] ?? 'أعمال صيانة وتطوير مجدولة 🛠️' }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fs-8">نص الرسالة التوضيحية للزوار والعملاء</label>
                            <textarea name="message" class="form-control" rows="3" required>{{ $maint['message'] ?? 'نقوم حالياً بإجراء تحديثات دورية وتطويرات هامة على أنظمة منصة ردود لتعزيز استقرار البنية التحتية وتقديم تجربة ردود ذكية فائقة السرعة.' }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fs-8 d-flex align-items-center justify-content-between">
                                <span>الموعد التقديري لانتهاء الصيانة (جدولة العد التنازلي)</span>
                                <span class="text-gold fs-9">اختياري</span>
                            </label>
                            <input type="datetime-local" name="scheduled_ends_at" class="form-control" value="{{ !empty($maint['scheduled_ends_at']) ? date('Y-m-d\TH:i', strtotime($maint['scheduled_ends_at'])) : '' }}">
                            <small class="text-white-50 fs-9 d-block mt-1">سيظهر هذا التوقيت في شاشة العد التنازلي المباشر (أيام، ساعات، دقائق، ثوانٍ).</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary border-opacity-25 p-3">
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-gold btn-sm rounded-pill px-4 fw-bold">حفظ وتطبيق فوراً</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ستارة الخلفية للشاشات الصغيرة -->
    <div class="sidebar-backdrop" id="adminSidebarBackdrop"></div>

    <!-- Global Command Palette Modal (Cmd + K / Quick Search) -->
    @include('layouts.partials.command-palette')

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('adminMobileSidebarToggle');
            const sidebar = document.querySelector('.admin-sidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');

            if (toggleBtn && sidebar && backdrop) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('show');
                    backdrop.classList.toggle('show');
                });

                backdrop.addEventListener('click', () => {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
