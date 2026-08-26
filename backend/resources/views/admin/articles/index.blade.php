@extends('admin.layouts.app')

@section('title', 'إدارة المدونة والمقالات')
@section('page_title', 'إدارة مقالات المدونة (Blog CMS)')

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h2 class="h4 fw-bold text-white mb-1">مقالات مدونة منصة ردود</h2>
        <p class="text-muted small mb-0">إنشاء وتعديل ونشر المقالات والأخبار الموجهة للعملاء وزوار المنصة.</p>
    </div>
    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary rounded-3 px-4 py-2 fw-bold">
        <i class="bi-plus me-1"></i> كتابة مقال جديد
    </a>
</div>

<!-- Filters & Search Card -->
<div class="card-custom p-3 mb-4">
    <form action="{{ route('admin.articles.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control bg-dark border-secondary text-white" placeholder="بحث بعنوان المقال أو التصنيف..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select bg-dark border-secondary text-white">
                <option value="">جميع الحالات (منشور ومسودة)</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور فقط</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة فقط</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100 rounded-3">تصفية</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary w-100 rounded-3">إعادة ضبط</a>
        </div>
    </form>
</div>

<!-- Articles Data Table -->
<div class="card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark-custom align-middle mb-0">
            <thead>
                <tr>
                    <th>المقال والتصنيف</th>
                    <th>وقت القراءة</th>
                    <th>مميز</th>
                    <th>الحالة</th>
                    <th>تاريخ النشر</th>
                    <th class="text-end">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $article)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-card-icon bg-primary bg-opacity-25 text-primary">
                                <i class="bi {{ $article->icon ?? 'bi-newspaper' }} fs-4"></i>
                            </div>
                            <div>
                                <a href="{{ route('blog.show', $article->slug) }}" target="_blank" class="fw-bold text-white text-decoration-none d-block mb-1">
                                    {{ $article->title }}
                                </a>
                                <span class="badge bg-secondary bg-opacity-50 text-white-50 border border-secondary border-opacity-25 fs-7">
                                    {{ $article->category }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="text-white-50 fs-7">
                        <i class="bi bi-clock me-1 text-gold"></i> {{ $article->read_time }}
                    </td>
                    <td>
                        @if($article->is_featured)
                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25">
                                <i class="bi-star me-1"></i> مقال رئيسي
                            </span>
                        @else
                            <span class="text-muted fs-7">-</span>
                        @endif
                    </td>
                    <td>
                        @if($article->is_published)
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">
                                <i class="bi-check-circle-fill me-1"></i> منشور
                            </span>
                        @else
                            <span class="badge bg-secondary bg-opacity-25 text-white-50 border border-secondary border-opacity-25">
                                <i class="bi-file-text me-1"></i> مسودة
                            </span>
                        @endif
                    </td>
                    <td class="text-white-50 fs-7">
                        {{ $article->published_at ? $article->published_at->format('Y-m-d') : 'لم ينشر بعد' }}
                    </td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <!-- Toggle Publish -->
                            <form action="{{ route('admin.articles.toggle-publish', $article->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $article->is_published ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $article->is_published ? 'إلغاء النشر' : 'نشر المقال' }}">
                                    <i class="bi {{ $article->is_published ? 'bi-eye-slash' : 'bi-globe' }}"></i>
                                </button>
                            </form>

                            <!-- Edit -->
                            <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-sm btn-outline-primary" title="تعديل المقال">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <!-- Delete -->
                            <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت تأكد من رغبتك في حذف هذا المقال؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف المقال">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi-newspaper fs-2 d-block mb-3"></i>
                        لا توجد مقالات مضافة في المدونة حالياً.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($articles->hasPages())
    <div class="p-3 border-top border-secondary border-opacity-25 d-flex justify-content-center">
        {{ $articles->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
