<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
/** @var \App\Models\User $user */ // Global tanımlama için eklenebilir

class ProfileController extends Controller
{
    public function show()
    {
        /** @var \App\Models\User $user */
        $user     = Auth::user();
        $listings = $user->listings()->latest()->get();
        $favorites = $user->favorites()->with('listing.photos')->latest()->get();
        return view('profile.show', compact('user', 'listings', 'favorites'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $request->validate([
            'name'  => 'required|max:255',
            'phone' => 'nullable|max:20',
            'iban'  => 'nullable|max:34',
            'city'  => 'nullable|max:100',
        ]);
        $user->update($request->only(['name', 'phone', 'iban', 'city']));
        return back()->with('success', 'Profiliniz güncellendi.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'          => 'required',
            'password'                  => 'required|min:8|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mevcut şifre yanlış.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Şifreniz değiştirildi.');
    }
}