@extends('layouts.app')
@section('title', 'Saldo Toko')
@section('content')
<div class="container">
    <h4 class="fw-bold mb-3">Saldo & Keuangan</h4>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border text-center p-3">
                <div class="text-muted small mb-1">Saldo Tersedia</div>
                <div class="fw-bold fs-4" style="color:var(--nm-accent)">Rp {{ number_format($balance?->available??0,0,',','.') }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border text-center p-3">
                <div class="text-muted small mb-1">Saldo Pending</div>
                <div class="fw-bold fs-4">Rp {{ number_format($balance?->pending??0,0,',','.') }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border text-center p-3">
                <div class="text-muted small mb-1">Total Pendapatan</div>
                <div class="fw-bold fs-4 text-success">Rp {{ number_format($balance?->total_earned??0,0,',','.') }}</div>
            </div>
        </div>
    </div>
    <div class="card border mb-3">
        <div class="card-header bg-white fw-semibold py-2">Tarik Saldo</div>
        <div class="card-body">
            <form method="POST" action="{{ route('seller.balance.withdraw') }}" class="row g-2">
                @csrf
                <div class="col-md-3"><input type="number" name="amount" class="form-control" placeholder="Jumlah (min Rp 50.000)" min="50000" required></div>
                <div class="col-md-3"><input type="text" name="bank_name" class="form-control" placeholder="Nama Bank" required></div>
                <div class="col-md-3"><input type="text" name="account_number" class="form-control" placeholder="No. Rekening" required></div>
                <div class="col-md-2"><input type="text" name="account_name" class="form-control" placeholder="Nama Pemilik" required></div>
                <div class="col-md-1"><button type="submit" class="btn btn-primary-nusa w-100">Tarik</button></div>
            </form>
        </div>
    </div>
    <div class="card border">
        <div class="card-header bg-white fw-semibold py-2">Riwayat Transaksi</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light"><tr><th>Tipe</th><th>Jumlah</th><th>Saldo Akhir</th><th>Keterangan</th><th>Tanggal</th></tr></thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <td><span class="badge {{ str_contains($tx->type,'credit')?'bg-success':'bg-danger' }}" style="font-size:.7rem">{{ $tx->type }}</span></td>
                        <td>Rp {{ number_format($tx->amount,0,',','.') }}</td>
                        <td>Rp {{ number_format($tx->balance_after,0,',','.') }}</td>
                        <td class="text-muted">{{ $tx->description }}</td>
                        <td class="text-muted">{{ $tx->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $transactions->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
