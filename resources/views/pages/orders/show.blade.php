{{-- pages/orders/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')
<div class="container">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pesanan Saya</a></li>
            <li class="breadcrumb-item active">#{{ strtoupper(substr($order->id, 0, 8)) }}</li>
        </ol>
    </nav>

    <div class="row g-3">
        <div class="col-lg-8">

            {{-- ===== STATUS TRACKER ===== --}}
            <div class="card border mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0">Status Pesanan</h6>
                        <span class="badge
                            @if($order->status==='completed')        bg-success
                            @elseif($order->status==='cancelled')    bg-danger
                            @elseif($order->status==='shipped')      bg-info
                            @elseif($order->status==='pending_payment') bg-warning text-dark
                            @else bg-secondary @endif">
                            {{ $order->status_label }}
                        </span>
                    </div>

                    {{-- Progress steps --}}
                    @php
                    $steps = [
                        ['key' => 'pending_payment', 'label' => 'Menunggu\nPembayaran', 'icon' => 'bi-clock'],
                        ['key' => 'paid',            'label' => 'Pembayaran\nDikonfirmasi', 'icon' => 'bi-check-circle'],
                        ['key' => 'processing',      'label' => 'Diproses\nPenjual', 'icon' => 'bi-gear'],
                        ['key' => 'shipped',         'label' => 'Dalam\nPengiriman', 'icon' => 'bi-truck'],
                        ['key' => 'delivered',       'label' => 'Sudah\nDiterima', 'icon' => 'bi-house-check'],
                        ['key' => 'completed',       'label' => 'Pesanan\nSelesai', 'icon' => 'bi-patch-check'],
                    ];
                    $statusOrder = ['pending_payment','paid','processing','shipped','delivered','completed'];
                    $currentIdx  = array_search($order->status, $statusOrder);
                    @endphp

                    @if(!in_array($order->status, ['cancelled','refunded']))
                    <div class="d-flex align-items-start justify-content-between position-relative" style="overflow-x:auto">
                        {{-- Progress line --}}
                        <div class="position-absolute" style="top:18px;left:32px;right:32px;height:2px;background:#e5e7eb;z-index:0">
                            <div style="height:100%;background:var(--nm-accent);transition:width .5s;
                                width:{{ $currentIdx >= 0 ? min(100, ($currentIdx / (count($steps)-1)) * 100) : 0 }}%"></div>
                        </div>

                        @foreach($steps as $i => $step)
                        @php $done = $currentIdx !== false && $i <= $currentIdx; @endphp
                        <div class="d-flex flex-column align-items-center" style="min-width:80px;position:relative;z-index:1">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-1"
                                 style="width:36px;height:36px;
                                 background:{{ $done ? 'var(--nm-accent)' : '#e5e7eb' }};
                                 color:{{ $done ? '#fff' : '#9ca3af' }}">
                                <i class="bi {{ $step['icon'] }}" style="font-size:.9rem"></i>
                            </div>
                            <div class="text-center" style="font-size:.68rem;color:{{ $done ? 'var(--nm-primary)' : '#9ca3af' }};white-space:pre-line;line-height:1.3">{{ $step['label'] }}</div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-2">
                        <i class="bi bi-x-circle text-danger" style="font-size:2rem"></i>
                        <p class="text-danger fw-semibold mt-1 mb-0">
                            {{ $order->status === 'refunded' ? 'Pesanan Dikembalikan' : 'Pesanan Dibatalkan' }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ===== TRACKING PENGIRIMAN ===== --}}
            @if($order->tracking_number)
            <div class="card border mb-3">
                <div class="card-header bg-white fw-semibold py-2">
                    <i class="bi bi-truck me-2" style="color:var(--nm-accent)"></i>Info Pengiriman
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="text-muted small">Kurir</div>
                            <div class="fw-semibold">{{ strtoupper($order->courier) }} {{ strtoupper($order->courier_service) }}</div>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-muted small">No. Resi</div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold font-monospace">{{ $order->tracking_number }}</span>
                                <button class="btn btn-outline-secondary btn-sm py-0 px-2"
                                        onclick="copyResi('{{ $order->tracking_number }}')" id="copyBtn">
                                    <i class="bi bi-copy"></i>
                                </button>
                                <a href="{{ $this->getTrackingUrl($order->courier, $order->tracking_number) }}"
                                   target="_blank" class="btn btn-primary-nusa btn-sm py-0 px-2">
                                    Lacak
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Timeline tracking (placeholder — bisa diisi data dari API ekspedisi) --}}
                    <div class="mt-3 border-top pt-3">
                        <div class="small fw-semibold mb-2 text-muted">Riwayat Pengiriman</div>
                        @if(isset($trackingHistory) && count($trackingHistory))
                        <div class="position-relative ps-3" style="border-left:2px solid #e5e7eb">
                            @foreach($trackingHistory as $event)
                            <div class="mb-3 position-relative">
                                <div class="position-absolute rounded-circle"
                                     style="width:10px;height:10px;background:var(--nm-accent);left:-6px;top:4px"></div>
                                <div class="small fw-semibold">{{ $event['description'] }}</div>
                                <div class="text-muted" style="font-size:.73rem">
                                    {{ $event['location'] }} · {{ \Carbon\Carbon::parse($event['datetime'])->format('d M Y, H:i') }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted small mb-0">Data tracking akan tersedia setelah paket dikirim.</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- ===== ITEM PESANAN ===== --}}
            <div class="card border mb-3">
                <div class="card-header bg-white fw-semibold py-2">
                    <i class="bi bi-box-seam me-2" style="color:var(--nm-accent)"></i>Produk yang Dipesan
                </div>
                @foreach($order->items as $item)
                <div class="card-body border-bottom">
                    <div class="d-flex gap-3 align-items-start">
                        <img src="{{ $item->product->thumbnail ?? asset('img/placeholder.jpg') }}"
                             class="rounded-2 flex-shrink-0" width="64" height="64" style="object-fit:cover">
                        <div class="flex-grow-1">
                            <a href="{{ route('products.show', $item->product->slug) }}"
                               class="fw-semibold text-decoration-none text-dark d-block mb-1">
                                {{ $item->product_name }}
                            </a>
                            @if(collect($item->variant_info)->filter()->isNotEmpty())
                            <div class="small text-muted mb-1">
                                {{ collect($item->variant_info)->map(fn($v,$k) => ucfirst($k).': '.$v)->filter()->implode(' · ') }}
                            </div>
                            @endif
                            <div class="small">
                                <span class="text-muted">x{{ $item->quantity }}</span>
                                <span class="mx-1 text-muted">·</span>
                                <span class="fw-semibold">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold" style="color:var(--nm-orange)">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                            {{-- Tombol ulasan --}}
                            @if($order->status === 'completed')
                                @if($item->review)
                                <div class="d-flex align-items-center gap-1 justify-content-end mt-1">
                                    @for($i=1;$i<=5;$i++)
                                    <i class="bi {{ $i <= $item->review->rating_product ? 'bi-star-fill' : 'bi-star' }}"
                                       style="color:#f5a623;font-size:.75rem"></i>
                                    @endfor
                                    <span class="text-muted" style="font-size:.73rem">Sudah diulas</span>
                                </div>
                                @else
                                <a href="{{ route('reviews.create', [$order->id, $item->id]) }}"
                                   class="btn btn-outline-warning btn-sm mt-1">
                                    <i class="bi bi-star me-1"></i>Beri Ulasan
                                </a>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Chat dengan seller produk ini --}}
                    <div class="mt-2">
                        <a href="{{ route('chat.store', $item->store->id) }}?product={{ $item->product_id }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-chat-dots me-1"></i>Chat Penjual
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ===== CTA UTAMA ===== --}}
            <div class="d-flex gap-2 flex-wrap">
                @if($order->status === 'pending_payment')
                <a href="{{ route('checkout.payment', $order->id) }}" class="btn btn-orange fw-semibold">
                    <i class="bi bi-lock me-1"></i>Bayar Sekarang
                </a>
                @endif

                @if($order->status === 'delivered')
                <form action="{{ route('orders.confirm-received', $order->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-success fw-semibold">
                        <i class="bi bi-check-circle me-1"></i>Konfirmasi Pesanan Diterima
                    </button>
                </form>
                @endif

                @if($order->canBeCancelled())
                <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                      onsubmit="return confirm('Batalkan pesanan ini?')">
                    @csrf
                    <button class="btn btn-outline-danger">Batalkan Pesanan</button>
                </form>
                @endif

                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    ← Kembali ke Pesanan
                </a>
            </div>

        </div>

        {{-- ===== KANAN: RINGKASAN & INFO ===== --}}
        <div class="col-lg-4">

            {{-- Info pembayaran --}}
            <div class="card border mb-3">
                <div class="card-header bg-white fw-semibold py-2">
                    <i class="bi bi-credit-card me-2" style="color:var(--nm-accent)"></i>Detail Pembayaran
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Metode</span>
                        <span class="fw-semibold text-uppercase">{{ str_replace('_', ' ', $order->payment?->method ?? '—') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Status</span>
                        <span class="badge {{ $order->payment?->isPaid() ? 'bg-success' : 'bg-warning text-dark' }}" style="font-size:.7rem">
                            {{ $order->payment?->isPaid() ? 'Lunas' : 'Menunggu' }}
                        </span>
                    </div>
                    @if($order->payment?->paid_at)
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Dibayar</span>
                        <span>{{ $order->payment->paid_at->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                    <hr class="my-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Ongkir</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Biaya Layanan</span>
                        <span>Rp {{ number_format($order->platform_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span class="price">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Info pengiriman --}}
            <div class="card border mb-3">
                <div class="card-header bg-white fw-semibold py-2">
                    <i class="bi bi-geo-alt me-2" style="color:var(--nm-accent)"></i>Alamat Pengiriman
                </div>
                <div class="card-body small">
                    <div class="fw-semibold">{{ $order->shipping_address['recipient'] }}</div>
                    <div class="text-muted">{{ $order->shipping_address['phone'] }}</div>
                    <div class="text-muted mt-1">
                        {{ $order->shipping_address['address_line'] }},<br>
                        {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['province'] }}<br>
                        {{ $order->shipping_address['postal_code'] }}
                    </div>
                    @if($order->courier)
                    <hr class="my-2">
                    <div class="text-muted">
                        <i class="bi bi-truck me-1"></i>
                        {{ strtoupper($order->courier) }} {{ strtoupper($order->courier_service ?? '') }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Info pesanan --}}
            <div class="card border">
                <div class="card-body small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">No. Pesanan</span>
                        <span class="fw-semibold font-monospace">{{ strtoupper(substr($order->id, 0, 8)) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tanggal Pesan</span>
                        <span>{{ $order->created_at->format('d M Y') }}</span>
                    </div>
                    @if($order->paid_at)
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tanggal Bayar</span>
                        <span>{{ $order->paid_at->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($order->completed_at)
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Tanggal Selesai</span>
                        <span>{{ $order->completed_at->format('d M Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyResi(resi) {
    navigator.clipboard.writeText(resi).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.innerHTML = '<i class="bi bi-check"></i>';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        setTimeout(() => {
            btn.innerHTML = '<i class="bi bi-copy"></i>';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    });
}
</script>
@endpush
