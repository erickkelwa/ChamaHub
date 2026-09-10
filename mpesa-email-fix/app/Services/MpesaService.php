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
        if (empty($callback) || str_starts_with($callback, 'http://localhost') || str_starts_with($callback, 'http://127.0.0.1')) {
            $callback = 'https://example.com/mpesa/callback';
        } elseif (str_starts_with($callback, 'http://')) {
            $callback = str_replace('http://', 'https://', $callback);
        }
        $this->callbackUrl    = $callback;
        $this->baseUrl        = config('mpesa.sandbox') ? 'https://sandbox.safaricom.co.ke' : 'https://api.safaricom.co.ke';
    }

    /**
     * Normalize any phone format a user might type (0712345678, +254712345678,
     * 254 712 345 678, 712345678, etc.) into Safaricom's required
     * 2547XXXXXXXX / 2541XXXXXXXX format. Returns null if it can't be
     * turned into a valid Kenyan mobile number, so callers can show a
     * friendly error instead of letting Safaricom reject the request.
     */
    public function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Strip everything except digits (handles +, spaces, dashes, brackets).
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($digits, '254')) {
            $normalized = $digits;
        } elseif (str_starts_with($digits, '0')) {
            $normalized = '254' . substr($digits, 1);
        } elseif (str_starts_with($digits, '7') || str_starts_with($digits, '1')) {
            // e.g. "712345678" typed without the leading 0
            $normalized = '254' . $digits;
        } else {
            return null;
        }

        // Must end up as 254 + 7 or 1 + 8 more digits = 12 digits total.
        return preg_match('/^254[71]\d{8}$/', $normalized) ? $normalized : null;
    }

    /**
     * Fetch an OAuth access token from Safaricom.
     */
    public function getAccessToken(): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withBasicAuth($this->consumerKey, $this->consumerSecret)
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
     * Initiate an STK Push (Lipa na M-Pesa) request.
     *
     * @param string $phone  Phone number in format 2547XXXXXXXX
     * @param float  $amount Amount to charge
     * @param string $accountRef Reference (e.g. "Contribution - August 2026")
     * @param string $description Short description
     */
    public function stkPush(string $phone, float $amount, string $accountRef, string $description): array
    {
        // Re-normalize defensively in case a caller passed a raw, unformatted
        // number straight through — Safaricom rejects anything that isn't
        // exactly 2547XXXXXXXX / 2541XXXXXXXX with no leading '+' or spaces.
        $normalizedPhone = $this->normalizePhone($phone);

        if (!$normalizedPhone) {
            return ['success' => false, 'message' => "Invalid phone number '{$phone}'. Use a Safaricom/Airtel number like 0712345678."];
        }

        $token = $this->getAccessToken();

        if (!$token) {
            return ['success' => false, 'message' => 'Could not obtain access token. Check MPESA_CONSUMER_KEY / MPESA_CONSUMER_SECRET.'];
        }

        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($this->shortCode . $this->passkey . $timestamp);

        try {
            $response = Http::timeout(30)
                ->withToken($token)
                ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", [
                    'BusinessShortCode' => $this->shortCode,
                    'Password'          => $password,
                    'Timestamp'         => $timestamp,
                    'TransactionType'   => 'CustomerPayBillOnline',
                    'Amount'            => (int) $amount,
                    'PartyA'            => $normalizedPhone,
                    'PartyB'            => $this->shortCode,
                    'PhoneNumber'       => $normalizedPhone,
                    'CallBackURL'       => $this->callbackUrl,
                    'AccountReference'  => $accountRef,
                    'TransactionDesc'   => $description,
                ]);

            $data = $response->json();

            if (isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                return ['success' => true, 'data' => $data];
            }

            Log::error('M-Pesa STK Push failed', ['response' => $data]);
            return ['success' => false, 'message' => $data['errorMessage'] ?? 'STK Push failed.', 'data' => $data];

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
