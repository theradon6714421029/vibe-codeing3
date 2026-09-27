<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function __construct(protected FirebaseService $firebase) {}

    /** สาธารณะ: หน้ารายการเมนู รองรับ search / filter by category */
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => ['nullable', 'string', 'max:100'],
            'q'           => ['nullable', 'string', 'max:100'],
        ]);

        $wheres = [];
        if ($request->filled('category_id')) {
            $wheres[] = ['category_id', '=', (string) $request->input('category_id')];
        }

        $menus = $this->firebase->all('menus', $wheres);
        $categories = $this->firebase->all('categories', [['is_active', '=', true]], 'sort_order');

        // ค้นหาด้วยชื่อทำฝั่ง PHP (Firestore ไม่รองรับ full-text search แบบ SQL LIKE)
        if ($request->filled('q')) {
            $q = Str::lower($request->string('q'));
            $menus = array_values(array_filter($menus, function ($m) use ($q) {
                return str_contains(Str::lower($m['name'] ?? ''), $q);
            }));
        }

        return view('menu.index', compact('menus', 'categories'));
    }

    /** สาธารณะ: ดูรายละเอียดเมนู */
    public function show(string $id)
    {
        $menu = $this->firebase->find('menus', $id);
        if (!$menu) {
            abort(404, 'ไม่พบเมนูนี้');
        }
        return view('menu.show', compact('menu'));
    }

    /** admin: เพิ่มเมนู (พร้อมรูปภาพ) */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'price'        => ['required', 'numeric', 'min:0', 'max:100000'],
            'category_id'  => ['required', 'string', 'max:100'],
            'is_available' => ['required', 'boolean'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if (!$this->firebase->find('categories', $data['category_id'])) {
            abort(422, 'หมวดหมู่ไม่ถูกต้อง');
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $this->storeImage($request->file('image'));
        }

        $id = $this->firebase->create('menus', [
            'name'         => strip_tags($data['name']),
            'description'  => strip_tags($data['description'] ?? ''),
            'price'        => round((float)$data['price'], 2),
            'category_id'  => $data['category_id'],
            'is_available' => $data['is_available'],
            'image_url'    => $imageUrl,
        ]);

        return response()->json(['id' => $id, 'message' => 'เพิ่มเมนูสำเร็จ'], 201);
    }

    /** admin: แก้ไขเมนู */
    public function update(Request $request, string $id)
    {
        $menu = $this->firebase->find('menus', $id);
        if (!$menu) {
            abort(404, 'ไม่พบเมนูนี้');
        }

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'price'        => ['required', 'numeric', 'min:0', 'max:100000'],
            'category_id'  => ['required', 'string', 'max:100'],
            'is_available' => ['required', 'boolean'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if (!$this->firebase->find('categories', $data['category_id'])) {
            abort(422, 'หมวดหมู่ไม่ถูกต้อง');
        }

        $update = [
            'name'         => strip_tags($data['name']),
            'description'  => strip_tags($data['description'] ?? ''),
            'price'        => round((float)$data['price'], 2),
            'category_id'  => $data['category_id'],
            'is_available' => $data['is_available'],
        ];

        if ($request->hasFile('image')) {
            $update['image_url'] = $this->storeImage($request->file('image'));
        }

        $this->firebase->update('menus', $id, $update);

        return response()->json(['message' => 'แก้ไขเมนูสำเร็จ']);
    }

    public function destroy(string $id)
    {
        if (!$this->firebase->find('menus', $id)) {
            abort(404, 'ไม่พบเมนูนี้');
        }

        $this->firebase->delete('menus', $id);
        return response()->json(['message' => 'ลบเมนูสำเร็จ']);
    }

    /** เก็บไฟล์ผ่าน Laravel storage (validated แล้วโดย 'image' rule) คืน public URL */
    protected function storeImage($file): string
    {
        // ใช้ชื่อไฟล์สุ่มเสมอ ห้ามใช้ชื่อไฟล์เดิมจากผู้ใช้ (ป้องกัน path traversal / overwrite)
        $path = $file->store('menu-images', 'public');
        return \Illuminate\Support\Facades\Storage::url($path);
    }
}
