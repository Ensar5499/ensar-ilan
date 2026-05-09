<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Favorite;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Kullanıcının favorilerini, içindeki ilan ve fotoğraflarla beraber çekiyoruz
        $favorites = $user->favorites()
            ->with(['listing.photos']) 
            ->latest()
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    public function toggle(Listing $listing)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Bu ilanın zaten favoride olup olmadığını kontrol et
        $existing = Favorite::where('user_id', $user->id)
                            ->where('listing_id', $listing->id)
                            ->first();

        if ($existing) {
            $existing->delete();
            $msg = 'Favoriden çıkarıldı.';
            $status = 'removed';
        } else {
            Favorite::create([
                'user_id' => $user->id, 
                'listing_id' => $listing->id
            ]);

            // İlan sahibine bildirim gönder (kendi ilanı değilse)
            if ($listing->user_id !== $user->id) {
                UserNotification::create([
                    'user_id' => $listing->user_id,
                    'type'    => 'favorite',
                    'message' => $user->name . ' ilanınızı favorilere ekledi.',
                    'link'    => route('listings.show', $listing),
                ]);
            }
            $msg = 'Favorilere eklendi.';
            $status = 'added';
        }

        // Eğer istek AJAX (JavaScript) ile gelmişse JSON dön, değilse eski usul geri yönlendir
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'status' => $status
            ]);
        }

        return back()->with('success', $msg);
    }
}