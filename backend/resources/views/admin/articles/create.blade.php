@extends('admin.layouts.app')

@section('title', 'إضافة مقال جديد')
@section('page_title', 'كتابة مقال جديد للمدونة')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary rounded-3 btn-sm">
        <i class="bi-arrow-right me-1"></i> العودة لقائمة المقالات
    </a>
</div>

<div class="card-custom p-4">
    <form action="{{ route('admin.articles.store') }}" method="POST">
        @csrf

        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label text-white fw-bold">عنوان المقال <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control bg-dark border-secondary text-white" placeholder="مثال: كيف يرفع الذكاء الاصطناعي المبيعات؟" value="{{ old('title') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label text-white fw-bold">التصنيف <span class="text-danger">*</span></label>
                <input type="text" name="category" class="form-control bg-dark border-secondary text-white" placeholder="مثال: أتمتة وذكاء اصطناعي" value="{{ old('category', 'أتمتة وذكاء اصطناعي') }}" required>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label text-white fw-bold">وقت القراءة المقدر</label>
                <input type="text" name="read_time" class="form-control bg-dark border-secondary text-white" placeholder="مثال: قراءة 5 دقائق" value="{{ old('read_time', 'قراءة 5 دقائق') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label text-white fw-bold">آيقونة المقال (Bootstrap Icon)</label>
                <input type="text" name="icon" class="form-control bg-dark border-secondary text-white" placeholder="مثال: bi-robot أو bi-whatsapp" value="{{ old('icon', 'bi-robot') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label text-white fw-bold">ملخص المقال (المعاينة المعروضة في الكروت) <span class="text-danger">*</span></label>
            <textarea name="summary" rows="3" class="form-control bg-dark border-secondary text-white" placeholder="اكتب ملخصاً قصيراً وجذاباً للمقال..." required>{{ old('summary') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label text-white fw-bold">محتوى المقال الكامل (يدعم HTML) <span class="text-danger">*</span></label>
            <textarea name="content" rows="12" class="form-control bg-dark border-secondary text-white font-monospace" placeholder="<p>اكتب نص المقال هنا وتنسيقات الـ HTML...</p>" required>{{ old('content') }}</textarea>
        </div>

        <div class="d-flex align-items-center gap-4 mb-4 p-3 bg-dark bg-opacity-50 rounded-3 border border-secondary border-opacity-25">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                <label class="form-check-label text-white fw-bold" for="is_featured">تعيين كمقال مميز رئيسي (Featured Hero Post)</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', 1) ? 'checked' : '' }}>
                <label class="form-check-label text-white fw-bold" for="is_published">نشر المقال فوراً للمستخدمين والزوار</label>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
            <button type="submit" class="btn btn-primary px-5 fw-bold">حفظ المقال</button>
        </div>
    </form>
</div>
@endsection
