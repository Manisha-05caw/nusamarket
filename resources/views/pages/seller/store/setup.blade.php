@extends('layouts.app')
@section('title', 'Setup Toko')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 py-4">
            <div class="text-center mb-4">
                <i class="bi bi-shop" style="font-size:3rem;color:var(--nm-accent)"></i>
                <h4 class="fw-bold mt-2">Setup Toko Kamu</h4>
                <p class="text-muted small">Lengkapi informasi toko untuk mulai berjualan</p>
            </div>
            <div class="card border shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('seller.store.setup.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Toko <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Contoh: Batik Java Official" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Deskripsi Toko</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Ceritakan tentang toko kamu...">{{ old('description') }}</textarea>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label class="form-label fw-semibold small">Kota</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="Jakarta">
                            </div>
                            <div class="col">
                                <label class="form-label fw-semibold small">Provinsi</label>
                                <input type="text" name="province" class="form-control" value="{{ old('province') }}" placeholder="DKI Jakarta">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary-nusa w-100 fw-semibold">
                            <i class="bi bi-shop me-1"></i>Buat Toko Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
