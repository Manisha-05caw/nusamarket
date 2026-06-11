@extends('layouts.app')
@section('title', 'Daftar Akun')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4 py-4">
            <div class="text-center mb-4">
                <div class="fw-bold fs-4" style="color:var(--nm-primary)"><i class="bi bi-shop me-2"></i>NusaMarket</div>
                <p class="text-muted small">Buat akun baru</p>
            </div>
            <div class="card border shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Nama kamu">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="email@contoh.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">No. HP (opsional)</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone') }}" placeholder="08xx">
                        </div>
                        <div class="mb-3" x-data="{ show: false }">
                            <label class="form-label small fw-semibold">Password</label>
                            <div class="input-group">
                                <input :type="show ? 'text' : 'password'" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Min. 8 karakter">
                                <button type="button" class="btn btn-outline-secondary" @click="show = !show">
                                    <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                                </button>
                            </div>
                            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" placeholder="Ulangi password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Daftar sebagai</label>
                            <div class="row g-2" x-data="{ role: '{{ old('role','buyer') }}' }">
                                <div class="col-6">
                                    <label class="d-block border rounded-3 p-3 text-center cursor-pointer"
                                           :class="role==='buyer'?'border-primary bg-light':''"
                                           @click="role='buyer'" style="cursor:pointer">
                                        <input type="radio" name="role" value="buyer" class="d-none"
                                               {{ old('role','buyer')==='buyer'?'checked':'' }}>
                                        <i class="bi bi-bag d-block fs-4 mb-1" style="color:var(--nm-accent)"></i>
                                        <div class="small fw-semibold">Pembeli</div>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="d-block border rounded-3 p-3 text-center cursor-pointer"
                                           :class="role==='seller'?'border-primary bg-light':''"
                                           @click="role='seller'" style="cursor:pointer">
                                        <input type="radio" name="role" value="seller" class="d-none"
                                               {{ old('role')==='seller'?'checked':'' }}>
                                        <i class="bi bi-shop d-block fs-4 mb-1" style="color:var(--nm-orange)"></i>
                                        <div class="small fw-semibold">Penjual</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-orange w-100 fw-semibold">Buat Akun</button>
                    </form>
                </div>
            </div>
            <p class="text-center small text-muted mt-3">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color:var(--nm-accent)">Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection
