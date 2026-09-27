<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(protected FirebaseService $firebase) {}

    /** เฉพาะออเดอร์ของตัวเอง — query กรองด้วย user_id จาก session ไม่รับจาก request */
    public function index()
    {
        $orders = $this->firebase->all('orders', [['user_id', '=', Auth::id()]], 'created_at');
        return view('orders.index', compact('orders'));
    }

    /** ดูออเดอร์เดียว: ต้องเป็นเจ้าของเท่านั้น (กัน IDOR) admin ดูได้ทุกใบผ่าน admin controller แยก */
    public function show(string $id)
    {
        $order = $this->firebase->find('orders', $id);

        if (!$order) {
            abort(404, 'ไม่พบออเดอร์นี้');
        }

        if ($order['user_id'] !== Auth::id()) {
            abort(403, 'คุณไม่มีสิทธิ์ดูออเดอร์นี้');
        }

        $items = $this->firebase->allSub('orders', $id, 'order_items');

        return view('orders.show', compact('order', 'items'));
    }

    /**
     * สร้างออเดอร์จากตะกร้าใน session
     * ราคาทุกบรรทัดคำนวณใหม่จาก Firestore ณ ตอนนี้เสมอ — ไม่เชื่อ price/total ใด ๆ ที่ frontend ส่งมา
     */
    public function store(Request $request)
    {
        $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            abort(422, 'ตะกร้าว่างเปล่า');
        }

        $restaurant = $this->firebase->first('restaurants', []);
        if (!$restaurant || !($restaurant['is_open'] ?? false)) {
            abort(422, 'ขณะนี้ร้านปิดให้บริการ ไม่สามารถสั่งอาหารได้');
        }

        $lineItems = [];
        $subtotal = 0.0;

        foreach ($cart as $menuId => $qty) {
            $menu = $this->firebase->find('menus', $menuId);

            if (!$menu || !($menu['is_available'] ?? false)) {
                abort(422, "เมนู '".($menu['name'] ?? $menuId)."' ไม่พร้อมให้บริการแล้ว กรุณาปรับตะกร้า");
            }

            $qty = max(1, min(50, (int)$qty));
            $lineTotal = round($menu['price'] * $qty, 2);
            $subtotal += $lineTotal;

            $lineItems[] = [
                'menu_id'    => $menuId,
                'menu_name'  => $menu['name'],
                'price'      => $menu['price'],
                'quantity'   => $qty,
                'line_total' => $lineTotal,
            ];
        }

        $orderId = $this->firebase->create('orders', [
            'user_id'       => Auth::id(), // มาจาก session เท่านั้น ห้ามรับจาก request
            'restaurant_id' => $restaurant['id'],
            'status'        => 'pending',
            'subtotal'      => round($subtotal, 2),
            'total'         => round($subtotal, 2), // ยังไม่มีระบบส่วนลด/ค่าส่งในเวอร์ชันนี้
            'note'          => strip_tags($request->input('note', '')),
        ]);

        foreach ($lineItems as $item) {
            $this->firebase->createSub('orders', $orderId, 'order_items', $item);
        }

        $request->session()->forget('cart');

        return response()->json(['message' => 'สั่งอาหารสำเร็จ', 'order_id' => $orderId], 201);
    }
}
