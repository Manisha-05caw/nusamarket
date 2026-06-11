{{-- ============================================================
     pages/seller/dashboard.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title', 'Dashboard Toko')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="container-fluid px-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Dashboard Toko</h4>
            <p class="text-muted small mb-0">{{ $store->name }} · {{ $store->city }}</p>
        </div>
        <a href="{{ route('seller.products.create') }}" class="btn btn-primary-nusa btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Produk
        </a>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="row g-3 mb-3">
        @foreach([
            ['icon'=>'bi-receipt',      'label'=>'Total Pesanan',   'value'=> number_format($stats['total_orders']),   'color'=>'var(--nm-accent)'],
            ['icon'=>'bi-clock',        'label'=>'Perlu Diproses',  'value'=> number_format($stats['pending_orders']), 'color'=>'#e85d24'],
            ['icon'=>'bi-box-seam',     'label'=>'Produk Aktif',    'value'=> $stats['active_products'].'/'.$stats['total_products'], 'color'=>'#198754'],
            ['icon'=>'bi-star-fill',    'label'=>'Rating Toko',     'value'=> number_format($stats['rating_avg'],1).' ★', 'color'=>'#f5a623'],
            ['icon'=>'bi-wallet2',      'label'=>'Saldo Tersedia',  'value'=>'Rp '.number_format($stats['available_balance'],0,',','.'), 'color'=>'#0d6efd'],
            ['icon'=>'bi-graph-up',     'label'=>'Total Pendapatan','value'=>'Rp '.number_format($stats['total_revenue'],0,',','.'), 'color'=>'#6f42c1'],
        ] as $stat)
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi {{ $stat['icon'] }}" style="font-size:1.2rem;color:{{ $stat['color'] }}"></i>
                        <span class="small text-muted">{{ $stat['label'] }}</span>
                    </div>
                    <div class="fw-bold" style="font-size:1.05rem">{{ $stat['value'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-3">

        {{-- ===== GRAFIK PENJUALAN ===== --}}
        <div class="col-lg-8">
            <div class="card border h-100">
                <div class="card-header bg-white fw-semibold py-2 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-bar-chart me-2" style="color:var(--nm-accent)"></i>Penjualan 7 Hari Terakhir</span>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="120"></canvas>
                </div>
            </div>
        </div>

        {{-- ===== PRODUK TERLARIS ===== --}}
        <div class="col-lg-4">
            <div class="card border h-100">
                <div class="card-header bg-white fw-semibold py-2">
                    <i class="bi bi-trophy me-2" style="color:#f5a623"></i>Produk Terlaris
                </div>
                <div class="list-group list-group-flush">
                    @forelse($topProducts as $i => $p)
                    <a href="{{ route('seller.products.edit', $p->id) }}"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2">
                        <span class="fw-bold text-muted" style="width:18px;font-size:.85rem">{{ $i+1 }}</span>
                        <img src="{{ $p->thumbnail ?? asset('img/placeholder.jpg') }}"
                             class="rounded" width="36" height="36" style="object-fit:cover">
                        <div class="flex-grow-1 min-w-0">
                            <div class="small fw-semibold text-truncate">{{ $p->name }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ number_format($p->total_sold) }} terjual</div>
                        </div>
                    </a>
                    @empty
                    <div class="list-group-item text-muted small">Belum ada data.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== PESANAN TERBARU ===== --}}
        <div class="col-12">
            <div class="card border">
                <div class="card-header bg-white fw-semibold py-2 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2" style="color:var(--nm-accent)"></i>Pesanan Terbaru</span>
                    <a href="{{ route('seller.orders.index') }}" class="small text-decoration-none" style="color:var(--nm-accent)">Lihat semua →</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Pembeli</th>
                                <th>Produk</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th>Waktu</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $o)
                            <tr>
                                <td class="fw-semibold">{{ $o->buyer_name }}</td>
                                <td class="text-truncate" style="max-width:180px">{{ $o->product_name }}</td>
                                <td>Rp {{ number_format($o->subtotal, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge
                                        @if($o->item_status==='delivered') bg-success
                                        @elseif($o->item_status==='pending') bg-warning text-dark
                                        @elseif($o->item_status==='shipped') bg-info
                                        @elseif($o->item_status==='cancelled') bg-danger
                                        @else bg-secondary @endif"
                                        style="font-size:.7rem">
                                        {{ ucfirst($o->item_status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ \Carbon\Carbon::parse($o->created_at)->diffForHumans() }}</td>
                                <td><a href="{{ route('seller.orders.show', $o->id) }}" class="btn btn-outline-secondary btn-sm py-0 px-2">Detail</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada pesanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const labels = @json($salesChart->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M')));
const revenue = @json($salesChart->pluck('revenue'));
const counts  = @json($salesChart->pluck('count'));

new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Pendapatan (Rp)',
                data: revenue,
                backgroundColor: 'rgba(46,117,182,.7)',
                borderRadius: 6,
                yAxisID: 'y',
            },
            {
                label: 'Jumlah Pesanan',
                data: counts,
                type: 'line',
                borderColor: '#e85d24',
                backgroundColor: 'transparent',
                pointBackgroundColor: '#e85d24',
                tension: .3,
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: {
            y:  { position: 'left',  ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } },
            y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { stepSize: 1 } }
        },
        plugins: { legend: { position: 'top' } }
    }
});
</script>
@endpush


{{-- ============================================================
     pages/seller/products/index.blade.php
     ============================================================ --}}
