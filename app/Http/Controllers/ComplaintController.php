<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function store(Request $request, Listing $listing)
    {
        $request->validate(['reason' => 'required|max:500']);

        // Aynı kullanıcı aynı ilana ikinci kez şikayet edemez
        $alreadyReported = Complaint::where('user_id', Auth::id())
            ->where('listing_id', $listing->id)->exists();

        if ($alreadyReported) {
            return back()->with('error', 'Bu ilanı zaten şikayet ettiniz.');
        }

        Complaint::create([
            'user_id'    => Auth::id(),
            'listing_id' => $listing->id,
            'reason'     => $request->reason,
        ]);

        return back()->with('success', 'Şikayetiniz alındı.');
    }
}
