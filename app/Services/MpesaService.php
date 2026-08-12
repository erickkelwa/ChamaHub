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
     * Initiate an STK Push (Lipa na M-Pesa) request.
     *
     * @param string $phone  Phone number in format 2547XXXXXXXX
     * @param float  $amount Amount to charge
     * @param string $accountRef Reference (e.g. "Contribution - August 2026")
     * @param string $description Short description
     */
    public function stkPush(string $phone, float $amount, string $accountRef, string $description): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return ['success' => false, 'message' => 'Could not obtain access token.'];
        }

        $timestamp = now()->format('YmdHis');
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
