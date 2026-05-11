<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\User;
use App\Models\Complaint; // Report yerine Complaint eklendi
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_listings'    => Listing::count(),
            'active_listings'   => Listing::where('status', 'active')->count(),
            'total_users'       => User::count(),
            // Bekleyen şikayet sayısını Complaint modelinden çekiyoruz
            'total_complaints'  => Complaint::where('status', 'pending')->count(),
        ];

        // Sistem ayarlarını veritabanından çekiyoruz
        $settings = [
            'maintenance_mode' => Setting::get('maintenance_mode', '0'),
            'disable_listings' => Setting::get('disable_listings', '0'),
        ];

        return view('admin.dashboard', compact('stats', 'settings'));
    }

    /**
     * Şikayetleri admin panelinde listelemek için gereken metod.
     */
    public function complaints()
    {
        // Şikayetleri, şikayet edilen ilan ve şikayet eden kullanıcı bilgileriyle beraber çekiyoruz
        $complaints = Complaint::with(['user', 'listing'])->latest()->get();
        
        return view('admin.complaints', compact('complaints'));
    }

    /**
     * AJAX ile gelen ayar güncelleme isteğini işler.
     */
    public function updateSetting(Request $request)
    {
        // Gelen veriyi doğrula
        $request->validate([
            'key'   => 'required|string',
            'value' => 'required|string'
        ]);

        // Ayarı güncelle veya yoksa oluştur
        Setting::updateOrCreate(
            ['key' => $request->key],
            ['value' => $request->value]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Sistem ayarı başarıyla güncellendi.'
        ]);
    }
}