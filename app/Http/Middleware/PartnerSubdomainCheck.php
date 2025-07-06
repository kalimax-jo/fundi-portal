<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PartnerSubdomainCheck
{
    public function handle(Request $request, Closure $next)
    {
        // Just pass through for now
        return $next($request);
    }
} 