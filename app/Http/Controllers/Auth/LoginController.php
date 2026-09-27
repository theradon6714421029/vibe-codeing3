<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        $credentials['email'] = strtolower(trim($credentials['email']));

        // throttle brute-force login ตาม email + ip
        $throttleKey = strtolower($credentials['email']).'|'.$request->ip();
        if (app('Illuminate\Cache\RateLimiter')->tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => 'พยายามเข้าสู่ระบบผิดหลายครั้งเกินไป กรุณาลองใหม่ภายหลัง',
            ]);
        }

        if (Auth::attempt($credentials)) {
            app('Illuminate\Cache\RateLimiter')->clear($throttleKey);
            $request->session()->regenerate(); // ป้องกัน session fixation

            if (!Auth::user()->is_active) {
                Auth::logout();
                throw ValidationException::withMessages(['email' => 'บัญชีนี้ถูกระงับการใช้งาน']);
            }

            return redirect()->intended(Auth::user()->isAdmin() ? '/admin/dashboard' : '/menu');
        }

        app('Illuminate\Cache\RateLimiter')->hit($throttleKey, 60);

        throw ValidationException::withMessages([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken(); // ป้องกัน CSRF token reuse หลัง logout
        return redirect('/login');
    }
}
