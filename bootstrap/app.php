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
        // Trust all proxies (Cloudflare + Render)
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
        
        // TEMPORARY: Exclude specific routes from CSRF protection for testing
        $middleware->validateCsrfTokens(except: [
            'gym_ni_bai-login',
            'gym_ni_bai-register', 
            'gym_ni_bai-management/login',
            'gym_ni_bai-management/signup'
        ]);
        
        // Redirect guests to management login by default
        $middleware->redirectGuestsTo(fn () => route('management.login'));
        
        // Redirect authenticated users to management dashboard by default
        $middleware->redirectUsersTo(fn () => route('management.dashboard'));
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
