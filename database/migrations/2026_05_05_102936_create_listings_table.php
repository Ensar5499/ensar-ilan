<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Önce tabloyu anahtar bağımlılığı olmadan oluşturuyoruz
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Burayı sadece sütun olarak tanımlıyoruz, foreign demiyoruz (hata vermemesi için)
            $table->unsignedBigInteger('category_id')->index(); 
            
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 15, 2);
            $table->string('city');
            $table->string('district');
            
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            
            $table->enum('status', ['active', 'passive', 'sold'])->default('active');
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });

        // 2. EĞER categories tablosu varsa anahtarı bağla, yoksa hata verme
        // Bu kısım migration sonunda çalışırsa sorun kalmaz.
        if (Schema::hasTable('categories')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};