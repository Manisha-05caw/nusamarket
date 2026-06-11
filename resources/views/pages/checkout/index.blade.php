{{-- ============================================================
     pages/checkout/index.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="container">
    <h4 class="fw-bold mb-3"><i class="bi bi-bag-check me-2"></i>Checkout</h4>

    <form action="{{ route('checkout.process') }}" method="POST" x-data="{
        selectedAddress: '{{ $defaultAddress?->id }}',
        paymentMethod: '',
        courier: '',
        courierService: '',
        shippingCost: 0,
        subtotal: {{ $cart->total }},
        platformFee: Math.round({{ $cart->total }} * 0.02),
        get total() { return this.subtotal + this.shippingCost + this.platformFee; },
        formatRp(v) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(v); }
    }">
        @csrf
        <div class="row g-3">

            {{-- KIRI --}}
            <div class="col-lg-8">

                {{-- 1. Alamat Pengiriman --}}
                <div class="card border mb-3">
                    <div class="card-header bg-white fw-semibold py-2">
                        <i class="bi bi-geo-alt me-2" style="color:var(--nm-accent)"></i>Alamat Pengiriman
                    </div>
                    <div class="card-body">
                        @forelse($addresses as $addr)
                        <label class="d-flex align-items-start gap-3 p-3 rounded-3 border mb-2 cursor-pointer"
                               :class="selectedAddress === '{{ $addr->id }}' ? 'border-primary bg-light' : ''"
                               style="cursor:pointer">
                            <input type="radio" name="address_id" value="{{ $addr->id }}"
                                   x-model="selectedAddress" class="form-check-input mt-1 flex-shrink-0">
                            <div>
                                <div class="fw-semibold small">{{ $addr->recipient }}
                                    <span class="badge bg-secondary ms-1" style="font-size:.7rem">{{ $addr->label }}</span>
                                    @if($addr->is_default)<span class="badge" style="background:var(--nm-accent-lt);color:var(--nm-primary);font-size:.7rem">Utama</span>@endif
                                </div>
                                <div class="small text-muted">{{ $addr->phone }}</div>
                                <div class="small text-muted">{{ $addr->full_address }}</div>
                            </div>
                        </label>
                        @empty
                        <p class="text-muted small">Belum ada alamat. <a href="{{ route('addresses.index') }}">Tambah alamat</a></p>
                        @endforelse
                        <a href="{{ route('addresses.index') }}" class="btn btn-outline-secondary btn-sm mt-1">
                            <i class="bi bi-plus me-1"></i>Tambah Alamat Baru
                        </a>
                    </div>
                </div>

                {{-- 2. Pilih Kurir --}}
                <div class="card border mb-3">
                    <div class="card-header bg-white fw-semibold py-2">
                        <i class="bi bi-truck me-2" style="color:var(--nm-accent)"></i>Pengiriman
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            @foreach([
                                ['value'=>'jne',    'label'=>'JNE'],
                                ['value'=>'jnt',    'label'=>'J&T Express'],
                                ['value'=>'sicepat','label'=>'SiCepat'],
                                ['value'=>'anteraja','label'=>'AnterAja'],
                            ] as $c)
                            <div class="col-6 col-md-3">
                                <label class="d-block border rounded-3 p-2 text-center cursor-pointer"
                                       :class="courier === '{{ $c['value'] }}' ? 'border-primary bg-light fw-semibold' : ''"
                                       style="cursor:pointer">
                                    <input type="radio" name="courier" value="{{ $c['value'] }}"
                                           x-model="courier" class="d-none">
                                    <div class="small">{{ $c['label'] }}</div>
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <div x-show="courier" x-cloak>
                            <div class="small fw-semibold mb-2">Layanan:</div>
                            <div class="row g-2">
                                @foreach([
                                    ['value'=>'REG','label'=>'Reguler','days'=>'2-3 hari','price'=>15000],
                                    ['value'=>'YES','label'=>'Yakin Esok Sampai','days'=>'1 hari','price'=>25000],
                                    ['value'=>'OKE','label'=>'Ekonomi','days'=>'3-5 hari','price'=>10000],
                                ] as $svc)
                                <div class="col-12">
                                    <label class="d-flex align-items-center justify-content-between border rounded-3 px-3 py-2 cursor-pointer"
                                           :class="courierService === '{{ $svc['value'] }}' ? 'border-primary bg-light' : ''"
                                           style="cursor:pointer"
                                           @click="courierService = '{{ $svc['value'] }}'; shippingCost = {{ $svc['price'] }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="radio" name="courier_service" value="{{ $svc['value'] }}"
                                                   x-model="courierService" class="form-check-input">
                                            <div>
                                                <div class="small fw-semibold">{{ $svc['label'] }}</div>
                                                <div class="text-muted" style="font-size:.75rem">Estimasi {{ $svc['days'] }}</div>
                                            </div>
                                        </div>
                                        <div class="small fw-semibold">Rp {{ number_format($svc['price'], 0, ',', '.') }}</div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Metode Pembayaran --}}
                <div class="card border mb-3">
                    <div class="card-header bg-white fw-semibold py-2">
                        <i class="bi bi-credit-card me-2" style="color:var(--nm-accent)"></i>Metode Pembayaran
                    </div>
                    <div class="card-body">
                        @php
                        $methods = [
                            ['group' => 'Transfer Bank', 'items' => [
                                ['value'=>'bank_transfer','label'=>'Transfer Bank (BCA, Mandiri, BRI, BNI)'],
                            ]],
                            ['group' => 'Dompet Digital', 'items' => [
                                ['value'=>'gopay', 'label'=>'GoPay'],
                                ['value'=>'ovo',   'label'=>'OVO'],
                                ['value'=>'dana',  'label'=>'DANA'],
                                ['value'=>'qris',  'label'=>'QRIS'],
                            ]],
                            ['group' => 'Kartu', 'items' => [
                                ['value'=>'credit_card','label'=>'Kartu Kredit / Debit'],
                            ]],
                        ];
                        @endphp
                        @foreach($methods as $group)
                        <div class="small fw-semibold text-muted mb-2 mt-3">{{ $group['group'] }}</div>
                        @foreach($group['items'] as $m)
                        <label class="d-flex align-items-center gap-3 border rounded-3 px-3 py-2 mb-2 cursor-pointer"
                               :class="paymentMethod === '{{ $m['value'] }}' ? 'border-primary bg-light' : ''"
                               style="cursor:pointer">
                            <input type="radio" name="payment_method" value="{{ $m['value'] }}"
                                   x-model="paymentMethod" class="form-check-input">
                            <span class="small">{{ $m['label'] }}</span>
                        </label>
                        @endforeach
                        @endforeach
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card border mb-3">
                    <div class="card-body">
                        <label class="form-label small fw-semibold">Catatan untuk Penjual (opsional)</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="2"
                                  placeholder="Contoh: Tolong dikemas dengan bubble wrap..."></textarea>
                    </div>
                </div>

            </div>

            {{-- KANAN: Ringkasan --}}
            <div class="col-lg-4">
                <div class="card border sticky-top" style="top:80px">
                    <div class="card-header bg-white fw-semibold py-2">Ringkasan Pesanan</div>
                    <div class="card-body">
                        {{-- Item list --}}
                        @foreach($cart->items as $item)
                        <div class="d-flex gap-2 mb-2">
                            <img src="{{ $item->variant->product->thumbnail ?? asset('img/placeholder.jpg') }}"
                                 class="rounded" width="40" height="40" style="object-fit:cover">
                            <div class="flex-grow-1 min-w-0">
                                <div class="small lh-sm" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ $item->variant->product->name }}
                                </div>
                                <div class="text-muted" style="font-size:.75rem">
                                    x{{ $item->quantity }}
                                    @if($item->variant->display_name) · {{ $item->variant->display_name }} @endif
                                </div>
                            </div>
                            <div class="small fw-semibold flex-shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                        @endforeach

                        <hr>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span x-text="formatRp(subtotal)">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Ongkos Kirim</span>
                            <span x-text="shippingCost > 0 ? formatRp(shippingCost) : 'Pilih kurir'">Pilih kurir</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Biaya Layanan (2%)</span>
                            <span x-text="formatRp(platformFee)">Rp {{ number_format(round($cart->total * 0.02), 0, ',', '.') }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold mb-3">
                            <span>Total Pembayaran</span>
                            <span class="price" x-text="formatRp(total)">—</span>
                        </div>

                        <button type="submit" class="btn btn-orange w-100 fw-semibold"
                                :disabled="!selectedAddress || !courier || !courierService || !paymentMethod">
                            Buat Pesanan
                        </button>
                        <div class="d-flex align-items-center gap-1 justify-content-center mt-2">
                            <i class="bi bi-shield-check text-success" style="font-size:.85rem"></i>
                            <span class="text-muted" style="font-size:.75rem">Transaksi dilindungi NusaMarket</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection


{{-- ============================================================
     pages/checkout/success.blade.php
     ============================================================ --}}
