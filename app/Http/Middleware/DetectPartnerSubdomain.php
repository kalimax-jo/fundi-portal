<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BusinessPartner;

class DetectPartnerSubdomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $host = explode(':', $host)[0];
        $parts = explode('.', $host);
        $subdomain = $parts[0] ?? null;

        // Debug information
        \Log::info('DetectPartnerSubdomain middleware', [
            'host' => $host,
            'subdomain' => $subdomain,
            'parts' => $parts
        ]);

        if (in_array($subdomain, ['www', 'admin', 'localhost'])) {
            \Log::info('Subdomain is in main domains list, skipping partner detection');
            return $next($request);
        }

        $partner = BusinessPartner::where('subdomain', $subdomain)
            ->where('partnership_status', 'active')
            ->first();

        \Log::info('Partner lookup result', [
            'subdomain' => $subdomain,
            'partner_found' => $partner ? true : false,
            'partner_id' => $partner ? $partner->id : null,
            'partner_name' => $partner ? $partner->name : null
        ]);

        if (!$partner) {
            \Log::error('Business partner not found or inactive', [
                'subdomain' => $subdomain,
                'all_partners' => BusinessPartner::all(['id', 'name', 'subdomain', 'partnership_status'])->toArray()
            ]);
            abort(404, 'Business partner not found or inactive.');
        }

        $request->attributes->set('business_partner', $partner);
        session(['current_business_partner' => $partner->id]);

        \Log::info('Partner set in request attributes and session', [
            'partner_id' => $partner->id,
            'session_partner_id' => session('current_business_partner')
        ]);

        return $next($request);
    }
} 