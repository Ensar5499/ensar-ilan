<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminListingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminComplaintController;
use App\Http\Controllers\CheckoutController;

// ─── ACİL DURUM KURULUM ROTASI ──────────────────────────────
Route::get('/ensar-kur', function() {
    try {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        
        $categories = [
            ['name' => 'Araba', 'slug' => 'araba'],
            ['name' => 'Motosiklet', 'slug' => 'motosiklet'],
            ['name' => 'Bisiklet', 'slug' => 'bisiklet'],
            ['name' => 'Arsa & Arazi', 'slug' => 'arsa-arazi'],
            ['name' => 'Konut / Daire', 'slug' => 'konut-daire'],
            ['name' => 'İşyeri / Ofis', 'slug' => 'isyeri-ofis'],
            ['name' => 'Elektronik', 'slug' => 'elektronik'],
            ['name' => 'Telefon / Tablet', 'slug' => 'telefon-tablet'],
            ['name' => 'Bilgisayar', 'slug' => 'bilgisayar'],
            ['name' => 'Kıyafet & Moda', 'slug' => 'kiyafet-moda'],
            ['name' => 'Spor / Outdoor', 'slug' => 'spor-outdoor'],
            ['name' => 'Ev / Mobilya', 'slug' => 'ev-mobilya'],
            ['name' => 'İkinci El', 'slug' => 'ikinci-el'],
            ['name' => 'İş Makineleri', 'slug' => 'is-makineleri'],
            ['name' => 'Hobi / Oyun', 'slug' => 'hobi-oyun'],
            ['name' => 'Evcil Hayvan', 'slug' => 'evcil-hayvan'],
            ['name' => 'Diğer', 'slug' => 'diger'],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        return "<h1>Süper!</h1> Kategoriler başarıyla zorla eklendi. Artık ilan verme hatası düzeldi. <br><br> <a href='/listings/create'>İlan Vererek Test Et</a>";
    } catch (\Exception $e) {
        return "<h1>Yine de bir hata var:</h1> " . $e->getMessage();
    }
});

// ─── Herkese açık sayfalar ─────────────────────────────────────
Route::get('/', [ListingController::class, 'index'])->name('home');
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index'); 
Route::get('/dashboard', function () { return redirect()->route('home'); })->name('dashboard'); 

// ─── Giriş yapan kullanıcılara özel ────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{receiver_id}/{listing_id}', [MessageController::class, 'chat'])->name('messages.chat');
    Route::post('/messages/send', [MessageController::class, 'store'])->name('messages.store');

    Route::post('/favorites/{listing}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    // --- YORUM VE ŞİKAYET GÜNCELLEMELERİ ---
    Route::post('/comments/{listing}', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update'); 
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy'); 
    
    // Şikayet rotasını ListingController'a bağladık
    Route::post('/listings/{listing}/report', [ListingController::class, 'report'])->name('listings.report'); 
    Route::post('/complaints/{listing}', [ComplaintController::class, 'store'])->name('complaints.store'); 

    Route::post('/checkout/pay', [CheckoutController::class, 'initiatePayment'])->name('checkout.pay');
    Route::get('/orders/success', [CheckoutController::class, 'success'])->name('orders.success');
});

Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');
Route::post('/webhook/nova', [App\Http\Controllers\WebhookController::class, 'handleNova'])->name('webhook.nova');

// ─── Sadece admin ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/listings', [AdminListingController::class, 'index'])->name('listings.index');
    Route::delete('/listings/{listing}', [AdminListingController::class, 'destroy'])->name('listings.destroy');
    Route::put('/listings/{listing}/status', [AdminListingController::class, 'updateStatus'])->name('listings.status');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    
    // Şikayetleri AdminController içindeki complaints metoduna yönlendirdik
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('complaints.index');
    Route::put('/complaints/{complaint}/resolve', [AdminComplaintController::class, 'resolve'])->name('complaints.resolve');
    Route::post('/settings/update', [AdminController::class, 'updateSetting'])->name('settings.update');
});

// ─── RENDER ÖZEL: FOTOĞRAF GÖSTERME ───────────────────────────
Route::get('/storage/listings/{filename}', function ($filename) {
    $path = storage_path('app/public/listings/' . $filename);
    if (!file_exists($path)) { abort(404); }
    $file = file_get_contents($path);
    $type = mime_content_type($path);
    return response($file)->header('Content-Type', $type);
});

require __DIR__.'/auth.php';