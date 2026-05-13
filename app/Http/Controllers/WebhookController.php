<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\NovaBankaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(private NovaBankaService $novaBanka) {}

    public function handleNova(Request $request)
    {
        $signature = $request->header('X-Nova-Signature');
        $rawBody   = $request->getContent();
        $data      = json_decode($rawBody, true);

        // Debug: tam olarak ne geliyor görelim
        $webhookSecret = env('NOVA_BANKA_WEBHOOK_SECRET');
        $expected      = hash_hmac('sha256', $rawBody, $webhookSecret ?? '');

        Log::info('Nova webhook debug', [
            'secret_var_set'      => !empty($webhookSecret),
            'secret_son4'         => $webhookSecret ? '***' . substr($webhookSecret, -4) : 'YOK',
            'received_signature'  => $signature ? substr($signature, 0, 20) . '...' : 'YOK',
            'expected_signature'  => substr($expected, 0, 20) . '...',
            'eslesme'             => hash_equals($expected, $signature ?? '') ? 'EVET' : 'HAYIR',
            'event'               => $data['event'] ?? 'YOK',
            'order_id'            => $data['order_id'] ?? 'YOK',
        ]);

        // Şimdilik imza olmadan işle (debug sonrası açacağız)
        if (isset($data['event']) && $data['event'] === 'payment.completed') {
            $orderId = $data['order_id'] ?? null;

            if ($orderId && preg_match('/^ENS-(\d+)-/', $orderId, $matches)) {
                $listingId = (int) $matches[1];
                $listing   = Listing::find($listingId);

                if ($listing) {
                    $listing->update(['status' => 'sold']);
                    Log::info('Webhook: İlan satıldı', ['listing_id' => $listingId]);
                }
            }

            return response()->json(['received' => true]);
        }

        return response()->json(['received' => true, 'status' => 'ignored']);
    }
}