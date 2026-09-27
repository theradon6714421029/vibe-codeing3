@extends('layouts.app')
@section('title', $restaurant['name'] ?? 'ร้านอาหาร')
@section('content')
<div class="card shadow-sm">
    @if(!empty($restaurant['image_url']))
        <img src="{{ $restaurant['image_url'] }}" class="card-img-top" style="max-height:320px;object-fit:cover" alt="">
    @endif
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <h2>{{ $restaurant['name'] ?? '-' }}</h2>
            @if(($restaurant['is_open'] ?? false))
                <span class="badge bg-success fs-6">เปิดอยู่</span>
            @else
                <span class="badge bg-secondary fs-6">ปิดอยู่</span>
            @endif
        </div>
        <p class="text-muted">{{ $restaurant['description'] ?? '' }}</p>
        <ul class="list-unstyled">
            <li><i class="bi bi-clock"></i> เวลาเปิด-ปิด: {{ $restaurant['open_time'] ?? '-' }} - {{ $restaurant['close_time'] ?? '-' }}</li>
            <li><i class="bi bi-geo-alt"></i> ที่อยู่: {{ $restaurant['address'] ?? '-' }}</li>
            <li><i class="bi bi-telephone"></i> โทร: {{ $restaurant['phone'] ?? '-' }}</li>
        </ul>
        <a href="/menu" class="btn btn-primary">ดูเมนูอาหาร</a>
    </div>
</div>
@endsection
