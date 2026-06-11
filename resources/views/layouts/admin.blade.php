{{-- ============================================================
     layouts/admin.blade.php
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — NusaMarket Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --admin-sidebar: #1a1a2e;
            --admin-sidebar-active: #e94560;
            --admin-accent: #0f3460;
        }
        body { background: #f0f2f5; font-size: .9rem; }
        .sidebar { width: 240px; min-height: 100vh; background: var(--admin-sidebar); position: fixed; top: 0; left: 0; z-index: 100; display: flex; flex-direction: column; }
        .sidebar-brand { padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.08); }
        .sidebar-brand span { font-size: 1.1rem; font-weight: 700; color: #fff; }
        .sidebar-nav { flex: 1; padding: .75rem 0; overflow-y: auto; }
        .sidebar-section { font-size: .68rem; font-weight: 600; color: rgba(255,255,255,.35); letter-spacing: .1em; text-transform: uppercase; padding: .75rem 1.5rem .25rem; }
        .sidebar-link { display: flex; align-items: center; gap: .75rem; padding: .55rem 1.5rem; color: rgba(255,255,255,.7); text-decoration: none; font-size: .85rem; transition: all .15s; }
        .sidebar-link:hover { color: #fff; background: rgba(255,255,255,.06); }
        .sidebar-link.active { color: #fff; background: var(--admin-sidebar-active); border-radius: 0; }
        .sidebar-link i { font-size: 1rem; width: 20px; }
        .main-wrap { margin-left: 240px; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e5e7eb; padding: .75rem 1.5rem; position: sticky; top: 0; z-index: 99; }
        .content-area { padding: 1.5rem; }
        .stat-card { background: #fff; border-radius: 10px; padding: 1.25rem; border: none; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
        .stat-card .icon-wrap { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-active  { background: #d1e7dd; color: #0a3622; }
        .badge-suspended { background: #f8d7da; color: #842029; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-shop text-danger me-2"></i>
        <span>NusaMarket</span>
        <div style="font-size:.65rem;color:rgba(255,255,255,.4);margin-top:2px">Admin Panel</div>
    </div>
    <nav class="sidebar-nav">
        <div class="sidebar-section">Overview</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="sidebar-section">Manajemen</div>
        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Pengguna
        </a>
        <a href="{{ route('admin.stores.index') }}" class="sidebar-link {{ request()->routeIs('admin.stores.*') ? 'active' : '' }}">
            <i class="bi bi-shop"></i> Toko
            @php $pendingCount = \App\Models\Store::where('status','pending_review')->count(); @endphp
            @if($pendingCount > 0)
            <span class="badge bg-danger ms-auto" style="font-size:.65rem">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Produk
        </a>

        <div class="sidebar-section">Lainnya</div>
        <a href="{{ route('home') }}" class="sidebar-link" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> Lihat Website
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-100 border-0 text-start" style="background:none">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </button>
        </form>
    </nav>
    <div style="padding:1rem 1.5rem;border-top:1px solid rgba(255,255,255,.08)">
        <div class="d-flex align-items-center gap-2">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=e94560&color=fff&size=32"
                 class="rounded-circle" width="32" height="32">
            <div style="line-height:1.2">
                <div style="font-size:.8rem;color:#fff;font-weight:600">{{ auth()->user()->name }}</div>
                <div style="font-size:.68rem;color:rgba(255,255,255,.4)">Administrator</div>
            </div>
        </div>
    </div>
</aside>

{{-- Main --}}
<div class="main-wrap">
    {{-- Topbar --}}
    <div class="topbar d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold text-dark">@yield('page-title', 'Dashboard')</h6>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">{{ now()->format('d M Y') }}</span>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success') || session('error'))
    <div class="px-4 pt-3">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 mb-0">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-0">
            <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
    </div>
    @endif

    <div class="content-area">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
@stack('scripts')
</body>
</html>


{{-- ============================================================
     pages/admin/dashboard.blade.php
     ============================================================ --}}
