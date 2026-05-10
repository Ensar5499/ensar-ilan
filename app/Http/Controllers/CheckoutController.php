<?php

namespace App\Http\Controllers;

use App\Services\NovaBankaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Hataları kaydetmek için eklendi

class CheckoutController extends Controller
{
    /**
     * Servisimizi buraya dahil ediyoruz
     */
    public function __construct(private NovaBankaService $novaBanka) {}

    /**
     * Satın Al butonuna basılınca burası çalışır
     */
    public function initiatePayment(Request $request)
    {
        // Kullanıcının giriş yapıp yapmadığını garantiye alalım
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Ödeme yapabilmek için giriş yapmalısınız.');
        }

        $user = Auth::user();

        // Önemli: Formdan gelen verilerle sipariş paketini hazırlıyoruz
        $orderData = [
            'order_id'       => 'ENS-' . strtoupper(uniqid()), // Benzersiz bir numara
            'amount'         => (float) $request->amount,       // İlanın fiyatı
            'currency'       => 'TRY',
            'description'    => $request->description,         // İlanın başlığı
            'customer_name'  => $user->name,
            'customer_email' => $user->email,
            'return_url'     => route('orders.success'),       // Ödeme bitince döneceği yer
            'webhook_url'    => route('webhook.nova'),         // Bankanın onay atacağı yer
        ];

        // Servisi kullanarak bankadan oturum (session) istiyoruz
        $result = $this->novaBanka->createPaymentSession($orderData);

        if (isset($result['success']) && $result['success']) {
            // Her şey tamamsa, kullanıcıyı arkadaşının ödeme sayfasına (checkout_url) gönder
            return redirect($result['checkout_url']);
        }

        // --- HATA AYIKLAMA (DEBUG) BÖLÜMÜ ---
        // Eğer ödeme başlamıyorsa, Render loglarına (Logs) teknik detayı yazdırıyoruz.
        Log::error('Nova Banka API Bağlantı Hatası:', [
            'gönderilen_veri' => $orderData,
            'bankadan_gelen' => $result
        ]);

        // Ekrana basılacak hata mesajını detaylandırıyoruz
        $technicalError = $result['error'] ?? 'Bilinmeyen hata';
        
        // Eğer bankadan (Samet'in sitesinden) bir doğrulama hatası geldiyse onu da ekleyelim
        if (isset($result['details'])) {
            $technicalError .= ' (Detay: ' . json_encode($result['details']) . ')';
        }
        // ------------------------------------

        // Hata varsa geri gönder ve detaylı mesajı bas
        return back()->with('error', 'Ödeme başlatılamadı: ' . $technicalError);
    }

    /**
     * Ödeme tamamlandıktan sonra kullanıcı buraya dönecek
     */
    public function success(Request $request)
    {
        return "Tebrikler Ensar, ödeme başarıyla gerçekleşti! İlanın şimdi onaylanıyor.";
    }
}
