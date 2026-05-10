<?php

namespace App\Http\Controllers;

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

        // 2. Güvenlik kontrolü: Bu mesaj gerçekten arkadaşından mı geldi?
        if (!$this->novaBanka->verifyWebhook($rawBody, $signature)) {
            Log::warning('Nova webhook: Geçersiz imza denemesi!');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($rawBody, true);

        // 3. Ödeme başarılı mı?
        if (isset($data['status']) && $data['status'] === 'completed') {
            $orderId = $data['order_id'];
            
            // BURADA: Veritabanında ilanı "Satıldı" olarak işaretleyebilir 
            // veya kullanıcıya bildirim atabilirsin.
            Log::info('Ödeme Başarılı! Sipariş No: ' . $orderId);
            
            return response()->json(['received' => true]);
        }

        return response()->json(['received' => true, 'status' => 'not_completed']);
    }
}
