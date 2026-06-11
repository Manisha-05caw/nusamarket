@extends('layouts.app')
@section('title', 'Pesanan Saya')

@section('content')
<div class="container">
    <h4 class="fw-bold mb-3"><i class="bi bi-box-seam me-2"></i>Pesanan Saya</h4>

    {{-- Status tabs --}}
    <ul class="nav nav-tabs mb-3">
        @foreach([
            ''           => 'Semua',
            'pending_payment' => 'Belum Bayar',
            'processing' => 'Diproses',
            'shipped'    => 'Dikirim',
            'delivered'  => 'Diterima',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
        ] as $val => $label)
        <li class="nav-item">
            <a class="nav-link {{ request('status', '') === $val ? 'active' : '' }}"
               href="{{ route('orders.index', ['status' => $val]) }}">
                {{ $label }}
            </a>
        </li>
        @endforeach
    </ul>

    @forelse($orders as $order)
    <div class="card border mb-3">
        <div class="card-header bg-white d-flex align-items-center justify-content-between py-2">
            <div class="d-flex align-items-center gap-2">
                <span class="small text-muted">{{ $order->created_at->format('d M Y') }}</span>
                <span class="badge
                    @if($order->status === 'completed') bg-success
                    @elseif($order->status === 'cancelled') bg-danger
                    @elseif($order->status === 'shipped') bg-info
                    @elseif($order->status === 'pending_payment') bg-warning text-dark
                    @else bg-secondary @endif"
                    style="font-size:.72rem">
                    {{ $order->status_label }}
                </span>
            </div>
            <span class="small text-muted">{{ Str::upper(substr($order->id, 0, 8)) }}</span>
        </div>
        <div class="card-body">
            @foreach($order->items->take(2) as $item)
            <div class="d-flex gap-3 mb-2">
                <img src="{{ $item->product->thumbnail ?? asset('img/placeholder.jpg') }}"
                     class="rounded" width="56" height="56" style="object-fit:cover">
                <div class="flex-grow-1">
                    <div class="small fw-semibold">{{ $item->product_name }}</div>
                    <div class="text-muted" style="font-size:.78rem">x{{ $item->quantity }}</div>
                    <div class="small">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                </div>
            </div>
            @endforeach
            @if($order->items->count() > 2)
            <div class="small text-muted">+ {{ $order->items->count() - 2 }} produk lainnya</div>
            @endif
        </div>
        <div class="card-footer bg-white d-flex align-items-center justify-content-between py-2">
            <div class="small">
                Total: <strong class="price">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
            </div>
            <div class="d-flex gap-2">
                @if($order->status === 'pending_payment')
                <a href="{{ route('checkout.payment', $order->id) }}" class="btn btn-orange btn-sm">Bayar Sekarang</a>
                @endif
                @if($order->status === 'delivered')
                <form action="{{ route('orders.confirm-received', $order->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-success btn-sm">Konfirmasi Terima</button>
                </form>
                @endif
                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary btn-sm">Detail</a>
                @if($order->canBeCancelled())
                <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                      onsubmit="return confirm('Batalkan pesanan ini?')">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm">Batalkan</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="bi bi-box" style="font-size:3rem;color:#ccc"></i>
        <p class="text-muted mt-2">Belum ada pesanan.</p>
        <a href="{{ route('home') }}" class="btn btn-primary-nusa btn-sm">Mulai Belanja</a>
    </div>
    @endforelse

    {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
</div>
@endsection
