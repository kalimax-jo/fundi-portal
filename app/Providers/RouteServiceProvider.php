<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // Check if we have a request context (for CLI commands like route:list, we don't)
            if (app()->runningInConsole() && !app()->bound('request')) {
                // Load all routes for CLI commands
                Route::middleware('web')
                    ->group(base_path('routes/web.php'));
                Route::middleware('web')
                    ->group(base_path('routes/partner.php'));
            } else {
                $host = request()->getHost();
                
                // Remove port number if present
                $host = explode(':', $host)[0];
                
                $subdomain = explode('.', $host)[0];
                $mainDomains = ['localhost', '127.0.0.1', 'www', 'admin'];

                if (!in_array($subdomain, $mainDomains)) {
                    // Load partner routes for business partner subdomains
                    Route::middleware('web')
                        ->group(base_path('routes/partner.php'));
                } else {
                    // Load main web routes
                    Route::middleware('web')
                        ->group(base_path('routes/web.php'));
                }
            }

            // Always load API routes
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        });
    }
}