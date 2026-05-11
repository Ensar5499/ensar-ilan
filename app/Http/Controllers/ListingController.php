<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingPhoto;
use App\Models\UserNotification;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Complaint; // Report yerine Complaint olarak düzeltildi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// SDK'yı doğrudan kullanmak için bunu ekledik
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

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

    /**
     * İlan detay sayfasını gösterir ve görüntülenme sayısını IP tabanlı kontrol eder.
     */
    public function show(Listing $listing)
    {
        // IP ve İlan ID kombinasyonuyla benzersiz bir anahtar oluştur
        $viewedKey = 'viewed_listing_' . $listing->id . '_ip_' . request()->ip();

        // ŞARTLAR: 
        // 1. Kullanıcı giriş yapmış olmalı (Auth::check())
        // 2. Kendi ilanına bakmıyor olmalı (Auth::id() !== $listing->user_id)
        // 3. Bu oturumda bu ilana ilk kez bakıyor olmalı (!session()->has($viewedKey))
        if (Auth::check() && Auth::id() !== $listing->user_id && !session()->has($viewedKey)) {
            $listing->increment('view_count');
            
            // Anahtarı oturuma kaydet
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

        if ($request->hasFile('photos')) {
            // Cloudinary yapılandırmasını el ile kuruyoruz
            Configuration::instance('cloudinary://598232723132484:bLim7bUknk5Y0ppMLmzCFwwFp6Y@dzoowxtjc?secure=true');
            $uploadApi = new UploadApi();

            foreach ($request->file('photos') as $i => $photo) {
                $upload = $uploadApi->upload($photo->getRealPath(), [
                    'folder' => 'listings'
                ]);
                
                $path = $upload['secure_url'];
                
                ListingPhoto::create([
                    'listing_id' => $listing->id, 
                    'path' => $path,
                    'order' => $i
                ]);
            }
        }

        return redirect()->route('listings.show', $listing)
            ->with('success', 'İlanınız başarıyla yayınlandı!');
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
        $listing->delete();
        return back()->with('success', 'İlan başarıyla silindi.');
    }

    public function report(Request $request, Listing $listing)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        Complaint::create([
            'user_id' => Auth::id(),
            'listing_id' => $listing->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Şikayetiniz başarıyla iletildi. İnceleme başlatılacaktır.');
    }
}