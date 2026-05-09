<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NovaBankaService
{
    private string $apiUrl;
    private string $apiKey;
    private string $apiSecret;
    private string $webhookSecret;

    public function __construct()
    {
        $this->apiUrl        = env('NOVA_BANKA_API_URL');
        $this->apiKey        = env('NOVA_BANKA_API_KEY');
        $this->apiSecret     = env('NOVA_BANKA_API_SECRET');
        $this->webhookSecret = env('NOVA_BANKA_WEBHOOK_SECRET');
    }

    public function createPaymentSession(array $orderData): array
    {
        $body = json_encode($orderData);
        $signature = hash_hmac('sha256', $body, $this->apiSecret);

        $response = Http::withHeaders([
            'X-POS-API-KEY'   => $this->apiKey,
            'X-POS-SIGNATURE' => $signature,
            'Content-Type'    => 'application/json',
            'Accept'          => 'application/json',
        ])->withBody($body, 'application/json')
          ->post($this->apiUrl . '/create-session');

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('NovaBanka ödeme oturumu hatası', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        return ['success' => false, 'error' => 'Ödeme başlatılamadı.'];
    }

    public function verifyWebhook(string $rawBody, string $receivedSignature): bool
    {
        $expected = hash_hmac('sha256', $rawBody, $this->webhookSecret);
        return hash_equals($expected, $receivedSignature);
    }
}