<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** ทุก action ผูก middleware role:admin ที่ routes/web.php แล้ว */
class OrderController extends Controller
{
    // นิยาม state machine ชัดเจน ป้องกันการเปลี่ยนสถานะข้ามขั้นตอนโดยพลการ
    protected const TRANSITIONS = [
        'pending'   => ['preparing', 'cancelled'],
        'preparing' => ['ready', 'cancelled'],
        'ready'     => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function __construct(protected FirebaseService $firebase) {}

    public function index(Request $request)
    {
        $status = $request->query('status');
        $wheres = $status ? [['status', '=', $status]] : [];
        $orders = $this->firebase->all('orders', $wheres, 'created_at');

        return view('admin.orders.index', compact('orders'));
    }

    public function show(string $id)
    {
        $order = $this->firebase->find('orders', $id);
        if (!$order) {
            abort(404, 'ไม่พบออเดอร์นี้');
        }

        $items = $this->firebase->allSub('orders', $id, 'order_items');
        return response()->json(['order' => $order, 'items' => $items]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'preparing', 'ready', 'completed', 'cancelled'])],
        ]);

        $order = $this->firebase->find('orders', $id);
        if (!$order) {
            abort(404, 'ไม่พบออเดอร์นี้');
        }

        $current = $order['status'];
        $allowedNext = self::TRANSITIONS[$current] ?? [];

        if (!in_array($data['status'], $allowedNext, true)) {
            abort(422, "ไม่สามารถเปลี่ยนสถานะจาก '{$current}' เป็น '{$data['status']}' ได้");
        }

        $this->firebase->update('orders', $id, ['status' => $data['status']]);

        return response()->json(['message' => 'อัปเดตสถานะสำเร็จ']);
    }
}
