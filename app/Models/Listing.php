<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'category_id', 
        'title', 
        'description', 
        'price',
        'city', 
        'district', // Controller'dan gelen verinin kaydedilmesi için kritik
        'status', 
        'lat', 
        'lng',
    ];

    /**
     * Veri tiplerini otomatik dönüştürme (Casting)
     * Fiyat ve koordinatların matematiksel işlemlerde sorun çıkarmaması için.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'lat' => 'double',
        'lng' => 'double',
        'category_id' => 'integer',
    ];

    /**
     * İlanın hangi kullanıcıya ait olduğu
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * İlanın hangi kategoriye ait olduğu
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * İlanın fotoğrafları
     */
    public function photos()
    {
        return $this->hasMany(ListingPhoto::class)->orderBy('order');
    }

    /**
     * İlanın favorileri
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * İlan yorumları
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * İlan şikayetleri
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Kullanıcının bu ilanı favorileyip favorilemediği kontrolü
     */
    public function isFavoritedByUser($userId)
    {
        if (!$userId) return false;
        return $this->favorites()->where('user_id', $userId)->exists();
    }
}