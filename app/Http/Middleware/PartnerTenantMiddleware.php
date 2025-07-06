<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BusinessPartner;

class PartnerTenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $host = explode(':', $host)[0];
        $parts = explode('.', $host);
        $subdomain = $parts[0] ?? null;

        if (in_array($subdomain, ['www', 'admin', 'localhost'])) {
            return $next($request);
        }

        $partner = BusinessPartner::where('subdomain', $subdomain)
            ->where('partnership_status', 'active')
            ->first();

        if (!$partner) {
            abort(404, 'Business partner not found or inactive.');
        }

        $request->attributes->set('business_partner', $partner);
        session(['current_business_partner' => $partner->id]);

        return $next($request);
    }
} 