<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // أضف هذا السطر هنا

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
        // أضف هذا الجزء لإجبار الموقع على استخدام HTTPS
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}