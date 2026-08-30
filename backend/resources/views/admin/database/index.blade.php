@extends('admin.layouts.app')

@section('page_title', 'مستكشف قاعدة البيانات والهيكل (Database Explorer)')

@section('content')
<div class="container-fluid p-0">

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 bg-dark bg-opacity-75 border border-secondary border-opacity-25 d-flex align-items-center justify-content-between shadow-sm">
                <div>
                    <div class="text-white-50 fs-8 fw-bold mb-1">إجمالي الجداول النشطة</div>
                    <div class="fs-4 fw-bold text-white">{{ $totalTables }} <small class="fs-8 text-gold">جداول</small></div>
                </div>
                <div class="rounded-circle p-2 bg-gold bg-opacity-10 text-gold fs-4">
                    <i class="bi bi-table"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 bg-dark bg-opacity-75 border border-secondary border-opacity-25 d-flex align-items-center justify-content-between shadow-sm">
                <div>
                    <div class="text-white-50 fs-8 fw-bold mb-1">إجمالي السجلات الكلي</div>
                    <div class="fs-4 fw-bold text-white">{{ number_format($totalRecords) }} <small class="fs-8 text-success">سجل</small></div>
                </div>
                <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success fs-4">
                    <i class="bi bi-hdd-stack-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 bg-dark bg-opacity-75 border border-secondary border-opacity-25 d-flex align-items-center justify-content-between shadow-sm">
                <div>
                    <div class="text-white-50 fs-8 fw-bold mb-1">حجم قاعدة البيانات</div>
                    <div class="fs-4 fw-bold text-white">{{ $dbSize }}</div>
                </div>
                <div class="rounded-circle p-2 bg-info bg-opacity-10 text-info fs-4">
                    <i class="bi bi-database-check"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-4 bg-dark bg-opacity-75 border border-secondary border-opacity-25 d-flex align-items-center justify-content-between shadow-sm">
                <div>
                    <div class="text-white-50 fs-8 fw-bold mb-1">محرك وقواعد البيانات</div>
                    <div class="fs-5 fw-bold text-white">PostgreSQL 16</div>
                </div>
                <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning fs-4">
                    <i class="bi bi-server"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs: Tables Browser vs SQL Query Runner -->
    <ul class="nav nav-pills mb-4 gap-2 bg-dark bg-opacity-50 p-2 rounded-4 border border-secondary border-opacity-25" id="dbTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 fw-bold fs-8" id="tab-browser-btn" data-bs-toggle="tab" data-bs-target="#tab-browser" type="button" role="tab">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i> استعراض جداول البيانات
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold fs-8" id="tab-query-btn" data-bs-toggle="tab" data-bs-target="#tab-query" type="button" role="tab">
                <i class="bi bi-terminal-fill me-2"></i> منصة تنفيذ استعلامات SQL (Read-Only)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="dbTabsContent">

        <!-- TAB 1: Table Browser -->
        <div class="tab-pane fade show active" id="tab-browser" role="tabpanel">
            
            <!-- Table Selection Horizontal Pills -->
            <div class="p-3 rounded-4 bg-dark bg-opacity-75 border border-secondary border-opacity-25 mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fs-8 text-white-50 fw-bold"><i class="bi bi-collection me-1 text-gold"></i> اختر الجدول للاستعراض والتصفح:</span>
                    <button class="btn btn-sm btn-link text-gold p-0 text-decoration-none fs-8" type="button" data-bs-toggle="collapse" data-bs-target="#schemaDetailsCollapse">
                        <i class="bi bi-info-circle me-1"></i> عرض تفاصيل هيكل الأعمدة (Schema)
                    </button>
                </div>
                <div class="d-flex flex-wrap gap-2" style="max-height: 120px; overflow-y: auto;">
                    @foreach($tablesList as $tName => $tData)
                        <a href="{{ route('admin.database.index', ['table' => $tName]) }}" 
                           class="btn btn-sm rounded-pill px-3 py-1 fs-8 fw-bold d-flex align-items-center gap-2 {{ $selectedTable === $tName ? 'btn-warning text-dark' : 'btn-outline-secondary text-white' }}" style="transition: all 0.2s;">
                            <span>{{ $tName }}</span>
                            <span class="badge {{ $selectedTable === $tName ? 'bg-dark text-white' : 'bg-secondary bg-opacity-50 text-white-50' }} rounded-pill fs-9">{{ $tData['count'] }}</span>
                        </a>
                    @endforeach
                </div>

                <!-- Schema Details Collapse -->
                <div class="collapse mt-3 pt-3 border-top border-secondary border-opacity-25" id="schemaDetailsCollapse">
                    <h6 class="text-gold fs-8 fw-bold mb-2"><i class="bi bi-diagram-3 me-1"></i> هيكل أعمدة الجدول الحالي (<code>{{ $selectedTable }}</code>):</h6>
                    <div class="table-responsive" style="max-height: 200px;">
                        <table class="table table-sm text-white fs-9 align-middle mb-0">
                            <thead>
                                <tr class="text-white-50">
                                    <th>اسم العمود (Column)</th>
                                    <th>نوع البيانات (Data Type)</th>
                                    <th>يقبل Null؟</th>
                                    <th>القيمة الافتراضية (Default)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($columnsMeta as $col)
                                    <tr>
                                        <td class="fw-bold text-gold">{{ $col->column_name }}</td>
                                        <td><code>{{ $col->data_type }}</code></td>
                                        <td>
                                            @if($col->is_nullable === 'YES')
                                                <span class="badge bg-success bg-opacity-25 text-success">نعم (Nullable)</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-25 text-danger">لا (Required)</span>
                                            @endif
                                        </td>
                                        <td class="text-white-50">{{ $col->column_default ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Table Actions & Filter Bar -->
            <div class="p-3 rounded-4 bg-dark bg-opacity-75 border border-secondary border-opacity-25 mb-4 shadow-sm">
                <form action="{{ route('admin.database.index') }}" method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="table" value="{{ $selectedTable }}">
                    
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-black text-gold border-secondary"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control bg-dark text-white border-secondary" placeholder="بحث شامل داخل جميع أعمدة الجدول..." value="{{ $search }}">
                            @if(!empty($search))
                                <a href="{{ route('admin.database.index', ['table' => $selectedTable]) }}" class="btn btn-outline-secondary border-secondary text-white-50" title="إلغاء البحث"><i class="bi bi-x-lg"></i></a>
                            @endif
                        </div>
                    </div>

                    <div class="col-auto">
                        <select name="per_page" class="form-select bg-dark text-white border-secondary fs-8" onchange="this.form.submit()">
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 سطر</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 سطر</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 سطر</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 سطر</option>
                        </select>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-warning rounded-pill px-3 fs-8 fw-bold">
                            <i class="bi bi-filter me-1"></i> تصفية
                        </button>
                    </div>

                    <div class="col-auto ms-auto d-flex gap-2">
                        <a href="{{ route('admin.database.export', ['table' => $selectedTable]) }}" class="btn btn-outline-success rounded-pill px-3 fs-8 fw-bold">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> تصدير CSV
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table Rows Data Grid -->
            <div class="card bg-dark bg-opacity-75 border-secondary border-opacity-25 rounded-4 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-black bg-opacity-40 border-secondary border-opacity-25 py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-table text-gold fs-5"></i>
                        <h6 class="fw-bold text-white mb-0">جدول: <span class="text-gold">{{ $selectedTable }}</span></h6>
                        <span class="badge bg-secondary bg-opacity-50 text-white fs-9 ms-2">عرض {{ $rows->count() }} من أصل {{ $rows->total() }} سجل</span>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 600px;">
                    <table class="table custom-dark-table align-middle mb-0 text-nowrap">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 70px;">الإجراءات</th>
                                @foreach($columnNames as $col)
                                    @php
                                        $isSorted = ($sortCol === $col);
                                        $nextDir = ($isSorted && $sortDir === 'asc') ? 'desc' : 'asc';
                                    @endphp
                                    <th>
                                        <a href="{{ route('admin.database.index', array_merge(request()->query(), ['sort' => $col, 'dir' => $nextDir])) }}" class="text-decoration-none text-gold d-inline-flex align-items-center gap-1">
                                            <span>{{ $col }}</span>
                                            @if($isSorted)
                                                <i class="bi bi-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }}-alt text-warning"></i>
                                            @else
                                                <i class="bi bi-arrow-down-up text-white-50 fs-9 opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr>
                                    <td class="text-center">
                                        @if(isset($row->id))
                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-circle p-1" style="width: 28px; height: 28px;" onclick="inspectRecord('{{ $selectedTable }}', '{{ $row->id }}')" title="معاينة السجل بالكامل">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @else
                                            <span class="text-white-50">-</span>
                                        @endif
                                    </td>
                                    @foreach($columnNames as $col)
                                        @php
                                            $val = $row->{$col} ?? null;
                                        @endphp
                                        <td>
                                            @if(is_null($val))
                                                <span class="text-white-50 opacity-50 fs-9"><em>null</em></span>
                                            @elseif(is_bool($val) || $val === true || $val === false)
                                                <span class="badge {{ $val ? 'bg-success' : 'bg-danger' }} bg-opacity-25 {{ $val ? 'text-success' : 'text-danger' }}">
                                                    {{ $val ? 'true' : 'false' }}
                                                </span>
                                            @elseif(str_contains($col, 'password'))
                                                <span class="text-white-50 fs-9">•••••••• [Bcrypt]</span>
                                            @elseif(is_string($val) && (str_starts_with($val, '{') || str_starts_with($val, '[')))
                                                <span class="badge bg-info bg-opacity-25 text-info fs-9 font-monospace" title="{{ Str::limit($val, 150) }}">
                                                    <i class="bi bi-braces me-1"></i> JSON ({{ strlen($val) }} B)
                                                </span>
                                            @elseif(is_string($val) && strlen($val) > 40)
                                                <span title="{{ $val }}">{{ Str::limit($val, 40) }}</span>
                                            @else
                                                <span>{{ $val }}</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($columnNames) + 1 }}" class="text-center py-5 text-white-50">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                        لا توجد سجلات مطابقة في جدول <strong>{{ $selectedTable }}</strong>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($rows->hasPages())
                    <div class="card-footer bg-black bg-opacity-40 border-secondary border-opacity-25 py-3 d-flex justify-content-between align-items-center">
                        <span class="text-white-50 fs-8">صفحة {{ $rows->currentPage() }} من {{ $rows->lastPage() }}</span>
                        {{ $rows->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

        </div>

        <!-- TAB 2: Safe SQL Query Runner -->
        <div class="tab-pane fade" id="tab-query" role="tabpanel">
            <div class="p-4 rounded-4 bg-dark bg-opacity-75 border border-secondary border-opacity-25 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-gold mb-1"><i class="bi bi-terminal-fill me-2"></i>منصة تنفيذ استعلامات SQL (Read-Only)</h5>
                        <p class="text-white-50 fs-8 mb-0">يمكنك تنفيذ استعلامات <code>SELECT</code> و <code>EXPLAIN</code> بأمان فوري مع حماية البيانات من التعديل غير المقصود.</p>
                    </div>
                    <span class="badge bg-success bg-opacity-25 text-success border border-success px-3 py-2 fs-8 fw-bold">
                        <i class="bi bi-shield-check me-1"></i> وضع القراءة الآمن مفعّل
                    </span>
                </div>

                <!-- Query Presets -->
                <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
                    <span class="text-white-50 fs-9 fw-bold"><i class="bi bi-lightning-fill text-gold me-1"></i>استعلامات جاهزة سريعة:</span>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2 fs-9 text-white" onclick="setPresetQuery('SELECT id, name, email, role, created_at FROM users ORDER BY id DESC LIMIT 20;')">أحدث المستخدمين</button>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2 fs-9 text-white" onclick="setPresetQuery('SELECT id, company_name, slug, status, created_at FROM workspaces ORDER BY id DESC LIMIT 20;')">أحدث المتاجر</button>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2 fs-9 text-white" onclick="SELECT b.id, w.company_name, b.name, b.ai_provider, b.model_type, b.is_active FROM bots b JOIN workspaces w ON b.workspace_id = w.id LIMIT 20;')">بيانات البوتات والمتاجر</button>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2 fs-9 text-white" onclick="setPresetQuery('SELECT status, count(*) as total FROM conversations GROUP BY status;')">إحصائيات المحادثات بالحالة</button>
                </div>

                <!-- SQL Input Area -->
                <div class="mb-3">
                    <textarea id="sqlInput" class="form-control bg-black text-warning border-secondary font-monospace fs-8 p-3" rows="5" placeholder="اكتب استعلام SQL هنا (مثال: SELECT * FROM users LIMIT 10;)">SELECT id, name, email, role, created_at FROM users ORDER BY id DESC LIMIT 10;</textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold" id="runQueryBtn" onclick="executeSqlQuery()">
                        <i class="bi bi-play-fill me-1"></i> تشغيل الاستعلام (Execute)
                    </button>
                    <div id="queryLatencyBadge" class="text-white-50 fs-8"></div>
                </div>

                <!-- Query Results Grid -->
                <div class="mt-4" id="queryResultsContainer" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-white mb-0" id="queryResultsTitle">نتائج الاستعلام:</h6>
                    </div>
                    <div class="table-responsive rounded-3 border border-secondary border-opacity-40" style="max-height: 400px;">
                        <table class="table custom-dark-table align-middle mb-0 text-nowrap" id="queryResultsTable">
                            <thead id="queryResultsHead"></thead>
                            <tbody id="queryResultsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Record Detail Inspection Modal -->
<div class="modal fade" id="recordInspectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-gold fw-bold" id="inspectModalTitle"><i class="bi bi-file-earmark-text me-2"></i>تفاصيل السجل</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="inspectModalBody">
                <div class="text-center py-4">
                    <span class="spinner-border spinner-border-sm text-gold me-2"></span> جاري تحميل بيانات السجل...
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" onclick="copyRecordJson()"><i class="bi bi-clipboard me-1"></i> نسخ بصيغة JSON</button>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
let currentInspectRecordJson = '';

async function inspectRecord(table, id) {
    const modal = new bootstrap.Modal(document.getElementById('recordInspectModal'));
    const modalTitle = document.getElementById('inspectModalTitle');
    const modalBody = document.getElementById('inspectModalBody');
    
    modalTitle.innerHTML = `<i class="bi bi-file-earmark-text text-gold me-2"></i> تفاصيل سجل #${id} من جدول (<code>${table}</code>)`;
    modalBody.innerHTML = '<div class="text-center py-4 text-warning"><span class="spinner-border spinner-border-sm me-2"></span> جاري التحميل...</div>';
    modal.show();

    try {
        const res = await fetch(`{{ url('/admin/database/record') }}/${table}/${id}`, {
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        });

        const data = await res.json();
        if (data.success && data.record) {
            currentInspectRecordJson = JSON.stringify(data.record, null, 2);
            let rowsHtml = '';
            for (const [key, val] of Object.entries(data.record)) {
                let displayVal = val;
                if (val === null) {
                    displayVal = '<span class="text-white-50 opacity-50">null</span>';
                } else if (typeof val === 'boolean') {
                    displayVal = `<span class="badge ${val ? 'bg-success' : 'bg-danger'}">${val}</span>`;
                } else if (typeof val === 'object' || (typeof val === 'string' && (val.startsWith('{') || val.startsWith('[')))) {
                    let formattedJson = val;
                    try {
                        const parsed = (typeof val === 'string') ? JSON.parse(val) : val;
                        formattedJson = JSON.stringify(parsed, null, 2);
                    } catch(e){}
                    displayVal = `<pre class="bg-black text-warning p-2 rounded fs-9 mb-0" style="max-height: 180px; overflow-y: auto;">${formattedJson}</pre>`;
                }
                rowsHtml += `
                    <tr>
                        <td class="fw-bold text-gold fs-8" style="width: 220px;">${key}</td>
                        <td class="fs-8 text-white">${displayVal}</td>
                    </tr>
                `;
            }

            modalBody.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-bordered border-secondary table-sm align-middle mb-0">
                        <tbody>${rowsHtml}</tbody>
                    </table>
                </div>
            `;
        } else {
            modalBody.innerHTML = `<div class="alert alert-danger mb-0">${data.message || 'تعذر جلب بيانات السجل'}</div>`;
        }
    } catch(err) {
        modalBody.innerHTML = `<div class="alert alert-danger mb-0">خطأ: ${err.message}</div>`;
    }
}

function copyRecordJson() {
    if (!currentInspectRecordJson) return;
    navigator.clipboard.writeText(currentInspectRecordJson).then(() => {
        alert('تم نسخ بيانات السجل كـ JSON إلى الحافظة بنجاح ✓');
    });
}

function setPresetQuery(sql) {
    document.getElementById('sqlInput').value = sql;
}

async function executeSqlQuery() {
    const sql = document.getElementById('sqlInput').value.trim();
    const btn = document.getElementById('runQueryBtn');
    const badge = document.getElementById('queryLatencyBadge');
    const container = document.getElementById('queryResultsContainer');
    const thead = document.getElementById('queryResultsHead');
    const tbody = document.getElementById('queryResultsBody');
    const title = document.getElementById('queryResultsTitle');

    if (!sql) {
        alert('يرجى إدخال استعلام SQL');
        return;
    }

    const origText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري التنفيذ...';
    btn.disabled = true;

    try {
        const res = await fetch("{{ route('admin.database.query') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ sql })
        });

        const data = await res.json();
        if (data.success) {
            badge.innerHTML = `<span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i> تم التنفيذ في ${data.latency_ms} ms (${data.count} نتائج)</span>`;
            title.innerText = `نتائج الاستعلام (${data.count} صفوف):`;
            
            // Build headers
            let headHtml = '<tr>';
            data.columns.forEach(col => {
                headHtml += `<th class="text-gold fs-8">${col}</th>`;
            });
            headHtml += '</tr>';
            thead.innerHTML = headHtml;

            // Build rows
            let bodyHtml = '';
            data.rows.forEach(row => {
                bodyHtml += '<tr>';
                data.columns.forEach(col => {
                    let cellVal = row[col];
                    if (cellVal === null) {
                        cellVal = '<span class="text-white-50 opacity-50">null</span>';
                    } else if (typeof cellVal === 'object') {
                        cellVal = `<span class="badge bg-info bg-opacity-25 text-info fs-9">${JSON.stringify(cellVal)}</span>`;
                    }
                    bodyHtml += `<td class="fs-8 text-white">${cellVal}</td>`;
                });
                bodyHtml += '</tr>';
            });
            tbody.innerHTML = bodyHtml;
            container.style.display = 'block';
        } else {
            badge.innerHTML = `<span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i> فشل الاستعلام</span>`;
            alert(data.message || 'حدث خطأ أثناء تنفيذ الاستعلام');
        }
    } catch(err) {
        alert('خطأ في الاتصال: ' + err.message);
    } finally {
        btn.innerHTML = origText;
        btn.disabled = false;
    }
}
</script>
@endsection
@endsection
