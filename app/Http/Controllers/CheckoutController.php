<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\NovaBankaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(private NovaBankaService $novaBanka) {}

    /**
     * Satın Al butonuna basılınca burası çalışır.
     */
    public function initiatePayment(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Ödeme yapabilmek için giriş yapmalısınız.');
        }

        // ── 1. İlanı ve satıcıyı bul ──────────────────────────────────────
        $listing = Listing::with('user')->findOrFail($request->listing_id);

        // Kendi ilanını satın almaya çalışıyor mu?
        if ($listing->user_id === Auth::id()) {
            return back()->with('error', 'Kendi ilanınızı satın alamazsınız.');
        }

        // Satıcının IBAN'ı var mı?
        $sellerIban = $listing->user->iban;
        if (!$sellerIban) {
            return back()->with('error', 'Satıcının IBAN bilgisi eksik, ödeme yapılamıyor.');
        }

        // Boşlukları temizle, büyük harfe çevir
        $sellerIban = strtoupper(str_replace(' ', '', $sellerIban));

        // ── 2. Sipariş paketini hazırla ───────────────────────────────────
        $user = Auth::user();

        $orderData = [
            // listing_id'yi order_id'ye gömdük — webhook'ta ilanı bulmak için
            'order_id'       => 'ENS-' . $listing->id . '-' . strtoupper(uniqid()),
            'amount'         => (float) $listing->price,
            'currency'       => 'TRY',
            'description'    => $listing->title . ' - İlan Ödemesi',
            'customer_name'  => $user->name,
            'customer_email' => $user->email,
            'seller_iban'    => $sellerIban,
            'return_url'     => route('orders.success'),
            'webhook_url'    => route('webhook.nova'),
        ];

        // ── 3. Bankadan ödeme oturumu iste ────────────────────────────────
        $result = $this->novaBanka->createPaymentSession($orderData);

        if (isset($result['success']) && $result['success']) {
            return redirect($result['checkout_url']);
        }

        // ── Hata ayıklama ─────────────────────────────────────────────────
        Log::error('Nova Banka API Bağlantı Hatası:', [
            'gönderilen_veri' => $orderData,
            'bankadan_gelen'  => $result,
        ]);

        $technicalError = $result['error'] ?? 'Bilinmeyen hata';

        if (isset($result['details'])) {
            $technicalError .= ' (Detay: ' . json_encode($result['details']) . ')';
        }

        return back()->with('error', 'Ödeme başlatılamadı: ' . $technicalError);
    }

    /**
     * Ödeme tamamlandıktan sonra kullanıcı buraya dönecek.
     */
    public function success(Request $request)
    {
        return view('listings.payment_success');
    }
}