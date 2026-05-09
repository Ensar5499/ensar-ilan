<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Render üzerinde (production ortamında) linklerin ve formların 
         * "Güvenli Değil" hatası vermemesi için HTTPS zorunlu kılınır.
         */
        if (config('app.env') === 'production' || env('RENDER')) {
            URL::forceScheme('https');
        }
    }
}
