<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        // Register layout views as components
        Blade::component('layouts.app', 'layouts.app');
        Blade::component('layouts.admin', 'layouts.admin');
        Blade::component('layouts.user', 'layouts.user');
    }
}
