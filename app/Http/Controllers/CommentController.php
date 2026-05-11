<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Comment;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    // Yorum Kaydetme
    public function store(Request $request, Listing $listing)
    {
        $request->validate(['body' => 'required|max:1000']);

        $comment = Comment::create([
            'user_id'    => Auth::id(),
            'listing_id' => $listing->id,
            'body'       => $request->body,
        ]);

        // İlan sahibine bildirim
        if ($listing->user_id !== Auth::id()) {
            UserNotification::create([
                'user_id' => $listing->user_id,
                'type'    => 'comment',
                'message' => Auth::user()->name . ' ilanınıza yorum yaptı.',
                'link'    => route('listings.show', $listing),
            ]);
        }

        return back()->with('success', 'Yorum eklendi.');
    }

    // Yorum Güncelleme
    public function update(Request $request, Comment $comment)
    {
        // Sadece yorum sahibi veya admin düzenleyebilir
        if (Auth::id() !== $comment->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate(['body' => 'required|max:1000']);

        $comment->update([
            'body' => $request->body
        ]);

        return back()->with('success', 'Yorum güncellendi.');
    }

    // Yorum Silme
    public function destroy(Comment $comment)
    {
        // Sadece yorum sahibi veya admin silebilir
        if (Auth::id() !== $comment->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Yorum silindi.');
    }

    // İlan Şikayet Etme (Eksik olan kısım)
    public function report(Request $request, Listing $listing)
    {
        $request->validate(['reason' => 'required|max:500']);

        // Eğer Report modelin varsa oraya kayıt atabilirsin. 
        // Şimdilik adminlere bildirim gitsin mantığı kuralım:
        UserNotification::create([
            'user_id' => 1, // Admin ID (Veritabanında admin ID'si kaçsa o)
            'type'    => 'report',
            'message' => 'Bir ilan şikayet edildi: ' . $listing->title . '. Sebep: ' . $request->reason,
            'link'    => route('listings.show', $listing),
        ]);

        return back()->with('success', 'Şikayetiniz incelenmek üzere iletildi.');
    }
}