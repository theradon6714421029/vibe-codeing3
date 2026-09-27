@extends('layouts.app')
@section('title', 'จัดการหมวดหมู่')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-semibold mb-0">จัดการหมวดหมู่</h3>
    <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#catModal" onclick="openCategoryModal()">
        <i class="bi bi-plus-lg"></i> เพิ่มหมวดหมู่
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th class="ps-4">ชื่อ</th><th>ลำดับ</th><th>สถานะ</th><th class="pe-4"></th></tr>
            </thead>
            <tbody id="categoryTableBody">
            @foreach($categories as $cat)
                <tr data-id="{{ $cat['id'] }}" data-name="{{ $cat['name'] }}" data-sort="{{ $cat['sort_order'] }}" data-active="{{ $cat['is_active'] ? 1 : 0 }}">
                    <td class="ps-4">{{ $cat['name'] }}</td>
                    <td>{{ $cat['sort_order'] }}</td>
                    <td>
                        @if($cat['is_active'])
                            <span class="badge bg-success">เปิดใช้งาน</span>
                        @else
                            <span class="badge bg-secondary">ปิด</span>
                        @endif
                    </td>
                    <td class="pe-4">
                        <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#catModal" onclick="openCategoryModal(this.closest('tr'))">แก้ไข</button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill delete-category-btn">ลบ</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="catModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:16px;border:none">
      <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-semibold">หมวดหมู่</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body p-4">
        <form id="categoryForm">
          <input type="hidden" id="cat_id">
          <div class="mb-3"><label class="form-label">ชื่อหมวดหมู่</label><input type="text" id="cat_name" class="form-control" maxlength="100" required></div>
          <div class="mb-3"><label class="form-label">ลำดับ</label><input type="number" id="cat_sort" class="form-control" value="0" min="0"></div>
          <div class="form-check mb-4"><input type="checkbox" id="cat_active" class="form-check-input" checked><label class="form-check-label">เปิดใช้งาน</label></div>
          <button class="btn btn-primary w-100 rounded-pill py-2">บันทึก</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')<script src="/js/admin-categories.js"></script>@endsection