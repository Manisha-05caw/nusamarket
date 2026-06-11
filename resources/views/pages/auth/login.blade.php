@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4 py-4">
            <div class="text-center mb-4">
                <div class="fw-bold fs-4" style="color:var(--nm-primary)"><i class="bi bi-shop me-2"></i>NusaMarket</div>
                <p class="text-muted small">Masuk ke akun kamu</p>
            </div>
            <div class="card border shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="email@contoh.com" autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3" x-data="{ show: false }">
                            <label class="form-label small fw-semibold">Password</label>
                            <div class="input-group">
                                <input :type="show ? 'text' : 'password'" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Masukkan password">
                                <button type="button" class="btn btn-outline-secondary" @click="show = !show">
                                    <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label small" for="remember">Ingat saya</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary-nusa w-100 fw-semibold">Masuk</button>
                    </form>
                </div>
            </div>
            <p class="text-center small text-muted mt-3">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-decoration-none fw-semibold" style="color:var(--nm-accent)">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection
