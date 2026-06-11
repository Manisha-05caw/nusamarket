{{-- pages/notifications/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Notifikasi')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="fw-bold mb-0"><i class="bi bi-bell me-2"></i>Semua Notifikasi</h4>
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-check-all me-1"></i>Tandai Semua Dibaca
                    </button>
                </form>
            </div>

            @forelse($notifications as $notif)
            @php
            $icons = [
                'new_order'       => ['icon' => 'bi-bag-check',      'color' => '#198754', 'bg' => '#d1e7dd'],
                'new_message'     => ['icon' => 'bi-chat-dots',      'color' => '#0d6efd', 'bg' => '#dbeafe'],
                'order_shipped'   => ['icon' => 'bi-truck',          'color' => '#0dcaf0', 'bg' => '#cff4fc'],
                'order_completed' => ['icon' => 'bi-check-circle',   'color' => '#198754', 'bg' => '#d1e7dd'],
                'new_review'      => ['icon' => 'bi-star',           'color' => '#f5a623', 'bg' => '#fff3cd'],
                'payment_success' => ['icon' => 'bi-credit-card',    'color' => '#198754', 'bg' => '#d1e7dd'],
                'store_approved'  => ['icon' => 'bi-patch-check',    'color' => '#0d6efd', 'bg' => '#dbeafe'],
                'store_suspended' => ['icon' => 'bi-exclamation-triangle', 'color' => '#dc3545', 'bg' => '#f8d7da'],
                'order_cancelled' => ['icon' => 'bi-x-circle',       'color' => '#dc3545', 'bg' => '#f8d7da'],
            ];
            $ic = $icons[$notif->type] ?? ['icon' => 'bi-bell', 'color' => '#6c757d', 'bg' => '#e9ecef'];
            @endphp

            <a href="{{ route('notifications.read', $notif->id) }}"
               class="d-flex gap-3 p-3 text-decoration-none text-dark rounded-3 mb-2
                      {{ $notif->is_read ? 'bg-white border' : 'border border-primary bg-light' }}">

                {{-- Icon --}}
                <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px;background:{{ $ic['bg'] }};color:{{ $ic['color'] }}">
                    <i class="bi {{ $ic['icon'] }} fs-5"></i>
                </div>

                {{-- Konten --}}
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="fw-semibold small {{ $notif->is_read ? 'text-muted' : '' }}">
                            {{ $notif->title }}
                        </div>
                        <div class="text-muted flex-shrink-0 ms-2" style="font-size:.72rem">
                            {{ $notif->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @if($notif->body)
                    <div class="text-muted small mt-1">{{ $notif->body }}</div>
                    @endif
                </div>

                {{-- Unread dot --}}
                @if(!$notif->is_read)
                <div class="flex-shrink-0 align-self-center">
                    <div class="rounded-circle bg-primary" style="width:8px;height:8px"></div>
                </div>
                @endif
            </a>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-bell-slash" style="font-size:3rem;color:#ccc"></i>
                <p class="text-muted mt-2">Belum ada notifikasi.</p>
            </div>
            @endforelse

            <div class="mt-3">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
@endsection
