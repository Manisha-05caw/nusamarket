@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<div class="container">

    {{-- ===== HERO BANNER ===== --}}
    <div id="heroBanner" class="carousel slide rounded-3 overflow-hidden mb-3 shadow-sm" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @foreach($banners as $i => $banner)
            <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="{{ $i }}"
                    class="{{ $i === 0 ? 'active' : '' }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach($banners as $i => $banner)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                <img src="{{ $banner['image'] }}" class="d-block w-100" alt="{{ $banner['title'] }}"
                     style="height:260px;object-fit:cover">
                <div class="carousel-caption text-start d-none d-md-block" style="bottom:24px;left:40px">
                    <h5 class="fw-bold">{{ $banner['title'] }}</h5>
                    <p class="small mb-2">{{ $banner['subtitle'] }}</p>
                    <a href="{{ $banner['url'] }}" class="btn btn-orange btn-sm">{{ $banner['cta'] }}</a>
                </div>
            </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroBanner" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroBanner" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    {{-- ===== FLASH SALE ===== --}}
    <div class="flash-strip p-3 mb-3"
         x-data="{
            end: new Date().getTime() + ({{ $flashSaleSeconds ?? 7200 }} * 1000),
            h: '00', m: '00', s: '00',
            init() {
                setInterval(() => {
                    const diff = this.end - new Date().getTime();
                    if(diff <= 0) { this.h = this.m = this.s = '00'; return; }
                    this.h = String(Math.floor(diff/3600000)).padStart(2,'0');
                    this.m = String(Math.floor((diff%3600000)/60000)).padStart(2,'0');
                    this.s = String(Math.floor((diff%60000)/1000)).padStart(2,'0');
                }, 1000);
            }
         }">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-flash px-2 py-1 fw-bold"><i class="bi bi-lightning-charge-fill me-1"></i>Flash Sale</span>
                <div class="countdown d-flex align-items-center gap-1">
                    <span x-text="h">00</span>
                    <small class="text-muted fw-bold">:</small>
                    <span x-text="m">00</span>
                    <small class="text-muted fw-bold">:</small>
                    <span x-text="s">00</span>
                </div>
            </div>
            <a href="{{ route('flash-sale.index') }}" class="small text-decoration-none" style="color:var(--nm-accent)">
                Lihat semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-2 row-cols-2 row-cols-sm-3 row-cols-md-5">
            @foreach($flashSaleProducts as $product)
            <div class="col">
                <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
                    <div class="card product-card h-100 border-0 shadow-sm position-relative">
                        <img src="{{ $product->thumbnail }}" class="card-img-top" alt="{{ $product->name }}">
                        <div class="card-body p-2">
                            <div class="price mb-0">Rp {{ number_format($product->sale_price, 0, ',', '.') }}</div>
                            <div class="sold">{{ $product->total_sold }} terjual</div>
                            <div class="progress mt-1" style="height:4px">
                                <div class="progress-bar bg-danger" style="width:{{ $product->sale_percent }}%"></div>
                            </div>
                            <div style="font-size:.72rem;color:#e85d24" class="mt-1">
                                Hemat {{ $product->discount_percent }}%
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ===== MAIN CONTENT: FILTER + PRODUCTS ===== --}}
    <div class="row g-3">

        {{-- SIDEBAR FILTER --}}
        <div class="col-lg-2 col-md-3 d-none d-md-block">
            @include('components.filter-sidebar')
        </div>

        {{-- PRODUCT LISTING --}}
        <div class="col-lg-10 col-md-9">

            {{-- Sort & view toggle --}}
            <div class="d-flex align-items-center justify-content-between bg-white rounded-3 px-3 py-2 mb-3 shadow-sm">
                <span class="small text-muted">
                    Menampilkan <strong>{{ $products->total() }}</strong> produk
                    @if(request('q'))<span> untuk "<strong>{{ request('q') }}</strong>"</span>@endif
                </span>
                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted mb-0">Urutkan:</label>
                    <select class="form-select form-select-sm" style="width:auto"
                            onchange="window.location.href=this.value">
                        @foreach([
                            ['label'=>'Terlaris',   'value'=>'sold'],
                            ['label'=>'Terbaru',    'value'=>'newest'],
                            ['label'=>'Harga ↑',    'value'=>'price_asc'],
                            ['label'=>'Harga ↓',    'value'=>'price_desc'],
                            ['label'=>'Rating',     'value'=>'rating'],
                        ] as $opt)
                        <option value="{{ request()->fullUrlWithQuery(['sort' => $opt['value']]) }}"
                                {{ request('sort', 'sold') === $opt['value'] ? 'selected' : '' }}>
                            {{ $opt['label'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Product grid --}}
            @if($products->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-search" style="font-size:3rem;color:#ccc"></i>
                <p class="text-muted mt-2">Produk tidak ditemukan.</p>
                <a href="{{ route('home') }}" class="btn btn-primary-nusa btn-sm">Lihat Semua Produk</a>
            </div>
            @else
            <div class="row g-2 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4">
                @foreach($products as $product)
                @include('components.product-card', ['product' => $product])
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
