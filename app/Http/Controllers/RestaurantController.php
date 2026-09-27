<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct(protected FirebaseService $firebase) {}

    /** สาธารณะ: ดูข้อมูลร้าน (ไม่ต้อง login) */
    public function show()
    {
        $restaurant = $this->firebase->first('restaurants', []) ?? [];
        return view('restaurant.show', compact('restaurant'));
    }

    /** เฉพาะ admin: แก้ไขข้อมูลร้าน */
    public function update(Request $request)
    {
        $data = $request->validate([
            'id'          => ['required', 'string'],
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_open'     => ['required', 'boolean'],
            'open_time'   => ['required', 'date_format:H:i'],
            'close_time'  => ['required', 'date_format:H:i'],
            'address'     => ['nullable', 'string', 'max:300'],
            'phone'       => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]*$/'],
        ]);

        $id = $data['id'];
        unset($data['id']);

        // sanitize ข้อความอิสระ ป้องกัน stored XSS (Blade {{ }} escape ตอน render อยู่แล้ว
        // แต่ strip_tags ที่ต้นทางกันกรณีถูกนำไปใช้นอก Blade เช่น export/JSON)
        $data['name'] = strip_tags($data['name']);
        $data['description'] = strip_tags($data['description'] ?? '');
        $data['address'] = strip_tags($data['address'] ?? '');

        $this->firebase->update('restaurants', $id, $data);

        return response()->json(['message' => 'อัปเดตข้อมูลร้านสำเร็จ']);
    }
}
