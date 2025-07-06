<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        // Register our custom Route Service Provider here
        \App\Providers\RouteServiceProvider::class,
    ])
    ->withRouting(
        // We remove the 'web' key because our provider now handles it.
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // Register the role and custom middleware
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'multi-tenancy' => \App\Http\Middleware\MultiTenancyMiddleware::class,
            'detect_partner_subdomain' => \App\Http\Middleware\DetectPartnerSubdomain::class,
            'partner_subdomain' => \App\Http\Middleware\DetectPartnerSubdomain::class,
            'partner_auth' => \App\Http\Middleware\PartnerAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();