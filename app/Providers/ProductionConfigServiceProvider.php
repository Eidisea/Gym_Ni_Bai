<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class ProductionConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only run in production environment
        if ($this->app->environment('production')) {
            // Force HTTPS detection for session cookies
            URL::forceScheme('https');
            
            // Set session configuration for production (Cloudflare + Render)
            config([
                'session.secure' => true,
                'session.same_site' => 'none',
                'session.domain' => '.onrender.com',
                'session.http_only' => true,
            ]);
        }
    }
}