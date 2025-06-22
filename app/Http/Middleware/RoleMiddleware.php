<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $roles)
    {
        $roleList = explode(',', $roles);
        if (!Auth::check() || !collect($roleList)->contains(fn($role) => $request->user()->hasRole(trim($role)))) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
} 