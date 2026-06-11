@extends('layouts.app')
@section('title', 'Pesanan Berhasil')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center py-5">
            <div class="mb-4" style="font-size:4rem;color:var(--nm-accent)">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h4 class="fw-bold mb-2">Pesanan Berhasil Dibuat!</h4>
            <p class="text-muted mb-1">Terima kasih sudah berbelanja di NusaMarket.</p>
            <p class="text-muted small mb-4">
                No. Pesanan: <strong>{{ Str::upper(substr($order->id, 0, 8)) }}</strong>
            </p>

            <div class="card border mb-4 text-start">
                <div class="card-body">
                    <div class="small fw-semibold mb-2">Detail Pesanan</div>
                    @foreach($order->items as $item)
                    <div class="d-flex gap-2 mb-2">
                        <img src="{{ $item->product->thumbnail ?? asset('img/placeholder.jpg') }}"
                             class="rounded" width="48" height="48" style="object-fit:cover">
                        <div>
                            <div class="small fw-semibold">{{ $item->product_name }}</div>
                            <div class="text-muted" style="font-size:.75rem">x{{ $item->quantity }} · Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @endforeach
                    <hr class="my-2">
                    <div class="d-flex justify-content-between small fw-bold">
                        <span>Total Dibayar</span>
                        <span class="price">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-center">
                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary-nusa">
                    <i class="bi bi-box-seam me-1"></i>Lacak Pesanan
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    Lanjut Belanja
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


{{-- ============================================================
     pages/orders/index.blade.php
     ============================================================ --}}
