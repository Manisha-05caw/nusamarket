@extends('layouts.app')
@section('title', 'Kelola Produk')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0">Kelola Produk</h4>
        <a href="{{ route('seller.products.create') }}" class="btn btn-primary-nusa btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Produk
        </a>
    </div>

    {{-- Filter --}}
    <form class="d-flex gap-2 mb-3" method="GET">
        <input type="text" name="q" class="form-control form-control-sm" style="max-width:240px"
               placeholder="Cari produk..." value="{{ request('q') }}">
        <select name="status" class="form-select form-select-sm" style="width:auto"
                onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status')==='active'   ? 'selected':'' }}>Aktif</option>
            <option value="inactive" {{ request('status')==='inactive' ? 'selected':'' }}>Nonaktif</option>
        </select>
        <button class="btn btn-outline-secondary btn-sm">Cari</button>
    </form>

    <div class="card border">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Terjual</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $p->thumbnail ?? asset('img/placeholder.jpg') }}"
                                     class="rounded" width="40" height="40" style="object-fit:cover">
                                <div>
                                    <div class="fw-semibold" style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $p->name }}</div>
                                    <div class="text-muted" style="font-size:.73rem">{{ $p->images->count() }} foto</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ \App\Models\Product::CATEGORIES[$p->category] ?? $p->category }}</td>
                        <td>
                            Rp {{ number_format($p->display_price, 0, ',', '.') }}
                            @if($p->discount_percent > 0)
                            <span class="badge badge-sale ms-1">-{{ $p->discount_percent }}%</span>
                            @endif
                        </td>
                        <td>{{ $p->variants->sum('stock') }}</td>
                        <td>{{ number_format($p->total_sold) }}</td>
                        <td>
                            <span class="stars" style="font-size:.8rem">★</span>
                            {{ $p->rating_avg > 0 ? number_format($p->rating_avg, 1) : '—' }}
                        </td>
                        <td>
                            <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}" style="font-size:.72rem">
                                {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('seller.products.edit', $p->id) }}"
                                   class="btn btn-outline-secondary btn-sm py-0 px-2">Edit</a>
                                <form action="{{ route('seller.products.toggle', $p->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm py-0 px-2 {{ $p->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                        {{ $p->is_active ? 'Nonaktif' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form action="{{ route('seller.products.destroy', $p->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm py-0 px-2">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada produk. <a href="{{ route('seller.products.create') }}">Tambah sekarang</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
