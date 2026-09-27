@extends('layouts.app')
@section('title', 'ออเดอร์ของฉัน')
@section('content')
<h3 class="mb-4 fw-semibold">ประวัติออเดอร์ของฉัน</h3>
<div class="card">
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th class="ps-4">รหัสออเดอร์</th><th>ยอดรวม</th><th>สถานะ</th><th>วันที่</th><th class="pe-4"></th></tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="ps-4"><code>{{ \Illuminate\Support\Str::limit($order['id'], 10) }}</code></td>
                    <td class="fw-semibold">฿{{ number_format($order['total'], 2) }}</td>
                    <td>
                        @php
                            $statusColor = ['pending'=>'warning','preparing'=>'info','ready'=>'primary','completed'=>'success','cancelled'=>'danger'][$order['status']] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $statusColor }}">{{ $order['status'] }}</span>
                    </td>
                    <td class="text-muted">{{ \Illuminate\Support\Carbon::parse($order['created_at'])->format('d/m/Y H:i') }}</td>
                    <td class="pe-4"><a href="/orders/{{ $order['id'] }}" class="btn btn-sm btn-outline-primary rounded-pill">ดูรายละเอียด</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">ยังไม่มีออเดอร์</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection