<?php

namespace App\Auth;

use App\Models\FirestoreUser;
use App\Services\FirebaseService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * FirestoreUserProvider
 *
 * ผูก Laravel Auth เข้ากับ Firestore collection "users"
 * ต้อง register ใน config/auth.php (ดูคำแนะนำใน README)
 */
class FirestoreUserProvider implements UserProvider
{
    public function __construct(protected FirebaseService $firebase) {}

    public function retrieveById($identifier): ?Authenticatable
    {
        $data = $this->firebase->find('users', $identifier);
        return $data ? new FirestoreUser($data) : null;
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null; // remember-me ไม่รองรับ (จงใจ ปิดเพื่อลด attack surface)
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        // no-op
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (empty($credentials['email'])) {
            return null;
        }

        // normalize email ก่อน query ป้องกัน case-mismatch / injection แปลก ๆ
        $email = Str::lower(trim($credentials['email']));
        $data = $this->firebase->first('users', [['email', '=', $email]]);

        return $data ? new FirestoreUser($data) : null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return Hash::check($credentials['password'], $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        // ใช้ bcrypt คงที่ ไม่ต้อง rehash อัตโนมัติในระบบนี้
    }
}
