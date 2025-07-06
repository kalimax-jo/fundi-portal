<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            $host = $request->getHost();
            $subdomain = explode('.', $host)[0];
            if ($subdomain !== 'www' && $subdomain !== 'localhost' && $subdomain !== 'admin') {
                return route('partner.login');
            }
            // Only use admin.login if it exists, otherwise fallback to /login
            if (\Route::has('admin.login')) {
                return route('admin.login');
            }
            return '/login';
        }
    }
} 