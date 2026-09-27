@extends('layouts.app')
@section('title', $menu['name'])
@section('content')
<div class="card hoverable">
    <img src="{{ $menu['image_url'] ?? 'https://placehold.co/600x400?text=No+Image' }}" class="card-img-hero" alt="{{ $menu['name'] }}">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h2 class="mb-0">{{ $menu['name'] }}</h2>
            @if($menu['is_available'] ?? false)
                <span class="badge bg-success">พร้อมเสิร์ฟ</span>
            @else
                <span class="badge bg-secondary">สินค้าหมด</span>
            @endif
        </div>
        <p class="text-muted">{{ $menu['description'] }}</p>
        <h3 class="text-primary mb-4">฿{{ number_format($menu['price'], 2) }}</h3>
        @if($menu['is_available'] ?? false)
            <button class="btn btn-primary btn-lg rounded-pill px-4 add-to-cart-btn" data-menu-id="{{ $menu['id'] }}">
                <i class="bi bi-cart-plus"></i> เพิ่มลงตะกร้า
            </button>
        @endif
    </div>
</div>
@endsection