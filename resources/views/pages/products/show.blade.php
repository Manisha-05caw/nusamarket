@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="container">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('products.index', ['category' => $product->category]) }}">
                    {{ \App\Models\Product::CATEGORIES[$product->category] ?? $product->category }}
                </a>
            </li>
            <li class="breadcrumb-item active">{{ Str::limit($product->name, 40) }}</li>
        </ol>
    </nav>

    <div class="row g-4">

        {{-- ===== KIRI: Foto Produk ===== --}}
        <div class="col-md-5"
             x-data="{ active: 0, images: {{ $product->images->pluck('url')->toJson() }} }">

            {{-- Main image --}}
            <div class="bg-white rounded-3 border mb-2 overflow-hidden d-flex align-items-center justify-content-center"
                 style="height:360px">
                <img :src="images[active]" class="img-fluid" style="max-height:340px;object-fit:contain"
                     alt="{{ $product->name }}">
            </div>

            {{-- Thumbnail strip --}}
            <div class="d-flex gap-2 overflow-auto">
                @foreach($product->images as $i => $img)
                <div class="border rounded-2 cursor-pointer flex-shrink-0"
                     style="width:64px;height:64px;overflow:hidden"
                     :class="active === {{ $i }} ? 'border-primary border-2' : ''"
                     @click="active = {{ $i }}">
                    <img src="{{ $img->url }}" class="w-100 h-100" style="object-fit:cover"
                         alt="Foto {{ $i+1 }}">
                </div>
                @endforeach
            </div>
        </div>

        {{-- ===== TENGAH: Info Produk ===== --}}
        <div class="col-md-4"
             x-data="{
                selectedVariant: null,
                qty: 1,
                variants: {{ $product->variants->toJson() }},
                selectedSize: '',
                selectedColor: '',
                get currentVariant() {
                    return this.variants.find(v =>
                        (!this.selectedSize  || v.size  === this.selectedSize) &&
                        (!this.selectedColor || v.color === this.selectedColor)
                    );
                },
                get price() {
                    return this.currentVariant ? this.currentVariant.price : {{ $product->base_price }};
                },
                get stock() {
                    return this.currentVariant ? this.currentVariant.stock : 0;
                },
                get sizes() { return [...new Set(this.variants.map(v=>v.size).filter(Boolean))]; },
                get colors() { return [...new Set(this.variants.map(v=>v.color).filter(Boolean))]; },
             }">

            {{-- Badge toko --}}
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge" style="background:var(--nm-accent-lt);color:var(--nm-primary)">
                    <i class="bi bi-shop me-1"></i>{{ $product->store->name }}
                </span>
                @if($product->store->rating_avg >= 4.5)
                <span class="badge bg-success-subtle text-success"><i class="bi bi-patch-check me-1"></i>Terpercaya</span>
                @endif
            </div>

            <h1 class="fs-5 fw-bold mb-2">{{ $product->name }}</h1>

            {{-- Rating & terjual --}}
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="d-flex align-items-center gap-1">
                    <span class="stars">
                        @for($i=1;$i<=5;$i++)
                        <i class="bi {{ $i <= round($product->rating_avg) ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </span>
                    <a href="#reviews" class="small text-decoration-none" style="color:var(--nm-accent)">
                        {{ number_format($product->rating_avg,1) }} ({{ $product->total_reviews }} ulasan)
                    </a>
                </div>
                <span class="text-muted small">{{ number_format($product->total_sold) }} terjual</span>
            </div>

            {{-- Harga --}}
            <div class="bg-light rounded-3 p-3 mb-3">
                <div class="d-flex align-items-baseline gap-2">
                    <span class="price fs-4" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(price)">
                        Rp {{ number_format($product->base_price, 0, ',', '.') }}
                    </span>
                    @if($product->discount_percent > 0)
                    <span class="badge badge-sale">-{{ $product->discount_percent }}%</span>
                    <span class="text-muted text-decoration-line-through small">
                        Rp {{ number_format($product->base_price, 0, ',', '.') }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Pilih Ukuran --}}
            <template x-if="sizes.length > 0">
                <div class="mb-3">
                    <div class="small fw-semibold mb-2">Ukuran:</div>
                    <div class="d-flex flex-wrap gap-2">
                        <template x-for="size in sizes" :key="size">
                            <button type="button"
                                    class="btn btn-sm"
                                    :class="selectedSize === size ? 'btn-primary-nusa' : 'btn-outline-secondary'"
                                    @click="selectedSize = (selectedSize === size ? '' : size)"
                                    x-text="size"></button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Pilih Warna --}}
            <template x-if="colors.length > 0">
                <div class="mb-3">
                    <div class="small fw-semibold mb-2">Warna:</div>
                    <div class="d-flex flex-wrap gap-2">
                        <template x-for="color in colors" :key="color">
                            <button type="button"
                                    class="btn btn-sm"
                                    :class="selectedColor === color ? 'btn-primary-nusa' : 'btn-outline-secondary'"
                                    @click="selectedColor = (selectedColor === color ? '' : color)"
                                    x-text="color"></button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Stok --}}
            <div class="small text-muted mb-3">
                Stok:
                <span :class="stock > 0 ? 'text-success fw-semibold' : 'text-danger fw-semibold'"
                      x-text="stock > 0 ? stock + ' tersedia' : 'Habis'">
                    {{ $product->variants->sum('stock') }} tersedia
                </span>
            </div>

            {{-- Jumlah --}}
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="small fw-semibold">Jumlah:</div>
                <div class="input-group" style="width:120px">
                    <button class="btn btn-outline-secondary btn-sm" @click="qty = Math.max(1, qty-1)">−</button>
                    <input type="number" class="form-control form-control-sm text-center"
                           x-model="qty" min="1" :max="stock">
                    <button class="btn btn-outline-secondary btn-sm" @click="qty = Math.min(stock, qty+1)">+</button>
                </div>
                <span class="small text-muted" x-show="stock > 0" x-text="'Maks ' + stock + ' pcs'"></span>
            </div>

            {{-- CTA Buttons --}}
            @auth
            <div class="d-flex gap-2">
                <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" x-bind:value="currentVariant?.id ?? ''">
                    <input type="hidden" name="quantity" x-bind:value="qty">
                    <button type="submit" class="btn btn-outline-primary w-100" :disabled="stock === 0">
                        <i class="bi bi-cart-plus me-1"></i>Keranjang
                    </button>
                </form>
                <form action="{{ route('checkout.direct') }}" method="POST" class="flex-grow-1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" x-bind:value="currentVariant?.id ?? ''">
                    <input type="hidden" name="quantity" x-bind:value="qty">
                    <button type="submit" class="btn btn-orange w-100" :disabled="stock === 0">
                        Beli Sekarang
                    </button>
                </form>
            </div>
            @else
            <a href="{{ route('login') }}" class="btn btn-orange w-100">
                Masuk untuk Membeli
            </a>
            @endauth

            {{-- Chat dengan seller --}}
            <div class="mt-2">
                <a href="{{ route('chat.store', $product->store->id) }}?product={{ $product->id }}"
                   class="btn btn-outline-secondary w-100">
                    <i class="bi bi-chat-dots me-1"></i>Chat dengan Penjual
                </a>
            </div>
        </div>

        {{-- ===== KANAN: Info Toko & Pengiriman ===== --}}
        <div class="col-md-3">

            {{-- Info Toko --}}
            <div class="card border mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ $product->store->logo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($product->store->name).'&background=2E75B6&color=fff' }}"
                             class="rounded-circle" width="44" height="44" alt="Logo toko">
                        <div>
                            <div class="fw-semibold" style="font-size:.9rem">{{ $product->store->name }}</div>
                            <div class="text-muted" style="font-size:.78rem">
                                <i class="bi bi-geo-alt me-1"></i>{{ $product->store->city ?? 'Indonesia' }}
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 text-center mb-2">
                        <div class="col-6">
                            <div class="fw-bold" style="font-size:.95rem">{{ number_format($product->store->rating_avg,1) }}</div>
                            <div class="text-muted" style="font-size:.72rem">Rating Toko</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold" style="font-size:.95rem">{{ number_format($product->store->total_sales) }}</div>
                            <div class="text-muted" style="font-size:.72rem">Produk Terjual</div>
                        </div>
                    </div>
                    <a href="{{ route('stores.show', $product->store->slug) }}"
                       class="btn btn-outline-secondary btn-sm w-100">Kunjungi Toko</a>
                </div>
            </div>

            {{-- Info Pengiriman --}}
            <div class="card border">
                <div class="card-body">
                    <div class="small fw-semibold mb-2"><i class="bi bi-truck me-1"></i>Pengiriman</div>
                    <div class="small text-muted mb-2">Dikirim dari <strong>{{ $product->store->city ?? 'Seller' }}</strong></div>
                    <div class="d-flex align-items-center gap-2 small mb-1">
                        <i class="bi bi-check-circle text-success"></i>JNE, J&T, SiCepat, AnterAja
                    </div>
                    <div class="d-flex align-items-center gap-2 small">
                        <i class="bi bi-shield-check text-success"></i>Proteksi pembeli aktif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== DESKRIPSI PRODUK ===== --}}
    <div class="bg-white rounded-3 border p-4 mt-4" x-data="{ expanded: false }">
        <h5 class="fw-bold mb-3">Deskripsi Produk</h5>
        <div class="product-desc" :class="expanded ? '' : 'desc-collapsed'"
             style="overflow:hidden">
            {!! nl2br(e($product->description)) !!}
        </div>
        <button class="btn btn-link p-0 small mt-2" @click="expanded = !expanded">
            <span x-text="expanded ? 'Tampilkan lebih sedikit ▲' : 'Selengkapnya ▼'">Selengkapnya ▼</span>
        </button>
    </div>

    {{-- ===== ULASAN ===== --}}
    <div class="bg-white rounded-3 border p-4 mt-3" id="reviews">
        <h5 class="fw-bold mb-3">Ulasan Pembeli</h5>

        {{-- Ringkasan rating --}}
        <div class="row g-3 align-items-center mb-4">
            <div class="col-md-2 text-center">
                <div class="display-4 fw-bold" style="color:var(--nm-orange)">{{ number_format($product->rating_avg, 1) }}</div>
                <div class="stars fs-5 my-1">
                    @for($i=1;$i<=5;$i++)
                    <i class="bi {{ $i <= round($product->rating_avg) ? 'bi-star-fill' : 'bi-star' }}"></i>
                    @endfor
                </div>
                <div class="small text-muted">{{ $product->total_reviews }} ulasan</div>
            </div>
            <div class="col-md-4">
                @foreach([5,4,3,2,1] as $star)
                @php $count = $reviews->where('rating_product', $star)->count(); $pct = $product->total_reviews ? round($count/$product->total_reviews*100) : 0; @endphp
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="small" style="width:12px">{{ $star }}</span>
                    <i class="bi bi-star-fill" style="color:#f5a623;font-size:.75rem"></i>
                    <div class="progress flex-grow-1" style="height:8px">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:var(--nm-orange)"></div>
                    </div>
                    <span class="small text-muted" style="width:28px">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- List ulasan --}}
        @forelse($reviews as $review)
        <div class="border-top pt-3 mb-3">
            <div class="d-flex align-items-center gap-2 mb-1">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($review->buyer->name) }}&size=32&background=eee&color=555"
                     class="rounded-circle" width="32" height="32" alt="Avatar">
                <div>
                    <div class="small fw-semibold">{{ $review->buyer->name }}</div>
                    <div class="stars" style="font-size:.75rem">
                        @for($i=1;$i<=5;$i++)<i class="bi {{ $i <= $review->rating_product ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor
                        <span class="text-muted ms-1" style="font-size:.72rem">
                            {{ $review->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
                @if($review->variant_info)
                <span class="badge bg-light text-muted ms-auto" style="font-size:.72rem">
                    {{ collect($review->orderItem->variant_info)->values()->implode(', ') }}
                </span>
                @endif
            </div>
            <p class="mb-1 small">{{ $review->comment }}</p>

            {{-- Foto ulasan --}}
            @if($review->images->count())
            <div class="d-flex gap-2 mb-2">
                @foreach($review->images as $img)
                <img src="{{ $img->url }}" class="rounded-2" width="64" height="64"
                     style="object-fit:cover;cursor:pointer"
                     onclick="showReviewImage('{{ $img->url }}')">
                @endforeach
            </div>
            @endif

            {{-- Balasan seller --}}
            @if($review->seller_reply)
            <div class="bg-light rounded-2 p-2 small">
                <strong><i class="bi bi-shop me-1"></i>{{ $product->store->name }}:</strong>
                {{ $review->seller_reply }}
            </div>
            @endif
        </div>
        @empty
        <p class="text-muted small">Belum ada ulasan untuk produk ini.</p>
        @endforelse

        {{ $reviews->links('pagination::bootstrap-5') }}
    </div>

    {{-- ===== PRODUK TERKAIT ===== --}}
    @if($relatedProducts->isNotEmpty())
    <div class="mt-4">
        <h5 class="fw-bold mb-3">Produk Terkait</h5>
        <div class="row g-2 row-cols-2 row-cols-sm-3 row-cols-md-5">
            @foreach($relatedProducts as $rp)
            @include('components.product-card', ['product' => $rp])
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('styles')
<style>
.desc-collapsed { max-height: 120px; }
</style>
@endpush
