@extends('layouts.app')
@section('title', 'ตะกร้าของฉัน')
@section('content')
<h3 class="mb-4 fw-semibold">ตะกร้าของฉัน</h3>

<div class="card">
    <div class="card-body p-0">
        <table class="table align-middle mb-0" id="cartTable">
            <thead class="table-light">
                <tr><th class="ps-4">เมนู</th><th>ราคา</th><th style="width:110px">จำนวน</th><th>รวม</th><th class="pe-4"></th></tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr data-menu-id="{{ $item['menu_id'] }}">
                    <td class="ps-4">
                        {{ $item['name'] }}
                        @unless($item['available'])
                            <span class="badge bg-danger ms-1">หมดแล้ว</span>
                        @endunless
                    </td>
                    <td>฿{{ number_format($item['price'], 2) }}</td>
                    <td><input type="number" class="form-control form-control-sm qty-input" value="{{ $item['quantity'] }}" min="1" max="50"></td>
                    <td class="fw-semibold text-primary">฿{{ number_format($item['line_total'], 2) }}</td>
                    <td class="pe-4"><button class="btn btn-sm btn-outline-danger remove-btn"><i class="bi bi-trash"></i></button></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">ตะกร้าว่างเปล่า</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-4 bg-white p-4 rounded-4 shadow-sm">
    <h4 class="mb-0">ยอดรวม: <span class="text-primary">฿<span id="cartTotal">{{ number_format($total, 2) }}</span></span></h4>
    @if(count($items) > 0)
        <button id="checkoutBtn" class="btn btn-primary btn-lg rounded-pill px-4">ยืนยันการสั่งซื้อ</button>
    @endif
</div>
@endsection
@section('scripts')<script src="/js/cart.js"></script>@endsection