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
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (Cloudflare + Render double proxy setup)
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
        
        // Configure session for Cloudflare + Render environment
        if (app()->environment('production')) {
            // Force HTTPS detection for session cookies
            \URL::forceScheme('https');
            
            // Set session configuration for production
            config([
                'session.secure' => true,
                'session.same_site' => 'none',
                'session.domain' => '.onrender.com',
                'session.http_only' => true,
            ]);
        }
        
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
