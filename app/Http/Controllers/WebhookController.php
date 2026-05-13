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
        // 1. Bankadan gelen imzayı ve gövdeyi al
        $signature = $request->header('X-Nova-Signature');
        $rawBody   = $request->getContent();

        // 2. Güvenlik kontrolü
        if (!$this->novaBanka->verifyWebhook($rawBody, $signature)) {
            Log::warning('Nova webhook: Geçersiz imza denemesi!');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($rawBody, true);

        // 3. Ödeme başarılı mı?
        if (isset($data['event']) && $data['event'] === 'payment.completed') {
            $orderId = $data['order_id'] ?? null;

            // order_id formatı: ENS-{listing_id}-{uniqid}
            // Örnek: ENS-42-6A04DB353837B
            if ($orderId && preg_match('/^ENS-(\d+)-/', $orderId, $matches)) {
                $listingId = (int) $matches[1];

                $listing = Listing::find($listingId);

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
            }

            return response()->json(['received' => true]);
        }

        return response()->json(['received' => true, 'status' => 'not_completed']);
    }
}