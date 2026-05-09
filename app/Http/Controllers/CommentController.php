<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Comment;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
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
}
