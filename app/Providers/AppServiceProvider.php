<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
         * Buat 2 folder terpisah untuk public
         * Masukkan di boot appserviceprovider
         */
        // $this->app->bind('path.public', function(){
        //     return base_path().'/../public_html';
        // });
    }
}
