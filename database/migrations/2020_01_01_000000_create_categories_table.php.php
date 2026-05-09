<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Kategori adı (Örn: Emlak, Vasıta)
            $table->string('slug')->unique(); // URL için (Örn: emlak, vasita)
            $table->string('icon')->nullable(); // İsteğe bağlı: Kategori ikonu (bi bi-house gibi)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};