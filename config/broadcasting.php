<?php
// =============================================================
// config/broadcasting.php
// =============================================================

return [
    'default' => env('BROADCAST_CONNECTION', 'reverb'),

    'connections' => [

        // ─── Laravel Reverb (self-hosted, gratis) ─────────────
        'reverb' => [
            'driver'  => 'reverb',
            'key'     => env('REVERB_APP_KEY'),
            'secret'  => env('REVERB_APP_SECRET'),
            'app_id'  => env('REVERB_APP_ID'),
            'options' => [
                'host'   => env('REVERB_HOST', 'localhost'),
                'port'   => env('REVERB_PORT', 8080),
                'scheme' => env('REVERB_SCHEME', 'http'),
                'useTLS' => env('REVERB_SCHEME', 'http') === 'https',
            ],
            'client_options' => [],
        ],

        // ─── Pusher (cloud, ada free tier) ────────────────────
        'pusher' => [
            'driver'  => 'pusher',
            'key'     => env('PUSHER_APP_KEY'),
            'secret'  => env('PUSHER_APP_SECRET'),
            'app_id'  => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
                'useTLS'  => true,
            ],
            'client_options' => [],
        ],

        'log'  => ['driver' => 'log'],
        'null' => ['driver' => 'null'],
    ],
];


/*
=============================================================
.env — pilih salah satu: Reverb (lokal) atau Pusher (cloud)
=============================================================

# === OPSI A: Laravel Reverb (direkomendasikan, gratis, self-hosted) ===
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=nusamarket
REVERB_APP_KEY=nusamarket-key
REVERB_APP_SECRET=nusamarket-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# === OPSI B: Pusher ===
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=ap1

# Aktifkan queue untuk broadcast async
QUEUE_CONNECTION=database
*/
