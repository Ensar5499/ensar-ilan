<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Mesajlaşma listesi
    public function index()
    {
        $userId = Auth::id();

        // Her konuşmanın son mesajını al
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver', 'listing'])
            ->latest()
            ->get()
            ->unique(function ($msg) use ($userId) {
                $other = $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
                return $msg->listing_id . '_' . $other;
            });

        return view('messages.index', compact('conversations'));
    }

    // İlan sayfasından gelen butona özel fonksiyon
    public function chat($receiver_id, $listing_id)
    {
        $user = User::findOrFail($receiver_id);
        $listing = Listing::findOrFail($listing_id);
        
        // Bu fonksiyon aslında show ile aynı işi yapar, sadece parametreleri farklıdır
        return $this->show($user, $listing);
    }

    // Belirli konuşma
    public function show(User $user, Listing $listing)
    {
        $userId = Auth::id();
        $messages = Message::where('listing_id', $listing->id)
            ->where(function ($q) use ($userId, $user) {
                $q->where('sender_id', $userId)->where('receiver_id', $user->id);
                $q->orWhere('sender_id', $user->id)->where('receiver_id', $userId);
            })
            ->with(['sender', 'receiver'])
            ->oldest()
            ->get();

        // Okunmamışları okundu işaretle
        Message::where('listing_id', $listing->id)
            ->where('sender_id', $user->id)
            ->where('receiver_id', $userId)
            ->update(['is_read' => true]);

        return view('messages.show', compact('messages', 'user', 'listing'));
    }

    // Mesaj gönder
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'listing_id'  => 'required|exists:listings,id',
            'body'        => 'required|max:2000',
        ]);

        Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'listing_id'  => $request->listing_id,
            'body'        => $request->body,
        ]);

        // Sadece geri döner, başarı mesajı gönderilmez
        return back();
    }
}