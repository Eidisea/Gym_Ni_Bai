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
        // Force HTTPS detection for session cookies in production
        if ($this->app->environment('production') || request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }
        
        // Set session configuration for Cloudflare + Render environment
        // This overrides the config values to ensure proper session handling
        if ($this->app->environment('production')) {
            config([
                'session.secure' => true,
                'session.same_site' => 'none',
                'session.domain' => null, // Use current domain instead of .onrender.com
                'session.http_only' => true,
                'session.partitioned' => false,
            ]);
            
            // Also ensure app URL is HTTPS
            if (!str_starts_with(config('app.url'), 'https://')) {
                config(['app.url' => 'https://gym-ni-bai.onrender.com']);
            }
        }
    }
}