<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

/**
 * CartController
 *
 * ตะกร้าเก็บใน Session (server-side) ไม่เก็บที่ Firestore เพื่อลดความซับซ้อน
 * เก็บแค่ menu_id + quantity ใน session — "ราคา" จะถูกคำนวณใหม่จาก DB เสมอทุกครั้งที่แสดงผล/checkout
 * เพื่อป้องกันผู้ใช้แก้ราคาผ่าน DevTools/Frontend (price tampering)
 */
class CartController extends Controller
{
    public function __construct(protected FirebaseService $firebase) {}

    public function index(Request $request)
    {
        [$items, $total] = $this->buildCart($request);
        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'menu_id'  => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $menu = $this->firebase->find('menus', $data['menu_id']);
        if (!$menu || !($menu['is_available'] ?? false)) {
            abort(422, 'เมนูนี้ไม่พร้อมให้บริการ');
        }

        $cart = $request->session()->get('cart', []);
        $cart[$data['menu_id']] = ($cart[$data['menu_id']] ?? 0) + $data['quantity'];
        $request->session()->put('cart', $cart);

        return response()->json(['message' => 'เพิ่มลงตะกร้าแล้ว', 'count' => array_sum($cart)]);
    }

    public function updateQuantity(Request $request)
    {
        $data = $request->validate([
            'menu_id'  => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $cart = $request->session()->get('cart', []);
        if (!isset($cart[$data['menu_id']])) {
            abort(404, 'ไม่พบรายการนี้ในตะกร้า');
        }

        $cart[$data['menu_id']] = $data['quantity'];
        $request->session()->put('cart', $cart);

        [$items, $total] = $this->buildCart($request);
        return response()->json(['message' => 'อัปเดตจำนวนแล้ว', 'total' => $total]);
    }

    public function remove(Request $request)
    {
        $data = $request->validate(['menu_id' => ['required', 'string', 'max:100']]);

        $cart = $request->session()->get('cart', []);
        unset($cart[$data['menu_id']]);
        $request->session()->put('cart', $cart);

        return response()->json(['message' => 'ลบรายการแล้ว']);
    }

    /**
     * ประกอบตะกร้าจาก session + ราคาปัจจุบันจาก Firestore เสมอ (never trust client price)
     * @return array{0: array, 1: float}
     */
    protected function buildCart(Request $request): array
    {
        $cart = $request->session()->get('cart', []);
        $items = [];
        $total = 0.0;

        foreach ($cart as $menuId => $qty) {
            $menu = $this->firebase->find('menus', $menuId);
            if (!$menu) {
                continue; // เมนูอาจถูกลบไปแล้ว ข้ามไปเงียบ ๆ
            }
            $lineTotal = round($menu['price'] * $qty, 2);
            $items[] = [
                'menu_id'    => $menuId,
                'name'       => $menu['name'],
                'price'      => $menu['price'],
                'quantity'   => $qty,
                'line_total' => $lineTotal,
                'available'  => $menu['is_available'] ?? false,
            ];
            $total += $lineTotal;
        }

        return [$items, round($total, 2)];
    }
}
