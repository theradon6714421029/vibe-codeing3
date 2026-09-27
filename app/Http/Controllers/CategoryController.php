<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

/** ทุก action ในนี้ผูกกับ middleware role:admin ที่ routes/web.php */
class CategoryController extends Controller
{
    public function __construct(protected FirebaseService $firebase) {}

    public function index()
    {
        $categories = $this->firebase->all('categories', [], 'sort_order');
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $id = $this->firebase->create('categories', [
            'name'       => strip_tags($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => true,
        ]);

        return response()->json(['id' => $id, 'message' => 'เพิ่มหมวดหมู่สำเร็จ'], 201);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['required', 'boolean'],
        ]);

        // ตรวจว่ามีจริงก่อนอัปเดต ป้องกันสร้าง document ใหม่โดยไม่ตั้งใจจาก id ที่เดา
        if (!$this->firebase->find('categories', $id)) {
            abort(404, 'ไม่พบหมวดหมู่');
        }

        $data['name'] = strip_tags($data['name']);
        $this->firebase->update('categories', $id, $data);

        return response()->json(['message' => 'แก้ไขหมวดหมู่สำเร็จ']);
    }

    public function destroy(string $id)
    {
        if (!$this->firebase->find('categories', $id)) {
            abort(404, 'ไม่พบหมวดหมู่');
        }

        // กันลบหมวดหมู่ที่ยังมีเมนูผูกอยู่ (data integrity)
        $menusInCategory = $this->firebase->all('menus', [['category_id', '=', $id]], null, 1);
        if (count($menusInCategory) > 0) {
            abort(409, 'ไม่สามารถลบได้ เนื่องจากยังมีเมนูอยู่ในหมวดหมู่นี้');
        }

        $this->firebase->delete('categories', $id);
        return response()->json(['message' => 'ลบหมวดหมู่สำเร็จ']);
    }
}
