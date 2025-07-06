<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // Add global middleware here if needed
        \App\Http\Middleware\MultiTenancyMiddleware::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            // Add web middleware here if needed
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $routeMiddleware = [
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'partner_auth' => \App\Http\Middleware\PartnerAuthenticate::class,
        'multitenancy' => 'App\\Http\\Middleware\\MultiTenancyMiddleware',
        'detect_partner_subdomain' => \App\Http\Middleware\DetectPartnerSubdomain::class,
        'partner_subdomain_check' => \App\Http\Middleware\PartnerSubdomainCheck::class,
        'partner_subdomain' => \App\Http\Middleware\PartnerTenantMiddleware::class,
        // Add other route middleware as needed
    ];

    public function __construct(...$args)
    {
        file_put_contents(storage_path('logs/kernel_loaded.log'), 'Kernel loaded at '.now().PHP_EOL, FILE_APPEND);
        parent::__construct(...$args);
    }
} 