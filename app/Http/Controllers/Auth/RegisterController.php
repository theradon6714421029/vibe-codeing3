<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function __construct(protected FirebaseService $firebase) {}

    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
            'phone'    => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]*$/'],
        ]);

        $email = strtolower(trim($data['email']));

        // เช็คซ้ำที่ backend เสมอ (อย่าไว้ใจ validation ฝั่ง frontend เท่านั้น)
        if ($this->firebase->first('users', [['email', '=', $email]])) {
            throw ValidationException::withMessages(['email' => 'อีเมลนี้ถูกใช้งานแล้ว']);
        }

        $id = $this->firebase->create('users', [
            'name'      => strip_tags($data['name']),
            'email'     => $email,
            'password'  => Hash::make($data['password']),
            'phone'     => strip_tags($data['phone'] ?? ''),
            'role'      => 'user', // บังคับ role เป็น user เสมอตอนสมัคร ห้ามรับ role จาก request
            'is_active' => true,
        ]);

        Auth::loginUsingId($id);
        $request->session()->regenerate();

        return redirect('/menu');
    }
}
