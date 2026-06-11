@extends('layouts.app')
@section('title', 'Flash Sale')
@section('content')
<div class="container">
    <h4 class="fw-bold mb-3"><span class="badge badge-flash me-2">⚡ Flash Sale</span></h4>
    <div class="row g-2 row-cols-2 row-cols-md-4">
        @foreach($products as $product)
        @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
    <div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
