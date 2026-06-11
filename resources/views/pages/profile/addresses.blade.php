@extends('layouts.app')
@section('title', 'Alamat Saya')
@section('content')
<div class="container">
    <h4 class="fw-bold mb-3">Alamat Pengiriman</h4>
    @foreach($addresses as $addr)
    <div class="card border mb-2">
        <div class="card-body py-2">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="fw-semibold small">{{ $addr->recipient }} <span class="badge bg-secondary ms-1" style="font-size:.7rem">{{ $addr->label }}</span> @if($addr->is_default)<span class="badge" style="background:var(--nm-accent-lt);color:var(--nm-primary);font-size:.7rem">Utama</span>@endif</div>
                    <div class="text-muted small">{{ $addr->phone }}</div>
                    <div class="text-muted small">{{ $addr->full_address }}</div>
                </div>
                <div class="d-flex gap-1">
                    @if(!$addr->is_default)
                    <form action="{{ route('addresses.default',$addr->id) }}" method="POST">@csrf<button class="btn btn-outline-secondary btn-sm py-0 px-2">Jadikan Utama</button></form>
                    @endif
                    <form action="{{ route('addresses.destroy',$addr->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm py-0 px-2">Hapus</button></form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <div class="card border mt-3">
        <div class="card-header bg-white fw-semibold py-2">Tambah Alamat Baru</div>
        <div class="card-body">
            <form method="POST" action="{{ route('addresses.store') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-3"><input type="text" name="label" class="form-control form-control-sm" placeholder="Label (Rumah/Kantor)" value="Rumah" required></div>
                    <div class="col-md-4"><input type="text" name="recipient" class="form-control form-control-sm" placeholder="Nama Penerima" required></div>
                    <div class="col-md-3"><input type="text" name="phone" class="form-control form-control-sm" placeholder="No. HP" required></div>
                    <div class="col-12"><input type="text" name="address_line" class="form-control form-control-sm" placeholder="Alamat Lengkap" required></div>
                    <div class="col-md-4"><input type="text" name="city" class="form-control form-control-sm" placeholder="Kota" required></div>
                    <div class="col-md-4"><input type="text" name="province" class="form-control form-control-sm" placeholder="Provinsi" required></div>
                    <div class="col-md-2"><input type="text" name="postal_code" class="form-control form-control-sm" placeholder="Kode Pos" required></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary-nusa btn-sm w-100">Tambah</button></div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
