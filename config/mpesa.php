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

    // Sandbox credentials (safe to commit — publicly available Safaricom test credentials).
    // Override via .env / Render env vars when going live with real production credentials.
    'consumer_key'    => env('MPESA_CONSUMER_KEY',    'XkAXKAmuR6SIp0VS2RkudVEn0h5Fi2uzoHeLQLIP0CLcHsbM'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', '93aPerOcgY67kTC9Qzm3kvX5a2XTrb8Fwo9vOJuhrUis3muvXtsou7VBEMYSBNZk'),
    'shortcode'       => env('MPESA_SHORTCODE',       '174379'),    // Safaricom sandbox paybill
    'passkey'         => env('MPESA_PASSKEY',         'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'),
    // Callback URL auto-resolves from APP_URL — works on every environment.
    'callback_url'    => env('MPESA_CALLBACK_URL', rtrim(env('APP_URL', 'https://chamahub.onrender.com'), '/') . '/mpesa/callback'),
    'sandbox'         => env('MPESA_SANDBOX', true),                // Set to false in production

];
