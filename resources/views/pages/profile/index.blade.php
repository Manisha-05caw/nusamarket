@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 py-3">
            <h4 class="fw-bold mb-4">Profil Saya</h4>
            <div class="card border mb-3">
                <div class="card-header bg-white fw-semibold py-2">Informasi Akun</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name',$user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">No. HP</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone',$user->phone) }}">
                        </div>
                        <button type="submit" class="btn btn-primary-nusa">Simpan</button>
                    </form>
                </div>
            </div>
            <div class="card border">
                <div class="card-header bg-white fw-semibold py-2">Ganti Password</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password Baru</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-outline-primary">Ganti Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
