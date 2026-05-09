<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Toplu atama yapılabilecek alanlar.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Kategoriye ait ilanları getirir.
     * (İleride kullanmak istersen ilişkiyi şimdiden ekledim)
     */
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}