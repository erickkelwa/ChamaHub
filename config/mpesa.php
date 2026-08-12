<?php

return [

    /*
    |--------------------------------------------------------------------------
    | M-Pesa Daraja API Configuration
    |--------------------------------------------------------------------------
    |
    | Register for sandbox credentials at: https://developer.safaricom.co.ke/
    | Replace the values below (or put them in your .env file) before going live.
    |
    */

    'consumer_key'    => env('MPESA_CONSUMER_KEY', 'YOUR_SANDBOX_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', 'YOUR_SANDBOX_CONSUMER_SECRET'),
    'shortcode'       => env('MPESA_SHORTCODE', '174379'),          // Sandbox default paybill
    'passkey'         => env('MPESA_PASSKEY', 'YOUR_PASSKEY'),
    'callback_url'    => env('MPESA_CALLBACK_URL', 'https://yourdomain.com/mpesa/callback'),
    'sandbox'         => env('MPESA_SANDBOX', true),                // Set to false in production

];
