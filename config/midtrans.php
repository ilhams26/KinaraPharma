<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi ini digunakan oleh PaymentController untuk koneksi ke
    | Midtrans Payment Gateway. Jangan panggil env() langsung di controller,
    | selalu gunakan config('midtrans.*') agar kompatibel dengan config:cache.
    |
    */

    'server_key'    => env('MIDTRANS_SERVER_KEY'),
    'client_key'    => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
];
