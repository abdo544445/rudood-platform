@props(['items' => []])

@if(!empty($items))
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-0 py-1 px-3 rounded-pill d-inline-flex align-items-center" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(212,175,55,0.2); font-size: 0.82rem;">
    <li class="breadcrumb-item">
      <a href="{{ auth()->user()?->isSuperAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="text-gold text-decoration-none">
        <i class="bi bi-house-door-fill me-1"></i> الرئيسية
      </a>
    </li>
    @foreach($items as $label => $link)
      @if($loop->last)
        <li class="breadcrumb-item active text-white fw-bold" aria-current="page">{{ $label }}</li>
      @else
        <li class="breadcrumb-item">
          <a href="{{ $link }}" class="text-white-50 text-decoration-none hover-gold">{{ $label }}</a>
        </li>
      @endif
    @endforeach
  </ol>
</nav>
@endif
