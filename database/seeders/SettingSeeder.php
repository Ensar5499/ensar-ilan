<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting; // Modeli dahil ettik

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tanımlamak istediğimiz varsayılan ayarlar
        $settings = [
            [
                'key'   => 'maintenance_mode',
                'value' => '0', // 0: Kapalı, 1: Açık
            ],
            [
                'key'   => 'disable_listings',
                'value' => '0', // 0: İlan verilebilir, 1: İlan verme kapalı
            ],
        ];

        foreach ($settings as $setting) {
            // Eğer ayar daha önce eklenmişse tekrar eklemez, yoksa oluşturur
            Setting::updateOrCreate(
                ['key' => $setting['key']], 
                ['value' => $setting['value']]
            );
        }
    }
}