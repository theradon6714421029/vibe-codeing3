@extends('layouts.app')
@section('title', 'รายละเอียดออเดอร์')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-semibold mb-0">ออเดอร์ <code>{{ \Illuminate\Support\Str::limit($order['id'], 10) }}</code></h3>
    @php
        $statusColor = ['pending'=>'warning','preparing'=>'info','ready'=>'primary','completed'=>'success','cancelled'=>'danger'][$order['status']] ?? 'secondary';
    @endphp
    <span class="badge bg-{{ $statusColor }} fs-6">{{ $order['status'] }}</span>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th class="ps-4">เมนู</th><th>ราคา</th><th>จำนวน</th><th class="pe-4">รวม</th></tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td class="ps-4">{{ $item['menu_name'] }}</td>
                    <td>฿{{ number_format($item['price'], 2) }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td class="pe-4 fw-semibold text-primary">฿{{ number_format($item['line_total'], 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-4 bg-white p-4 rounded-4 shadow-sm">
    <h4 class="mb-0">ยอดรวม: <span class="text-primary">฿{{ number_format($order['total'], 2) }}</span></h4>
</div>

@if(!empty($order['note']))
    <p class="text-muted mt-3">หมายเหตุ: {{ $order['note'] }}</p>
@endif
@endsection