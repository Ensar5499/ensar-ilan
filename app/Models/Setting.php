<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * Toplu atama yapılabilecek alanlar.
     * key: Ayarın adı (ör: maintenance_mode)
     * value: Ayarın değeri (ör: 1 veya 0)
     */
    protected $fillable = [
        'key', 
        'value'
    ];

    /**
     * İsteğe bağlı: Ayarı anahtarına göre hızlıca getirmek için statik bir yardımcı metod.
     * Kullanımı: Setting::get('maintenance_mode')
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}