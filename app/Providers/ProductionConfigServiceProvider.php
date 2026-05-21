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
        // Only apply configuration in production
        if ($this->app->environment('production')) {
            // Force HTTPS scheme
            URL::forceScheme('https');
            
            // Set the most basic session configuration that should work
            config([
                'session.driver' => 'file',
                'session.secure' => false, // Try without secure first
                'session.same_site' => 'lax',
                'session.domain' => null,
                'session.http_only' => true,
                'session.lifetime' => 120,
            ]);
            
            // Ensure app URL is HTTPS
            config(['app.url' => 'https://gym-ni-bai.onrender.com']);
        }
    }
}