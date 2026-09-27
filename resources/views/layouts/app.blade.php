<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ร้านอาหาร')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #d84315;
            --brand-dark: #a02f0d;
        }
        * { font-family: 'Kanit', sans-serif; }
        body { background:#f5f4f2; }

        .navbar-brand { font-weight: 600; letter-spacing: .5px; }
        .navbar.bg-dark { background-color: #1c1c1c !important; }

        .btn-primary { background-color: var(--brand); border-color: var(--brand); }
        .btn-primary:hover { background-color: var(--brand-dark); border-color: var(--brand-dark); }
        .text-primary { color: var(--brand) !important; }
        .bg-primary { background-color: var(--brand) !important; }

        /* การ์ดมาตรฐาน: shadow นุ่ม + ยกตัวตอน hover */
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.06);
            transition: transform .18s ease, box-shadow .18s ease;
            overflow: hidden;
        }
        .card.food-card:hover, .card.hoverable:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0,0,0,.12);
        }

        /* รูปภาพ: สัดส่วนคงที่ทุกการ์ด ไม่บูดเบี้ยว */
        .card-img-top {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            background: #eee;
        }
        .card-img-hero {
            width: 100%;
            aspect-ratio: 16 / 7;
            object-fit: cover;
            background: #eee;
        }

        .badge { border-radius: 20px; padding: 6px 12px; font-weight: 400; }
        .toast-container { z-index: 1080; }

        .navbar-nav .nav-link { transition: opacity .15s; }
        .navbar-nav .nav-link:hover { opacity: .75; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/">🍜 ร้านอาหาร</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/menu">เมนู</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link" href="/cart">ตะกร้า</a></li>
                    <li class="nav-item"><a class="nav-link" href="/orders">ออเดอร์ของฉัน</a></li>
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Admin</a></li>
                    @endif
                @endauth
            </ul>
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item">
                        <form action="/logout" method="POST" class="d-flex">
                            @csrf
                            <button class="btn btn-outline-light btn-sm">ออกจากระบบ ({{ auth()->user()->name }})</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="/login">เข้าสู่ระบบ</a></li>
                    <li class="nav-item"><a class="nav-link" href="/register">สมัครสมาชิก</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container my-4">
    @yield('content')
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="/js/app.js"></script>
@yield('scripts')
</body>
</html>