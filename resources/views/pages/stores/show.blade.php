@extends('layouts.app')
@section('title', $store->name)
@section('content')
<div class="container">
    <div class="card border mb-3">
        <div class="card-body d-flex gap-3 align-items-center">
            <img src="{{ $store->logo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($store->name).'&background=2E75B6&color=fff&size=80' }}"
                 class="rounded-circle" width="72" height="72">
            <div>
                <h5 class="fw-bold mb-1">{{ $store->name }}</h5>
                <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $store->city }}, {{ $store->province }}</div>
                <div class="small mt-1"><span class="stars">★</span> {{ number_format($store->rating_avg,1) }} · {{ number_format($store->total_sales) }} terjual</div>
            </div>
        </div>
    </div>
    <div class="row g-2 row-cols-2 row-cols-md-4">
        @foreach($products as $product)
        @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
    <div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
