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
        Schema::table('listings', function (Blueprint $table) {
            // user_id sütunundan hemen sonra category_id sütununu ekler
            // constrained() sayesinde categories tablosuyla bağlantı kurar
            $table->foreignId('category_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            // Geri almak istersen önce bağlantıyı sonra sütunu siler
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};