<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\User;
use App\Models\Complaint;
use App\Models\Setting; // Ayarlar modelini ekledik
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_listings'    => Listing::count(),
            'active_listings'   => Listing::where('status', 'active')->count(),
            'total_users'       => User::count(),
            'total_complaints'  => Complaint::where('status', 'pending')->count(),
        ];

        // Sistem ayarlarını veritabanından çekiyoruz (Yoksa varsayılan 0 döner)
        $settings = [
            'maintenance_mode' => Setting::get('maintenance_mode', '0'),
            'disable_listings' => Setting::get('disable_listings', '0'),
        ];

        return view('admin.dashboard', compact('stats', 'settings'));
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