@extends('layouts.app')
@section('title', 'Wishlist')
@section('content')
<div class="container">
    <h4 class="fw-bold mb-3"><i class="bi bi-heart me-2"></i>Wishlist Saya</h4>
    @if($items->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-heart" style="font-size:3rem;color:#ccc"></i>
        <p class="text-muted mt-2">Belum ada produk di wishlist.</p>
        <a href="{{ route('home') }}" class="btn btn-primary-nusa btn-sm">Mulai Belanja</a>
    </div>
    @else
    <div class="row g-2 row-cols-2 row-cols-md-4">
        @foreach($items as $item)
        @include('components.product-card', ['product' => $item->product])
        @endforeach
    </div>
    <div class="mt-3">{{ $items->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
