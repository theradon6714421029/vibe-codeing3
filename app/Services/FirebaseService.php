<?php

namespace App\Services;

use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\DocumentReference;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * FirebaseService
 *
 * จุดเดียวที่คุยกับ Firestore ทั้งระบบ (Service Layer)
 * - ใช้ Admin SDK เท่านั้น รันฝั่ง Server (Laravel) ห้าม expose ไปยัง Frontend
 * - ทุก method รับ/คืนข้อมูลแบบ array เพื่อไม่ผูก Controller กับ SDK โดยตรง
 */
class FirebaseService
{
    protected FirestoreClient $firestore;

    public function __construct()
    {
        $credentialsPath = config('firebase.credentials');

        if (!$credentialsPath || !is_file($credentialsPath)) {
            throw new RuntimeException(
                'Firebase credentials file not found. ตรวจสอบ FIREBASE_CREDENTIALS ใน .env ' .
                'และห้ามวางไฟล์นี้ไว้ใน public/ เด็ดขาด'
            );
        }

        $this->firestore = new FirestoreClient([
            'projectId'   => config('firebase.project_id'),
            'credentials' => $credentialsPath,
            'databaseId'  => config('firebase.database_id', '(default)'),
            'transport'   => 'rest',
        ]);
    }

    /** ดึง document เดียวตาม id, คืน null ถ้าไม่พบ (ไม่ throw เพื่อให้ controller ตัดสินใจ 404 เอง) */
    public function find(string $collection, string $id): ?array
    {
        $doc = $this->firestore->collection($collection)->document($id)->snapshot();

        if (!$doc->exists()) {
            return null;
        }

        return $this->withId($doc->id(), $doc->data());
    }

    /** ดึงทั้ง collection แบบมี filter/order/limit อย่างง่าย */
    public function all(string $collection, array $wheres = [], ?string $orderBy = null, int $limit = 0): array
    {
        $query = $this->firestore->collection($collection);

        foreach ($wheres as $where) {
            // $where = [field, operator, value]
            $query = $query->where($where[0], $where[1], $where[2]);
        }

        if ($orderBy) {
            $query = $query->orderBy($orderBy);
        }

        if ($limit > 0) {
            $query = $query->limit($limit);
        }

        $results = [];
        foreach ($query->documents() as $doc) {
            if ($doc->exists()) {
                $results[] = $this->withId($doc->id(), $doc->data());
            }
        }

        return $results;
    }

    /** ดึง document แรกที่ match เงื่อนไข (เช่น หา user จาก email) */
    public function first(string $collection, array $wheres): ?array
    {
        $rows = $this->all($collection, $wheres, null, 1);
        return $rows[0] ?? null;
    }

    /** สร้าง document ใหม่ พร้อม timestamps มาตรฐาน คืนค่า id ที่ถูกสร้าง */
    public function create(string $collection, array $data): string
    {
        $now = now()->toIso8601String();
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        $ref = $this->firestore->collection($collection)->newDocument();
        $ref->set($data);

        return $ref->id();
    }

    /** สร้าง document ด้วย id ที่กำหนดเอง (เช่น ผูกกับ auth uid) */
    public function createWithId(string $collection, string $id, array $data): void
    {
        $now = now()->toIso8601String();
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        $this->firestore->collection($collection)->document($id)->set($data);
    }

    /** อัปเดตเฉพาะ field ที่ระบุ (merge ไม่ overwrite ทั้ง document) */
    public function update(string $collection, string $id, array $data): void
    {
        $data['updated_at'] = now()->toIso8601String();

        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = ['path' => $key, 'value' => $value];
        }

        $this->firestore->collection($collection)->document($id)->update($fields);
    }

    public function delete(string $collection, string $id): void
    {
        $this->firestore->collection($collection)->document($id)->delete();
    }

    /** ---- Sub-collection helpers (ใช้กับ orders/{orderId}/order_items) ---- */

    public function createSub(string $collection, string $parentId, string $subCollection, array $data): string
    {
        $now = now()->toIso8601String();
        $data['created_at'] = $now;

        $ref = $this->firestore
            ->collection($collection)->document($parentId)
            ->collection($subCollection)->newDocument();
        $ref->set($data);

        return $ref->id();
    }

    public function allSub(string $collection, string $parentId, string $subCollection): array
    {
        $query = $this->firestore
            ->collection($collection)->document($parentId)
            ->collection($subCollection);

        $results = [];
        foreach ($query->documents() as $doc) {
            if ($doc->exists()) {
                $results[] = $this->withId($doc->id(), $doc->data());
            }
        }

        return $results;
    }

    protected function withId(string $id, array $data): array
    {
        return array_merge(['id' => $id], $data);
    }
}
