<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingPhoto;
use App\Models\UserNotification;
use App\Models\Setting;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// 1. BURAYI EKLEDİK: Cloudinary kullanabilmek için gerekli
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; 

class ListingController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Listing::with(['user', 'photos'])->where('status', 'active');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        if ($request->sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $listings = $query->paginate(12);
        $categories = Category::all(); 

        if ($request->ajax()) {
            return response()->json([
                'html' => view('listings.partials._list', compact('listings'))->render()
            ]);
        }

        return view('listings.index', compact('listings', 'categories'));
    }

    public function show(Listing $listing)
    {
        $viewedKey = 'viewed_listing_' . $listing->id;
        if (Auth::id() !== $listing->user_id && !session()->has($viewedKey)) {
            $listing->increment('view_count');
            session()->put($viewedKey, true);
        }

        $listing->load(['user', 'photos', 'comments.user', 'category']);
        return view('listings.show', compact('listing'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->role !== 'admin' && Setting::get('disable_listings') == '1') {
            return redirect()->route('home')->with('error', 'Şu an sistem bakımı nedeniyle yeni ilan kabul edilmemektedir.');
        }

        if (!$user->iban) {
            return redirect()->route('profile.show')
                ->with('error', 'İlan verebilmek için önce IBAN numaranızı profilinize ekleyin.');
        }

        $categories = Category::all();
        return view('listings.create', compact('categories'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role !== 'admin' && Setting::get('disable_listings') == '1') {
            return redirect()->route('home')->with('error', 'Şu an yeni ilan verilememektedir.');
        }

        $request->validate([
            'title'        => 'required|max:255',
            'description'  => 'required',
            'category_id'  => 'required|exists:categories,id',
            'price'        => 'required|numeric|min:0|max:99999999', 
            'city'         => 'required',
            'district'     => 'required',
            'photos'       => 'nullable|array',
            'photos.*'     => 'image|max:2048',
            'lat'          => 'nullable|numeric',
            'lng'          => 'nullable|numeric',
        ]);

        $listing = $user->listings()->create($request->only([
            'title', 'description', 'price', 'city', 'district', 'category_id', 'lat', 'lng'
        ]));

        // 2. BURAYI GÜNCELLEDİK: Fotoğrafları Cloudinary'e yüklüyoruz
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $i => $photo) {
                // Eski kod: $path = $photo->store('listings', 'public');
                // Yeni kod: Cloudinary'e yükleyip direkt linkini alıyoruz
                $path = Cloudinary::upload($photo->getRealPath())->getSecurePath();
                
                ListingPhoto::create([
                    'listing_id' => $listing->id, 
                    'path' => $path, // Artık dosya yolu değil, internet linki (https://...) kayıt oluyor
                    'order' => $i
                ]);
            }
        }

        return redirect()->route('listings.show', $listing)
            ->with('success', 'İlanınız başarıyla ve kalıcı olarak yayınlandı!');
    }

    public function edit(Listing $listing)
    {
        $this->authorize('update', $listing);
        $categories = Category::all();
        return view('listings.edit', compact('listing', 'categories'));
    }

    public function update(Request $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        $request->validate([
            'title'        => 'required|max:255',
            'description'  => 'required',
            'category_id'  => 'required|exists:categories,id',
            'price'        => 'required|numeric|min:0|max:99999999',
            'city'         => 'required',
            'district'     => 'required',
            'status'       => 'required|in:active,passive,sold',
            'lat'          => 'nullable|numeric',
            'lng'          => 'nullable|numeric',
        ]);

        $listing->update($request->only([
            'title', 'description', 'price', 'city', 'district', 'category_id', 'status', 'lat', 'lng'
        ]));

        return redirect()->route('listings.show', $listing)
            ->with('success', 'İlan başarıyla güncellendi.');
    }

    public function destroy(Listing $listing)
    {
        $this->authorize('delete', $listing);

        // 3. NOT: Cloudinary'den silme işlemi için ek ayar gerekebilir ama 
        // şu an resimlerin silinmemesi önceliğimiz olduğu için buraya dokunmuyoruz.
        
        $listing->delete();

        return back()->with('success', 'İlan başarıyla silindi.');
    }
}