<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NovaBankaService
{
    private ?string $apiUrl;
    private ?string $apiKey;
    private ?string $apiSecret;
    private ?string $webhookSecret;

    public function __construct()
    {
        $envUrl = env('NOVA_BANKA_API_URL');
        
        // URL'yi temizle ve ayarla
        if (empty($envUrl) || str_contains($envUrl, 'localhost')) {
            $this->apiUrl = 'https://novabanka.onrender.com/api/v1/pos';
        } else {
            // Sondaki slash'ı (/) temizler ki hata olmasın
            $this->apiUrl = rtrim($envUrl, '/');
        }

        $this->apiKey        = env('NOVA_BANKA_API_KEY');
        $this->apiSecret     = env('NOVA_BANKA_API_SECRET');
        $this->webhookSecret = env('NOVA_BANKA_WEBHOOK_SECRET');
    }

    public function createPaymentSession(array $orderData): array
    {
        // JSON'u ham haliyle hazırlıyoruz
        $body = json_encode($orderData);
        $signature = hash_hmac('sha256', $body, $this->apiSecret ?? '');

        try {
            // URL'yi birleştir (Çift slash hatasını önler)
            $targetUrl = $this->apiUrl . '/create-session';

            $response = Http::withHeaders([
                'X-POS-API-KEY'   => $this->apiKey,
                'X-POS-SIGNATURE' => $signature,
                'Content-Type'    => 'application/json',
                'Accept'          => 'application/json',
            ])->withBody($body, 'application/json')
              ->post($targetUrl);

            if ($response->successful()) {
                return $response->json();
            }

            // HATA VARSA: Samet'in sitesi ne cevap verdi?
            $errorDetail = $response->json('message') ?? $response->body();
            
            Log::error('NovaBanka API Hatası', [
                'status' => $response->status(),
                'detail' => $errorDetail
            ]);

            return [
                'success' => false, 
                'error' => "Banka Hatası ({$response->status()}): " . $errorDetail
            ];

        } catch (\Exception $e) {
            Log::error('NovaBanka Bağlantı Hatası: ' . $e->getMessage());
            return [
                'success' => false, 
                'error' => 'Bağlantı kurulamadı: ' . $e->getMessage()
            ];
        }
    }

    public function verifyWebhook(string $rawBody, string $receivedSignature): bool
    {
        if (!$this->webhookSecret) return false;
        $expected = hash_hmac('sha256', $rawBody, $this->webhookSecret);
        return hash_equals($expected, $receivedSignature);
    }
}
