<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BusinessPartner;

class DetectPartnerSubdomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost(); // e.g., bpr.electronova.rw
        $mainDomain = 'electronova.rw';

        // Remove port if present
        $host = explode(':', $host)[0];

        // Check if the host ends with the main domain
        if (str_ends_with($host, $mainDomain)) {
            $subdomain = str_replace('.' . $mainDomain, '', $host);

            // Only set partner if subdomain is not empty and not 'www', 'admin', or 'localhost'
            if (!empty($subdomain) && !in_array($subdomain, ['www', 'admin', 'localhost'])) {
                $partner = BusinessPartner::where('subdomain', $subdomain)
                    ->where('partnership_status', 'active')
                    ->first();

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
        }

        return $next($request);
    }
} 