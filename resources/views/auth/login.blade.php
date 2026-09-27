@extends('layouts.app')
@section('title', 'เข้าสู่ระบบ')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card p-2">
            <div class="card-body p-4">
                <h4 class="mb-4 fw-semibold text-center">เข้าสู่ระบบ</h4>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/login">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">อีเมล</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required maxlength="255">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">รหัสผ่าน</label>
                        <input type="password" name="password" class="form-control" required minlength="8" maxlength="100">
                    </div>
                    <button class="btn btn-primary w-100 rounded-pill py-2">เข้าสู่ระบบ</button>
                </form>
                <p class="mt-3 mb-0 text-center text-muted">ยังไม่มีบัญชี? <a href="/register" class="text-primary">สมัครสมาชิก</a></p>
            </div>
        </div>
    </div>
</div>
@endsection