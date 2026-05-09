<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'iban',
        'city',
        'role', // BURASI EKLENDİ: Artık role güncellenebilir.
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /**
     * Kullanıcının admin olup olmadığını kontrol eder.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Kullanıcının ilanları
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    // Kullanıcının favorileri
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // Kullanıcının bildirimleri
    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }
}