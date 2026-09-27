<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * FirestoreUser
 *
 * ไม่ใช่ Eloquent Model เพราะข้อมูล user เก็บใน Firestore ไม่ใช่ SQL DB
 * ต้อง implement Authenticatable เองเพื่อให้ Laravel Auth (Auth::user(), Auth::attempt) ใช้งานได้ปกติ
 */
class FirestoreUser implements Authenticatable
{
    public string $id;
    public string $name;
    public string $email;
    public string $password; // hashed
    public string $role;     // 'admin' | 'user'
    public bool $is_active;

    public function __construct(array $attrs)
    {
        $this->id       = $attrs['id'];
        $this->name     = $attrs['name'] ?? '';
        $this->email    = $attrs['email'] ?? '';
        $this->password = $attrs['password'] ?? '';
        $this->role     = $attrs['role'] ?? 'user';
        $this->is_active = (bool)($attrs['is_active'] ?? true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ---- Authenticatable contract ----
    public function getAuthIdentifierName() { return 'id'; }
    public function getAuthIdentifier() { return $this->id; }
    public function getAuthPasswordName() { return 'password'; }
    public function getAuthPassword() { return $this->password; }
    public function getRememberToken() { return null; } // remember-me ปิดไว้เพื่อความง่าย/ปลอดภัย
    public function setRememberToken($value) {}
    public function getRememberTokenName() { return null; }
}
