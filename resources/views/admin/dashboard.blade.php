@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<h3 class="mb-4 fw-semibold">Admin Dashboard</h3>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card hoverable p-3">
            <div class="text-warning fs-1 fw-bold">{{ $stats['pending_count'] }}</div>
            <small class="text-muted">รอดำเนินการ</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hoverable p-3">
            <div class="text-info fs-1 fw-bold">{{ $stats['preparing_count'] }}</div>
            <small class="text-muted">กำลังเตรียม</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hoverable p-3">
            <div class="text-primary fs-1 fw-bold">{{ $stats['menu_count'] }}</div>
            <small class="text-muted">เมนูทั้งหมด</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card hoverable p-3">
            <div class="text-secondary fs-1 fw-bold">{{ $stats['category_count'] }}</div>
            <small class="text-muted">หมวดหมู่</small>
        </div>
    </div>
</div>
<div class="card">
    <div class="list-group list-group-flush">
        <a href="/admin/orders" class="list-group-item list-group-item-action py-3"><i class="bi bi-box-seam me-2"></i> จัดการออเดอร์</a>
        <a href="/admin/menus" class="list-group-item list-group-item-action py-3"><i class="bi bi-egg-fried me-2"></i> จัดการเมนู</a>
        <a href="/admin/categories" class="list-group-item list-group-item-action py-3"><i class="bi bi-tags me-2"></i> จัดการหมวดหมู่</a>
        <a href="/admin/restaurant" class="list-group-item list-group-item-action py-3"><i class="bi bi-shop me-2"></i> ข้อมูลร้าน</a>
    </div>
</div>
@endsection