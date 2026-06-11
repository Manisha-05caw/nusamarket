{{-- pages/seller/orders/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')
<div class="container">

    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('seller.orders.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0">Detail Pesanan</h4>
        <span class="badge
            @if($item->item_status==='delivered')   bg-success
            @elseif($item->item_status==='cancelled') bg-danger
            @elseif($item->item_status==='shipped')   bg-info
            @elseif($item->item_status==='pending')   bg-warning text-dark
            @else bg-secondary @endif ms-2">
            {{ ucfirst($item->item_status) }}
        </span>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">

            {{-- Produk --}}
            <div class="card border mb-3">
                <div class="card-header bg-white fw-semibold py-2">Produk Dipesan</div>
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <img src="{{ $item->product->thumbnail ?? asset('img/placeholder.jpg') }}"
                             class="rounded-2" width="72" height="72" style="object-fit:cover">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $item->product_name }}</div>
                            @if(collect($item->variant_info)->filter()->isNotEmpty())
                            <div class="small text-muted">
                                {{ collect($item->variant_info)->map(fn($v,$k) => ucfirst($k).': '.$v)->filter()->implode(' · ') }}
                            </div>
                            @endif
                            <div class="small mt-1">
                                <span class="text-muted">Jumlah:</span>
                                <strong>{{ $item->quantity }} pcs</strong>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small">Nilai</div>
                            <div class="fw-bold" style="color:var(--nm-orange)">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aksi sesuai status --}}
            @if($item->item_status === 'pending')
            <div class="card border mb-3 border-warning">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-circle text-warning me-2"></i>Konfirmasi Pesanan</h6>
                    <p class="small text-muted mb-3">Kamu punya waktu 2×24 jam untuk memproses pesanan ini. Segera konfirmasi dan siapkan barang.</p>
                    <form action="{{ route('seller.orders.process', $item->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-primary-nusa fw-semibold">
                            <i class="bi bi-check-circle me-1"></i>Proses Pesanan
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @if($item->item_status === 'processing')
            <div class="card border mb-3 border-info" x-data="{ open: false }">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class="bi bi-truck text-info me-2"></i>Input Nomor Resi</h6>
                    <p class="small text-muted mb-3">Setelah paket dikirim, masukkan nomor resi pengiriman agar pembeli bisa melacak.</p>
                    <form action="{{ route('seller.orders.ship', $item->id) }}" method="POST">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col">
                                <label class="form-label small fw-semibold">Nomor Resi</label>
                                <input type="text" name="tracking_number" class="form-control"
                                       placeholder="Contoh: JNE1234567890" required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-info fw-semibold text-white">
                                    <i class="bi bi-send me-1"></i>Tandai Dikirim
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            @if($item->item_status === 'shipped')
            <div class="alert alert-info d-flex align-items-center gap-2">
                <i class="bi bi-truck fs-5"></i>
                <div>
                    <strong>Paket sedang dalam pengiriman.</strong>
                    Nomor resi: <code>{{ $item->order->tracking_number }}</code>
                </div>
            </div>
            @endif

            @if($item->item_status === 'delivered')
            <div class="alert alert-success d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div><strong>Pesanan sudah diterima pembeli.</strong> Dana akan masuk ke saldo kamu.</div>
            </div>
            @endif

            {{-- Ulasan --}}
            @if($item->review)
            <div class="card border">
                <div class="card-header bg-white fw-semibold py-2">
                    <i class="bi bi-star me-2" style="color:#f5a623"></i>Ulasan Pembeli
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($item->order->buyer->name) }}&size=32&background=eee&color=555"
                             class="rounded-circle" width="32" height="32">
                        <div>
                            <div class="fw-semibold small">{{ $item->order->buyer->name }}</div>
                            <div style="color:#f5a623;font-size:.8rem">
                                @for($i=1;$i<=5;$i++)
                                <i class="bi {{ $i <= $item->review->rating_product ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                                <span class="text-muted ms-1" style="font-size:.72rem">
                                    {{ $item->review->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <p class="small mb-3">{{ $item->review->comment }}</p>

                    @if(!$item->review->seller_reply)
                    <form action="{{ route('reviews.reply', $item->review->id) }}" method="POST">
                        @csrf
                        <div class="d-flex gap-2">
                            <textarea name="reply" class="form-control form-control-sm"
                                      rows="2" placeholder="Tulis balasan ulasan..." required></textarea>
                            <button class="btn btn-primary-nusa btn-sm align-self-end">Balas</button>
                        </div>
                    </form>
                    @else
                    <div class="bg-light rounded-2 p-2 small">
                        <strong><i class="bi bi-shop me-1"></i>Balasan kamu:</strong>
                        {{ $item->review->seller_reply }}
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- Kanan: Info Pembeli & Pengiriman --}}
        <div class="col-lg-4">

            <div class="card border mb-3">
                <div class="card-header bg-white fw-semibold py-2">
                    <i class="bi bi-person me-2" style="color:var(--nm-accent)"></i>Info Pembeli
                </div>
                <div class="card-body small">
                    <div class="fw-semibold">{{ $item->order->buyer->name }}</div>
                    <div class="text-muted">{{ $item->order->buyer->email }}</div>
                    <a href="{{ route('chat.store', auth()->user()->stores->first()->id) }}?buyer={{ $item->order->buyer_id }}"
                       class="btn btn-outline-secondary btn-sm mt-2 w-100">
                        <i class="bi bi-chat-dots me-1"></i>Chat Pembeli
                    </a>
                </div>
            </div>

            <div class="card border mb-3">
                <div class="card-header bg-white fw-semibold py-2">
                    <i class="bi bi-geo-alt me-2" style="color:var(--nm-accent)"></i>Alamat Pengiriman
                </div>
                <div class="card-body small">
                    @php $addr = $item->order->shipping_address; @endphp
                    <div class="fw-semibold">{{ $addr['recipient'] }}</div>
                    <div class="text-muted">{{ $addr['phone'] }}</div>
                    <div class="text-muted mt-1">
                        {{ $addr['address_line'] }},<br>
                        {{ $addr['city'] }}, {{ $addr['province'] }}<br>
                        {{ $addr['postal_code'] }}
                    </div>
                </div>
            </div>

            <div class="card border">
                <div class="card-body small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">No. Pesanan</span>
                        <span class="font-monospace fw-semibold">{{ strtoupper(substr($item->order_id, 0, 8)) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Kurir</span>
                        <span>{{ strtoupper($item->order->courier ?? '—') }}</span>
                    </div>
                    @if($item->order->tracking_number)
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Resi</span>
                        <code>{{ $item->order->tracking_number }}</code>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Tanggal</span>
                        <span>{{ $item->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
