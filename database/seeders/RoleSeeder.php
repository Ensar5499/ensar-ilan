<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// Spatie modelini içeri aktarıyoruz
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Önce 'user' rolünü oluşturalım (Kayıt işlemi için zorunlu)
        Role::updateOrCreate(
            ['name' => 'user', 'guard_name' => 'web']
        );

        // Bir de 'admin' rolünü oluşturalım (Panel yönetimi için)
        Role::updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web']
        );
    }
}