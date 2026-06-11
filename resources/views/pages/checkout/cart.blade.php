{{-- ============================================================ --}}
{{-- pages/checkout/cart.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Keranjang Belanja')

@section('content')
<div class="container">
    <h4 class="fw-bold mb-3"><i class="bi bi-cart3 me-2"></i>Keranjang Belanja</h4>

    @if($cart->items->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-cart-x" style="font-size:4rem;color:#ccc"></i>
        <p class="text-muted mt-3">Keranjang kamu masih kosong.</p>
        <a href="{{ route('home') }}" class="btn btn-primary-nusa">Mulai Belanja</a>
    </div>
    @else
    <div class="row g-3">
        <div class="col-lg-8">
            @foreach($groupedItems as $storeId => $items)
            @php $store = $items->first()->variant->product->store @endphp
            <div class="card border mb-3">
                <div class="card-header bg-white py-2 d-flex align-items-center gap-2">
                    <i class="bi bi-shop" style="color:var(--nm-accent)"></i>
                    <a href="{{ route('stores.show', $store->slug) }}" class="fw-semibold text-decoration-none text-dark">
                        {{ $store->name }}
                    </a>
                    <span class="badge bg-light text-muted ms-1">{{ $store->city }}</span>
                </div>
                <div class="card-body p-0">
                    @foreach($items as $item)
                    <div class="d-flex gap-3 p-3 border-bottom align-items-start"
                         x-data="{ qty: {{ $item->quantity }} }">
                        <img src="{{ $item->variant->product->thumbnail ?? asset('img/placeholder.jpg') }}"
                             class="rounded-2 flex-shrink-0" width="72" height="72" style="object-fit:cover"
                             alt="{{ $item->variant->product->name }}">
                        <div class="flex-grow-1 min-w-0">
                            <a href="{{ route('products.show', $item->variant->product->slug) }}"
                               class="text-decoration-none text-dark fw-semibold small lh-sm d-block mb-1">
                                {{ $item->variant->product->name }}
                            </a>
                            @if($item->variant->display_name)
                            <span class="badge bg-light text-muted" style="font-size:.75rem">{{ $item->variant->display_name }}</span>
                            @endif
                            <div class="price mt-1">Rp {{ number_format($item->variant->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0">
                            {{-- Qty --}}
                            <div class="input-group input-group-sm" style="width:110px">
                                <button class="btn btn-outline-secondary" type="button"
                                        @click="qty = Math.max(1, qty-1)"
                                        onclick="updateCart('{{ $item->id }}', Math.max(1, {{ $item->quantity }}-1))">−</button>
                                <input type="number" class="form-control text-center" x-model="qty"
                                       min="1" max="{{ $item->variant->stock }}"
                                       @change="updateCart('{{ $item->id }}', qty)">
                                <button class="btn btn-outline-secondary" type="button"
                                        @click="qty = Math.min({{ $item->variant->stock }}, qty+1)"
                                        onclick="updateCart('{{ $item->id }}', Math.min({{ $item->variant->stock }}, {{ $item->quantity }}+1))">+</button>
                            </div>
                            {{-- Hapus --}}
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link btn-sm text-danger p-0">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Summary --}}
        <div class="col-lg-4">
            <div class="card border sticky-top" style="top:80px">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Ringkasan Belanja</h6>
                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-muted">Total Produk ({{ $cart->item_count }} item)</span>
                        <span>Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-muted">Estimasi Ongkos Kirim</span>
                        <span class="text-muted">Dihitung saat checkout</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Total</span>
                        <span class="price">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn btn-orange w-100 fw-semibold">
                        Checkout Sekarang
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 mt-2 btn-sm">
                        Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
@push('scripts')
<script>
function updateCart(itemId, qty) {
    fetch(`/cart/${itemId}`, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ quantity: qty })
    }).then(() => location.reload());
}
</script>
@endpush
