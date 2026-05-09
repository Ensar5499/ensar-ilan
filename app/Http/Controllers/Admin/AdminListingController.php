<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminListingController extends Controller
{
    public function index()
    {
        $listings = Listing::with(['user', 'photos'])->latest()->paginate(20);
        return view('admin.listings.index', compact('listings'));
    }

    public function destroy(Listing $listing)
    {
        foreach ($listing->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }
        $listing->delete();
        return back()->with('success', 'İlan silindi.');
    }

    public function updateStatus(Request $request, Listing $listing)
    {
        $request->validate(['status' => 'required|in:active,passive,sold']);
        $listing->update(['status' => $request->status]);
        return back()->with('success', 'İlan durumu güncellendi.');
    }
}
