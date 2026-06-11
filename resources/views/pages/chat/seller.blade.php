@extends('layouts.app')
@section('title', 'Pesan Masuk')
@section('content')
<div class="container">
    <h4 class="fw-bold mb-3">Pesan dari Pembeli</h4>
    <div class="card border">
        @forelse($conversations as $c)
        <a href="{{ route('chat.show',$c->id) }}" class="d-flex align-items-center gap-3 p-3 border-bottom text-decoration-none text-dark">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($c->buyer->name) }}&size=44&background=eee&color=555"
                 class="rounded-circle" width="44" height="44">
            <div class="flex-grow-1">
                <div class="fw-semibold small">{{ $c->buyer->name }}</div>
                <div class="text-muted small text-truncate">{{ $c->latestMessage?->content ?? 'Mulai percakapan...' }}</div>
            </div>
            <div class="text-muted" style="font-size:.72rem">{{ $c->last_message_at->diffForHumans(null,true) }}</div>
        </a>
        @empty
        <div class="text-center text-muted py-5">Belum ada pesan masuk.</div>
        @endforelse
    </div>
</div>
@endsection
