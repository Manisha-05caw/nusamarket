{{-- pages/checkout/payment.blade.php --}}
@extends('layouts.app')
@section('title', 'Pembayaran')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            <div class="text-center mb-4">
                <h4 class="fw-bold">Selesaikan Pembayaran</h4>
                <p class="text-muted small">Pesanan #{{ strtoupper(substr($order->id, 0, 8)) }}</p>
            </div>

            {{-- Timer --}}
            <div class="alert alert-warning py-2 text-center"
                 x-data="{
                    deadline: new Date('{{ $order->payment->expired_at?->toIso8601String() }}').getTime(),
                    h:'23', m:'59', s:'59',
                    init() {
                        const t = setInterval(() => {
                            const diff = this.deadline - Date.now();
                            if(diff <= 0) { clearInterval(t); window.location.reload(); return; }
                            this.h = String(Math.floor(diff/3600000)).padStart(2,'0');
                            this.m = String(Math.floor((diff%3600000)/60000)).padStart(2,'0');
                            this.s = String(Math.floor((diff%60000)/1000)).padStart(2,'0');
                        }, 1000);
                    }
                 }">
                <i class="bi bi-clock me-1"></i>
                Selesaikan dalam <strong x-text="h+':'+m+':'+s">23:59:59</strong>
            </div>

            {{-- Ringkasan pesanan --}}
            <div class="card border mb-3">
                <div class="card-header bg-white fw-semibold py-2 small">Ringkasan Pesanan</div>
                <div class="card-body pb-2">
                    @foreach($order->items as $item)
                    <div class="d-flex gap-2 mb-2">
                        <img src="{{ $item->product->thumbnail ?? asset('img/placeholder.jpg') }}"
                             class="rounded" width="44" height="44" style="object-fit:cover">
                        <div class="flex-grow-1 min-w-0">
                            <div class="small fw-semibold text-truncate">{{ $item->product_name }}</div>
                            <div class="text-muted" style="font-size:.75rem">x{{ $item->quantity }}</div>
                        </div>
                        <div class="small fw-semibold flex-shrink-0">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                    <hr class="my-2">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Ongkos Kirim ({{ strtoupper($order->courier) }})</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Biaya Layanan</span>
                        <span>Rp {{ number_format($order->platform_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total Pembayaran</span>
                        <span class="price" style="font-size:1.05rem">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Alamat pengiriman --}}
            <div class="card border mb-3">
                <div class="card-body py-2 small">
                    <div class="d-flex gap-2">
                        <i class="bi bi-geo-alt text-primary mt-1"></i>
                        <div>
                            <div class="fw-semibold">{{ $order->shipping_address['recipient'] }}</div>
                            <div class="text-muted">{{ $order->shipping_address['phone'] }}</div>
                            <div class="text-muted">
                                {{ $order->shipping_address['address_line'] }},
                                {{ $order->shipping_address['city'] }},
                                {{ $order->shipping_address['province'] }}
                                {{ $order->shipping_address['postal_code'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol bayar via Midtrans Snap --}}
            @if($snapToken)
            <div class="d-grid gap-2">
                <button id="pay-button" class="btn btn-orange fw-semibold py-3"
                        onclick="payNow()">
                    <i class="bi bi-lock me-2"></i>Bayar Sekarang — Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </button>
                <a href="{{ route('orders.cancel', $order->id) }}"
                   class="btn btn-outline-secondary btn-sm"
                   onclick="return confirm('Batalkan pesanan ini?')">
                    Batalkan Pesanan
                </a>
            </div>

            <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                <img src="https://payment.midtrans.com/payment-assets/img/midtrans.png"
                     height="20" alt="Midtrans">
                <span class="text-muted" style="font-size:.75rem">Pembayaran aman diproses oleh Midtrans</span>
            </div>
            @else
            <div class="alert alert-danger text-center">
                Gagal memuat halaman pembayaran. <a href="{{ route('orders.show', $order->id) }}">Coba lagi</a>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ $clientKey }}"></script>
{{-- Ganti ke https://app.midtrans.com/snap/snap.js untuk production --}}

<script>
function payNow() {
    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            window.location.href = '{{ route('checkout.success', $order->id) }}';
        },
        onPending: function(result) {
            alert('Pembayaran pending. Silakan selesaikan pembayaran kamu.');
            window.location.href = '{{ route('orders.show', $order->id) }}';
        },
        onError: function(result) {
            alert('Pembayaran gagal. Silakan coba lagi.');
        },
        onClose: function() {
            // User tutup popup tanpa bayar — tidak apa-apa
        }
    });
}
</script>
@endpush
