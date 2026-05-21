<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        // Register your AuthServiceProvider so Gates & Policies load
        \App\Providers\AuthServiceProvider::class,
        // Register production configuration provider
        \App\Providers\ProductionConfigServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Re-enable proxy trust - this is CRITICAL for Cloudflare + Render
        $middleware->trustProxies(at: '*');
        
        // Trust specific proxy headers for Cloudflare + Render
        $middleware->trustProxies(
            at: '*',
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                    \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                    \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                    \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
                    \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
        );
        
        // Temporarily disable CSRF protection until session configuration is fixed
        $middleware->validateCsrfTokens(except: [
            '*' // Disable CSRF for all routes temporarily
        ]);
        
        // Session configuration will be handled by ProductionConfigServiceProvider
        
        // Authentication redirects are handled in individual controllers
        // No global redirects to avoid conflicts between customer/management portals
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log all exceptions for debugging
        $exceptions->reportable(function (Throwable $e) {
            \Log::error('Application Exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        });
    })->create();
