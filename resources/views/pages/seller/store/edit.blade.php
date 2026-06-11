@extends('layouts.app')
@section('title', 'Edit Toko')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 py-4">
            <h4 class="fw-bold mb-4">Edit Profil Toko</h4>
            <div class="card border shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('seller.store.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Toko</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $store->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $store->description) }}</textarea>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label class="form-label fw-semibold small">Kota</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $store->city) }}">
                            </div>
                            <div class="col">
                                <label class="form-label fw-semibold small">Provinsi</label>
                                <input type="text" name="province" class="form-control" value="{{ old('province', $store->province) }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary-nusa fw-semibold">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
