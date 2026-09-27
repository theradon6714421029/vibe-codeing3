@extends('layouts.app')
@section('title', 'เมนูอาหาร')
@section('content')
<form method="GET" action="/menu" class="row g-2 mb-4 bg-white p-3 rounded-4 shadow-sm mx-0">
    <div class="col-md-6">
        <input type="text" name="q" class="form-control" placeholder="ค้นหาเมนู..." value="{{ request('q') }}" maxlength="100">
    </div>
    <div class="col-md-4">
        <select name="category_id" class="form-select">
            <option value="">ทุกหมวดหมู่</option>
            @foreach($categories as $cat)
                <option value="{{ $cat['id'] }}" @selected(request('category_id') == $cat['id'])>{{ $cat['name'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">ค้นหา</button></div>
</form>

<div class="row g-3">
    @forelse($menus as $menu)
        <div class="col-md-4">
            <div class="card food-card h-100">
                <a href="/menu/{{ $menu['id'] }}" class="text-decoration-none text-reset">
                    <img src="{{ $menu['image_url'] ?? 'https://placehold.co/400x300?text=No+Image' }}" class="card-img-top" alt="{{ $menu['name'] }}">
                </a>
                <div class="card-body d-flex flex-column">
                    <a href="/menu/{{ $menu['id'] }}" class="text-decoration-none text-reset">
                        <h5>{{ $menu['name'] }}</h5>
                    </a>
                    <p class="text-muted small flex-grow-1">{{ \Illuminate\Support\Str::limit($menu['description'] ?? '', 80) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="text-primary fs-5">฿{{ number_format($menu['price'], 2) }}</strong>
                        @if($menu['is_available'] ?? false)
                            <button class="btn btn-sm btn-primary rounded-pill px-3 add-to-cart-btn" data-menu-id="{{ $menu['id'] }}">
                                <i class="bi bi-cart-plus"></i> เพิ่ม
                            </button>
                        @else
                            <span class="badge bg-secondary">หมด</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">ไม่พบเมนูอาหาร</p>
    @endforelse
</div>
@endsection
