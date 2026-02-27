<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans configuration
    |--------------------------------------------------------------------------
    |
    | See https://docs.midtrans.com for details. Add the keys to your .env
    | file, e.g. MIDTRANS_SERVER_KEY, MIDTRANS_CLIENT_KEY, MIDTRANS_PRODUCTION.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_3DS', true),
];