<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Yabancı anahtar kontrolünü kapat
        Schema::disableForeignKeyConstraints();
        
        // Tabloyu temizle
        DB::table('categories')->truncate();
        
        // Kontrolü tekrar aç
        Schema::enableForeignKeyConstraints();

        // Senin istediğin ve eklediğim mantıklı kategoriler
        $categories = [
            'Araba',
            'Motosiklet',
            'Bisiklet',
            'Arsa & Arazi',
            'Konut / Daire',
            'İşyeri / Ofis',
            'Elektronik',
            'Telefon / Tablet',
            'Bilgisayar',
            'Kıyafet & Moda',
            'Spor / Outdoor',
            'Ev / Mobilya',
            'İkinci El / Sıfır Alışveriş',
            'İş Makineleri',
            'Hobi / Oyun',
            'Evcil Hayvan',
            'Diğer'
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }
}