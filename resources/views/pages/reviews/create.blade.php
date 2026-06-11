@extends('layouts.app')
@section('title', 'Beri Ulasan')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 py-3">
            <h4 class="fw-bold mb-3">Beri Ulasan</h4>
            <div class="card border mb-3">
                <div class="card-body d-flex gap-3">
                    <img src="{{ $item->product->thumbnail ?? asset('img/placeholder.jpg') }}" class="rounded" width="60" height="60" style="object-fit:cover">
                    <div><div class="fw-semibold">{{ $item->product_name }}</div><div class="text-muted small">x{{ $item->quantity }}</div></div>
                </div>
            </div>
            <form method="POST" action="{{ route('reviews.store',[$order->id,$item->id]) }}" enctype="multipart/form-data">
                @csrf
                @foreach([['name'=>'rating_product','label'=>'Kualitas Produk'],['name'=>'rating_delivery','label'=>'Pengiriman'],['name'=>'rating_service','label'=>'Pelayanan Penjual']] as $r)
                <div class="mb-3" x-data="{ rating: 5 }">
                    <label class="form-label small fw-semibold">{{ $r['label'] }}</label>
                    <div class="d-flex gap-2">
                        @for($i=1;$i<=5;$i++)
                        <label style="cursor:pointer;font-size:1.5rem;color:#f5a623">
                            <input type="radio" name="{{ $r['name'] }}" value="{{ $i }}" class="d-none" {{ $i===5?'checked':'' }}>
                            ★
                        </label>
                        @endfor
                    </div>
                </div>
                @endforeach
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Komentar (opsional)</label>
                    <textarea name="comment" class="form-control" rows="3" placeholder="Bagikan pengalaman belanjamu..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary-nusa w-100 fw-semibold">Kirim Ulasan</button>
            </form>
        </div>
    </div>
</div>
@endsection
