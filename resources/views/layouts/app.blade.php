<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NusaMarket') — Belanja Mudah, Terpercaya</title>

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --nm-primary:   #1E3A5F;
            --nm-accent:    #2E75B6;
            --nm-accent-lt: #D6E4F0;
            --nm-orange:    #E85D24;
        }

        body { background: #f5f5f5; font-size: .9375rem; }

        /* Navbar */
        .navbar-nusa { background: var(--nm-primary); }
        .navbar-nusa .navbar-brand { font-weight: 700; font-size: 1.3rem; color: #fff !important; }
        .navbar-nusa .nav-link { color: rgba(255,255,255,.85) !important; }
        .navbar-nusa .nav-link:hover { color: #fff !important; }

        .search-bar { border-radius: 6px 0 0 6px !important; border-right: none; }
        .search-btn  { background: var(--nm-orange); border-color: var(--nm-orange); color: #fff;
                       border-radius: 0 6px 6px 0 !important; }
        .search-btn:hover { background: #c94e1a; border-color: #c94e1a; color: #fff; }

        /* Category bar */
        .cat-bar { background: var(--nm-accent); }
        .cat-bar .nav-link { color: rgba(255,255,255,.85) !important; font-size: .83rem; padding: .4rem .75rem; }
        .cat-bar .nav-link:hover,
        .cat-bar .nav-link.active { color: #fff !important; background: rgba(0,0,0,.15); border-radius: 4px; }

        /* Cards */
        .product-card { transition: box-shadow .18s, transform .18s; border: 1px solid #e8e8e8; }
        .product-card:hover { box-shadow: 0 6px 18px rgba(0,0,0,.1); transform: translateY(-2px); }
        .product-card .card-img-top { height: 170px; object-fit: cover; }
        .product-card .price { color: var(--nm-orange); font-weight: 700; }
        .product-card .sold  { font-size: .78rem; color: #888; }
        .product-card .store-name { font-size: .78rem; color: #666; }
        .product-card .wishlist-btn { position: absolute; top: 8px; right: 8px;
            background: rgba(255,255,255,.85); border: none; border-radius: 50%;
            width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;
            font-size: .95rem; cursor: pointer; transition: background .15s; }
        .product-card .wishlist-btn:hover { background: #fff; }

        /* Badges */
        .badge-sale { background: var(--nm-orange); }
        .badge-flash { background: #e8c824; color: #333; }
        .badge-new  { background: var(--nm-accent); }

        /* Rating stars */
        .stars { color: #f5a623; font-size: .82rem; }

        /* Sidebar filter */
        .filter-card { background: #fff; border-radius: 8px; border: 1px solid #e8e8e8; }
        .filter-card .filter-title { font-size: .82rem; font-weight: 600; color: var(--nm-primary);
            text-transform: uppercase; letter-spacing: .04em; }

        /* Flash sale strip */
        .flash-strip { background: #fff; border-radius: 8px; border: 1px solid #e8e8e8; }
        .countdown span { background: var(--nm-primary); color: #fff; border-radius: 4px;
            padding: 2px 7px; font-size: .8rem; font-weight: 700; margin: 0 1px; }

        /* Footer */
        .footer-main { background: var(--nm-primary); color: rgba(255,255,255,.8); }
        .footer-main a { color: rgba(255,255,255,.7); text-decoration: none; font-size: .87rem; }
        .footer-main a:hover { color: #fff; }
        .footer-main h6 { color: #fff; font-weight: 600; }

        /* Toast */
        .toast-container { z-index: 9999; }

        /* Misc */
        .cursor-pointer { cursor: pointer; }
        .text-primary-nusa { color: var(--nm-primary) !important; }
        .btn-primary-nusa { background: var(--nm-accent); border-color: var(--nm-accent); color: #fff; }
        .btn-primary-nusa:hover { background: #265f91; border-color: #265f91; color: #fff; }
        .btn-orange { background: var(--nm-orange); border-color: var(--nm-orange); color: #fff; }
        .btn-orange:hover { background: #c94e1a; border-color: #c94e1a; color: #fff; }
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ===================== NAVBAR ===================== --}}
<nav class="navbar navbar-nusa navbar-expand-lg py-2 sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand me-3" href="{{ route('home') }}">
            <i class="bi bi-shop me-1"></i>NusaMarket
        </a>

        {{-- Search --}}
        <form class="d-flex flex-grow-1 me-3" action="{{ route('products.search') }}" method="GET"
              role="search" x-data="{ q: '{{ request('q') }}' }">
            <input class="form-control search-bar"
                   type="search" name="q" placeholder="Cari produk, toko, atau kategori..."
                   x-model="q" value="{{ request('q') }}">
            <button class="btn search-btn px-3" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>

        {{-- Right icons --}}
        <div class="d-flex align-items-center gap-2">
            @auth
                {{-- Notifikasi --}}
                <div x-data="{ open: false }" class="position-relative">
                    <button class="btn btn-link text-white p-1 position-relative" @click="open = !open">
                        <i class="bi bi-bell fs-5"></i>
                        @if(auth()->user()->unread_notifications_count > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
                            {{ auth()->user()->unread_notifications_count }}
                        </span>
                        @endif
                    </button>
                    <div x-show="open" x-cloak @click.away="open = false"
                         class="dropdown-menu dropdown-menu-end show shadow" style="min-width:300px;top:100%;right:0;position:absolute">
                        <h6 class="dropdown-header">Notifikasi</h6>
                        @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notif)
                        <a class="dropdown-item {{ $notif->is_read ? '' : 'fw-semibold bg-light' }}"
                           href="#">
                            <div class="small">{{ $notif->title }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ $notif->created_at->diffForHumans() }}</div>
                        </a>
                        @empty
                        <span class="dropdown-item text-muted">Tidak ada notifikasi</span>
                        @endforelse
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center small" href="{{ route('notifications.index') }}">Lihat semua</a>
                    </div>
                </div>

                {{-- Keranjang --}}
                <a href="{{ route('cart.index') }}" class="btn btn-link text-white p-1 position-relative">
                    <i class="bi bi-cart3 fs-5"></i>
                    @if(session('cart_count', 0) > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
                        {{ session('cart_count') }}
                    </span>
                    @endif
                </a>

                {{-- User dropdown --}}
                <div x-data="{ open: false }" class="position-relative">
                    <button class="btn btn-link p-0" @click="open = !open">
                        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=2E75B6&color=fff' }}"
                             class="rounded-circle" width="34" height="34" alt="Avatar">
                    </button>
                    <div x-show="open" x-cloak @click.away="open = false"
                         class="dropdown-menu dropdown-menu-end show shadow" style="top:100%;right:0;position:absolute">
                        <h6 class="dropdown-header">{{ auth()->user()->name }}</h6>
                        <a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i>Profil Saya</a>
                        <a class="dropdown-item" href="{{ route('orders.index') }}"><i class="bi bi-box-seam me-2"></i>Pesanan Saya</a>
                        <a class="dropdown-item" href="{{ route('wishlist.index') }}"><i class="bi bi-heart me-2"></i>Wishlist</a>
                        @if(auth()->user()->stores()->exists())
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('seller.dashboard') }}"><i class="bi bi-shop me-2"></i>Dashboard Toko</a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-orange btn-sm">Daftar</a>
            @endauth
        </div>
    </div>
</nav>

{{-- ===================== CATEGORY BAR ===================== --}}
<nav class="cat-bar">
    <div class="container">
        <ul class="nav flex-nowrap overflow-auto" style="scrollbar-width:none">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Semua</a></li>
            @foreach(\App\Models\Product::CATEGORIES as $key => $label)
            <li class="nav-item">
                <a class="nav-link {{ request('category') === $key ? 'active' : '' }}"
                   href="{{ route('products.index', ['category' => $key]) }}">
                    {{ $label }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</nav>

{{-- ===================== FLASH MESSAGES ===================== --}}
@if(session('success') || session('error'))
<div class="container mt-2">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
</div>
@endif

{{-- ===================== MAIN CONTENT ===================== --}}
<main class="py-3">
    @yield('content')
</main>

{{-- ===================== FOOTER ===================== --}}
<footer class="footer-main mt-4 py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3">
                <h5 class="text-white fw-bold mb-3"><i class="bi bi-shop me-1"></i>NusaMarket</h5>
                <p style="font-size:.87rem">Platform marketplace terpercaya yang menghubungkan jutaan penjual dan pembeli di seluruh Indonesia.</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#"><i class="bi bi-twitter-x fs-5"></i></a>
                    <a href="#"><i class="bi bi-tiktok fs-5"></i></a>
                </div>
            </div>
            <div class="col-md-3">
                <h6>Tentang NusaMarket</h6>
                <ul class="list-unstyled mt-2">
                    <li class="mb-1"><a href="#">Tentang Kami</a></li>
                    <li class="mb-1"><a href="#">Karir</a></li>
                    <li class="mb-1"><a href="#">Blog</a></li>
                    <li class="mb-1"><a href="#">Press</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6>Bantuan</h6>
                <ul class="list-unstyled mt-2">
                    <li class="mb-1"><a href="#">Pusat Bantuan</a></li>
                    <li class="mb-1"><a href="#">Cara Berbelanja</a></li>
                    <li class="mb-1"><a href="#">Cara Berjualan</a></li>
                    <li class="mb-1"><a href="#">Kebijakan Privasi</a></li>
                    <li class="mb-1"><a href="#">Syarat & Ketentuan</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6>Metode Pembayaran</h6>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach(['BCA','Mandiri','BRI','BNI','GoPay','OVO','Dana','QRIS'] as $pay)
                    <span class="badge bg-secondary" style="font-size:.75rem">{{ $pay }}</span>
                    @endforeach
                </div>
                <h6 class="mt-3">Download App</h6>
                <div class="d-flex gap-2 mt-2">
                    <a href="#" class="btn btn-outline-light btn-sm"><i class="bi bi-apple me-1"></i>App Store</a>
                    <a href="#" class="btn btn-outline-light btn-sm"><i class="bi bi-google-play me-1"></i>Play Store</a>
                </div>
            </div>
        </div>
        <hr class="border-secondary mt-4">
        <p class="text-center mb-0" style="font-size:.82rem">© {{ date('Y') }} NusaMarket. Seluruh hak cipta dilindungi.</p>
    </div>
</footer>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>

@stack('scripts')
</body>
</html>
