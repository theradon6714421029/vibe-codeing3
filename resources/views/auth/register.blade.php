@extends('layouts.app')
@section('title', 'สมัครสมาชิก')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card p-2">
            <div class="card-body p-4">
                <h4 class="mb-4 fw-semibold text-center">สมัครสมาชิก</h4>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/register">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">ชื่อ</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">อีเมล</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">เบอร์โทร</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่าน</label>
                        <input type="password" name="password" class="form-control" required minlength="8" maxlength="100">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">ยืนยันรหัสผ่าน</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8" maxlength="100">
                    </div>
                    <button class="btn btn-primary w-100 rounded-pill py-2">สมัครสมาชิก</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection