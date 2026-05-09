<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\CheckoutController; // Bunu ekledik

// ─── Herkese açık sayfalar ─────────────────────────────────────
Route::get('/', [ListingController::class, 'index'])->name('home');
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index'); 

Route::get('/dashboard', function () { return redirect()->route('home'); })->name('dashboard'); 

// ─── Giriş yapan kullanıcılara özel ────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // İlan yönetimi
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');

    // Profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Mesajlaşma
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{receiver_id}/{listing_id}', [MessageController::class, 'chat'])->name('messages.chat');
    Route::post('/messages/send', [MessageController::class, 'store'])->name('messages.store');

    // Favoriler
    Route::post('/favorites/{listing}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    // Yorumlar
    Route::post('/comments/{listing}', [CommentController::class, 'store'])->name('comments.store');

    // Şikayetler
    Route::post('/complaints/{listing}', [ComplaintController::class, 'store'])->name('complaints.store');

    // Ödeme İşlemleri (Eklendi)
    Route::post('/checkout/pay', [CheckoutController::class, 'initiatePayment'])->name('checkout.pay');
    Route::get('/orders/success', [CheckoutController::class, 'success'])->name('orders.success');
});

// ─── Detay Sayfası (En Sonda Olmalı) ───────────────────────────
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');

// ─── Banka Bildirim (Webhook) ──────────────────────────────────
// Giriş zorunluluğu olmayan yere ekledik
Route::post('/webhook/nova', [App\Http\Controllers\WebhookController::class, 'handleNova'])->name('webhook.nova');

// ─── Sadece admin görebilir ─────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/listings', [AdminListingController::class, 'index'])->name('listings.index');
    Route::delete('/listings/{listing}', [AdminListingController::class, 'destroy'])->name('listings.destroy');
    Route::put('/listings/{listing}/status', [AdminListingController::class, 'updateStatus'])->name('listings.status');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/complaints', [AdminComplaintController::class, 'index'])->name('complaints.index');
    Route::put('/complaints/{complaint}/resolve', [AdminComplaintController::class, 'resolve'])->name('complaints.resolve');
    
    Route::post('/settings/update', [AdminController::class, 'updateSetting'])->name('settings.update');
});

require __DIR__.'/auth.php';