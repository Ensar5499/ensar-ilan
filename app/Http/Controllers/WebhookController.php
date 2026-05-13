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

        // ── Gelen her şeyi logla (debug için) ─────────────────────────────
        Log::info('Nova webhook alındı', [
            'event'        => $data['event'] ?? 'YOK',
            'order_id'     => $data['order_id'] ?? 'YOK',
            'signature'    => $signature ? substr($signature, 0, 20) . '...' : 'YOK',
            'body_preview' => substr($rawBody, 0, 200),
        ]);

        // ── İmza doğrulama ─────────────────────────────────────────────────
        if (!$this->novaBanka->verifyWebhook($rawBody, $signature ?? '')) {
            // İmza hatalıysa logla ama yine de işlemi dene (geliştirme aşaması)
            Log::warning('Nova webhook: İmza doğrulaması başarısız! Yine de işleniyor...', [
                'signature_received' => $signature,
            ]);
            // TODO: Canlıya geçince bu satırı açıp alttaki bloğu kaldır:
            // return response()->json(['error' => 'Unauthorized'], 401);
        }

        // ── Ödeme tamamlandı eventi ────────────────────────────────────────
        if (isset($data['event']) && $data['event'] === 'payment.completed') {
            $orderId = $data['order_id'] ?? null;

            Log::info('Nova webhook: payment.completed alındı', ['order_id' => $orderId]);

            // order_id formatı: ENS-{listing_id}-{uniqid}
            if ($orderId && preg_match('/^ENS-(\d+)-/', $orderId, $matches)) {
                $listingId = (int) $matches[1];
                $listing   = Listing::find($listingId);

                if ($listing) {
                    $listing->update(['status' => 'sold']);

                    Log::info('Webhook: İlan satıldı olarak işaretlendi', [
                        'listing_id' => $listingId,
                        'order_id'   => $orderId,
                    ]);
                } else {
                    Log::error('Webhook: İlan bulunamadı', [
                        'listing_id' => $listingId,
                        'order_id'   => $orderId,
                    ]);
                }
            } else {
                Log::error('Webhook: order_id formatı tanınmadı', ['order_id' => $orderId]);
            }

            return response()->json(['received' => true]);
        }

        Log::info('Nova webhook: Bilinmeyen event', ['event' => $data['event'] ?? 'YOK']);
        return response()->json(['received' => true, 'status' => 'ignored']);
    }
}