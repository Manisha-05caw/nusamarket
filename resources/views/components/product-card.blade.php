{{-- components/product-card.blade.php --}}
{{-- Usage: @include('components.product-card', ['product' => $product]) --}}
<div class="col">
    <div class="card product-card h-100 position-relative"
         x-data="{ wishlisted: {{ auth()->check() && auth()->user()->wishlists()->where('product_id', $product->id)->exists() ? 'true' : 'false' }} }">

        {{-- Wishlist button --}}
        @auth
        <button class="wishlist-btn" @click.prevent="toggleWishlist()"
                x-bind:class="wishlisted ? 'text-danger' : 'text-secondary'">
            <i class="bi" :class="wishlisted ? 'bi-heart-fill' : 'bi-heart'"></i>
        </button>
        @endauth

        {{-- Badges --}}
        @if($product->discount_percent > 0)
        <span class="badge badge-sale position-absolute" style="top:8px;left:8px;font-size:.7rem">
            -{{ $product->discount_percent }}%
        </span>
        @endif
        @if($product->is_new)
        <span class="badge badge-new position-absolute" style="top:{{ $product->discount_percent > 0 ? '30' : '8' }}px;left:8px;font-size:.7rem">Baru</span>
        @endif

        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
            <img src="{{ $product->thumbnail ?? asset('img/placeholder.jpg') }}"
                 class="card-img-top" alt="{{ $product->name }}"
                 loading="lazy">
            <div class="card-body p-2">
                <p class="card-title mb-1 lh-sm" style="font-size:.875rem;
                   display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                    {{ $product->name }}
                </p>
                <div class="price">Rp {{ number_format($product->display_price, 0, ',', '.') }}</div>
                @if($product->discount_percent > 0)
                <div class="text-muted text-decoration-line-through" style="font-size:.78rem">
                    Rp {{ number_format($product->base_price, 0, ',', '.') }}
                </div>
                @endif
                <div class="d-flex align-items-center gap-1 mt-1">
                    <span class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= round($product->rating_avg) ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </span>
                    <span class="sold">{{ $product->rating_avg > 0 ? number_format($product->rating_avg, 1) : '' }}</span>
                </div>
                <div class="sold">{{ number_format($product->total_sold) }} terjual</div>
                <div class="store-name mt-1">
                    <i class="bi bi-shop" style="font-size:.72rem"></i>
                    {{ $product->store->name ?? '' }}
                    @if($product->store?->city)
                    · {{ $product->store->city }}
                    @endif
                </div>
            </div>
        </a>
    </div>
</div>

@once
@push('scripts')
<script>
function toggleWishlist(productId) {
    fetch(`/wishlist/toggle/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        this.wishlisted = data.wishlisted;
    });
}
</script>
@endpush
@endonce
