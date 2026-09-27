@extends('layouts.app')
@section('title', 'จัดการออเดอร์')
@section('content')
<h3 class="mb-4 fw-semibold">จัดการออเดอร์</h3>
<div class="card">
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th class="ps-4">รหัส</th><th>ยอดรวม</th><th>สถานะ</th><th>วันที่</th><th class="pe-4">เปลี่ยนสถานะ</th></tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr data-id="{{ $order['id'] }}">
                    <td class="ps-4"><code>{{ \Illuminate\Support\Str::limit($order['id'], 10) }}</code></td>
                    <td class="fw-semibold">฿{{ number_format($order['total'],2) }}</td>
                    <td class="status-cell">
                        @php
                            $statusColor = ['pending'=>'warning','preparing'=>'info','ready'=>'primary','completed'=>'success','cancelled'=>'danger'][$order['status']] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $statusColor }}">{{ $order['status'] }}</span>
                    </td>
                    <td class="text-muted">{{ \Illuminate\Support\Carbon::parse($order['created_at'])->format('d/m/Y H:i') }}</td>
                    <td class="pe-4">
                        <select class="form-select form-select-sm status-select" style="width:150px">
                            <option value="pending" @selected($order['status']=='pending')>pending</option>
                            <option value="preparing" @selected($order['status']=='preparing')>preparing</option>
                            <option value="ready" @selected($order['status']=='ready')>ready</option>
                            <option value="completed" @selected($order['status']=='completed')>completed</option>
                            <option value="cancelled" @selected($order['status']=='cancelled')>cancelled</option>
                        </select>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')<script src="/js/admin-orders.js"></script>@endsection