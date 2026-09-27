@extends('layouts.app')
@section('title', 'จัดการเมนู')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-semibold mb-0">จัดการเมนู</h3>
    <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#menuModal" onclick="openMenuModal()">
        <i class="bi bi-plus-lg"></i> เพิ่มเมนู
    </button>
</div>

<div class="row g-3" id="menuGrid">
    @foreach($menus as $menu)
        <div class="col-md-4" data-id="{{ $menu['id'] }}" data-name="{{ $menu['name'] }}" data-description="{{ $menu['description'] }}"
             data-price="{{ $menu['price'] }}" data-category="{{ $menu['category_id'] }}" data-available="{{ $menu['is_available'] ? 1 : 0 }}">
            <div class="card hoverable h-100">
                <img src="{{ $menu['image_url'] ?? 'https://placehold.co/400x300?text=No+Image' }}" class="card-img-top" alt="{{ $menu['name'] }}">
                <div class="card-body">
                    <h6 class="fw-semibold">{{ $menu['name'] }}</h6>
                    <p class="text-primary fs-5 mb-2">฿{{ number_format($menu['price'],2) }}</p>
                    <span class="badge {{ $menu['is_available'] ? 'bg-success' : 'bg-secondary' }}">{{ $menu['is_available'] ? 'พร้อมขาย' : 'ปิดขาย' }}</span>
                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary rounded-pill edit-menu-btn" data-bs-toggle="modal" data-bs-target="#menuModal">แก้ไข</button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill delete-menu-btn">ลบ</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="modal fade" id="menuModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:16px;border:none">
      <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-semibold">เมนูอาหาร</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body p-4">
        <form id="menuForm" enctype="multipart/form-data">
          <input type="hidden" id="menu_id">
          <div class="mb-3"><label class="form-label">ชื่อเมนู</label><input type="text" id="menu_name" class="form-control" maxlength="150" required></div>
          <div class="mb-3"><label class="form-label">รายละเอียด</label><textarea id="menu_description" class="form-control" maxlength="1000"></textarea></div>
          <div class="mb-3"><label class="form-label">ราคา</label><input type="number" step="0.01" min="0" id="menu_price" class="form-control" required></div>
          <div class="mb-3">
            <label class="form-label">หมวดหมู่</label>
            <select id="menu_category" class="form-select" required>
                @foreach($categories as $cat)
                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                @endforeach
            </select>
          </div>
          <div class="mb-3"><label class="form-label">รูปภาพ</label><input type="file" id="menu_image" class="form-control" accept="image/png,image/jpeg,image/webp"></div>
          <div class="form-check mb-4"><input type="checkbox" id="menu_available" class="form-check-input" checked><label class="form-check-label">พร้อมขาย</label></div>
          <button class="btn btn-primary w-100 rounded-pill py-2">บันทึก</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')<script src="/js/admin-menus.js"></script>@endsection