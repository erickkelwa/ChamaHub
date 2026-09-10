<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $shortCode;
    protected string $passkey;
    protected string $callbackUrl;
    protected string $baseUrl;

    public function __construct()
    {
        $this->consumerKey    = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->shortCode      = config('mpesa.shortcode');
        $this->passkey        = config('mpesa.passkey');
        $callback = config('mpesa.callback_url');
        // If running locally, derive callback from APP_URL (Render URL) so Safaricom can reach it.
        if (empty($callback) || str_starts_with($callback, 'http://localhost') || str_starts_with($callback, 'http://127.0.0.1')) {
            $appUrl = rtrim(config('app.url', 'https://chamahub.onrender.com'), '/');
            // If APP_URL itself is local, use the known production URL as fallback
            if (str_starts_with($appUrl, 'http://localhost') || str_starts_with($appUrl, 'http://127.0.0.1')) {
                $appUrl = 'https://chamahub.onrender.com';
            }
            $callback = $appUrl . '/mpesa/callback';
        } elseif (str_starts_with($callback, 'http://')) {
            // Safaricom requires HTTPS
            $callback = str_replace('http://', 'https://', $callback);
        }
        $this->callbackUrl    = $callback;
        $this->baseUrl        = config('mpesa.sandbox') ? 'https://sandbox.safaricom.co.ke' : 'https://api.safaricom.co.ke';
    }

    /**
     * Fetch an OAuth access token from Safaricom.
     */
    public function getAccessToken(): ?string
    {
        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('M-Pesa: Failed to get access token', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('M-Pesa: Access token exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Normalize any Kenyan phone number to the 12-digit 254XXXXXXXXX format
     * that Safaricom's STK Push API requires.
     *
     * Handles: 07XXXXXXXX, 01XXXXXXXX, +2547XXXXXXXX, 2547XXXXXXXX,
     *          numbers with spaces/dashes/parentheses.
     *
     * Returns null when the number cannot be mapped to a valid Kenyan mobile.
     */
    public static function normalizePhone(string $raw): ?string
    {
        // Strip everything except digits
        $digits = preg_replace('/[^0-9]/', '', $raw);

        // Handle leading '254' (possibly from '+254...')
        if (str_starts_with($digits, '254')) {
            $normalized = $digits;
        // Handle local format: 07XXXXXXXX or 01XXXXXXXX
        } elseif (str_starts_with($digits, '0') && strlen($digits) === 10) {
            $normalized = '254' . substr($digits, 1);
        // Handle 9-digit number without leading 0 or country code (e.g. 7XXXXXXXX)
        } elseif (strlen($digits) === 9) {
            $normalized = '254' . $digits;
        } else {
            return null;
        }

        // Must be exactly 12 digits: 254 + 9-digit subscriber number
        if (!preg_match('/^254[0-9]{9}$/', $normalized)) {
            return null;
        }

        return $normalized;
    }

    /**
     * Initiate an STK Push (Lipa na M-Pesa) request.
     *
     * @param string $phone  Phone number in any recognisable Kenyan format
     * @param float  $amount Amount to charge
     * @param string $accountRef Reference (e.g. "Contribution - August 2026")
     * @param string $description Short description
     */
    public function stkPush(string $phone, float $amount, string $accountRef, string $description): array
    {
        // Normalize the phone number before anything else
        $phone = self::normalizePhone($phone);
        if (!$phone) {
            return ['success' => false, 'message' => "Invalid phone number. Use a Safaricom/Airtel number like 0712345678 or 254712345678."];
        }

        $token = $this->getAccessToken();

        if (!$token) {
            return ['success' => false, 'message' => 'Could not obtain access token.'];
        }

        // Safaricom expects this timestamp in Africa/Nairobi (EAT) local time,
        // regardless of the app's configured timezone (config/app.php is UTC).
        // Using the wrong timezone here throws the password off by hours,
        // which Safaricom rejects with errorCode 500.001.1001 "Wrong credentials".
        $timestamp = now('Africa/Nairobi')->format('YmdHis');
        $password  = base64_encode($this->shortCode . $this->passkey . $timestamp);

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", [
                    'BusinessShortCode' => $this->shortCode,
                    'Password'          => $password,
                    'Timestamp'         => $timestamp,
                    'TransactionType'   => 'CustomerPayBillOnline',
                    'Amount'            => (int) $amount,
                    'PartyA'            => $phone,
                    'PartyB'            => $this->shortCode,
                    'PhoneNumber'       => $phone,
                    'CallBackURL'       => $this->callbackUrl,
                    'AccountReference'  => $accountRef,
                    'TransactionDesc'   => $description,
                ]);

            $data = $response->json();

            if (isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                return ['success' => true, 'data' => $data];
            }

            Log::error('M-Pesa STK Push failed', ['response' => $data]);
            $message = $data['errorMessage']
                ?? $data['ResponseDescription']
                ?? $data['CustomerMessage']
                ?? 'STK Push failed.';
            return ['success' => false, 'message' => $message, 'data' => $data];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle the M-Pesa callback and return the parsed result.
     */
    public function parseCallback(array $callbackData): array
    {
        $body    = $callbackData['Body']['stkCallback'] ?? [];
        $code    = $body['ResultCode'] ?? 1;
        $items   = $body['CallbackMetadata']['Item'] ?? [];

        $meta = [];
        foreach ($items as $item) {
            $meta[$item['Name']] = $item['Value'] ?? null;
        }

        return [
            'success'       => $code === 0,
            'result_code'   => $code,
            'result_desc'   => $body['ResultDesc'] ?? 'Unknown',
            'checkout_id'   => $body['CheckoutRequestID'] ?? null,
            'amount'        => $meta['Amount'] ?? null,
            'mpesa_code'    => $meta['MpesaReceiptNumber'] ?? null,
            'phone'         => $meta['PhoneNumber'] ?? null,
            'transaction_date' => $meta['TransactionDate'] ?? null,
        ];
    }
}
