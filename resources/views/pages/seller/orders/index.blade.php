@extends('layouts.app')
@section('title', 'Pesanan Masuk')
@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0">Pesanan Masuk</h4>
    </div>
    <ul class="nav nav-tabs mb-3">
        @foreach(['pending'=>'Pending','processing'=>'Diproses','shipped'=>'Dikirim','delivered'=>'Selesai','all'=>'Semua'] as $val=>$label)
        <li class="nav-item">
            <a class="nav-link {{ request('status','pending')===$val?'active':'' }}"
               href="{{ route('seller.orders.index',['status'=>$val]) }}">{{ $label }}</a>
        </li>
        @endforeach
    </ul>
    <div class="card border">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr><th>Produk</th><th>Pembeli</th><th>Nilai</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold" style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $item->product_name }}</div>
                            <div class="text-muted" style="font-size:.75rem">x{{ $item->quantity }}</div>
                        </td>
                        <td>{{ $item->order->buyer->name }}</td>
                        <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                        <td>
                            <span class="badge @if($item->item_status==='delivered') bg-success @elseif($item->item_status==='pending') bg-warning text-dark @elseif($item->item_status==='shipped') bg-info @else bg-secondary @endif" style="font-size:.7rem">
                                {{ ucfirst($item->item_status) }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $item->created_at->format('d M Y') }}</td>
                        <td><a href="{{ route('seller.orders.show',$item->id) }}" class="btn btn-outline-secondary btn-sm py-0 px-2">Detail</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $items->withQueryString()->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
