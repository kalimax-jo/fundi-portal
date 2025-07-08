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

        \Log::info('DetectPartnerSubdomain middleware', [
            'host' => $host,
            'parts' => $parts
        ]);

        // Only treat as partner subdomain if there are 4 parts (e.g., bpr.fundi.electronova.rw)
        if (count($parts) === 4 && !in_array($parts[0], ['www', 'admin', 'localhost'])) {
            $subdomain = $parts[0];

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
        }

        // If not a partner subdomain, just continue (main portal)
        return $next($request);
    }
} 