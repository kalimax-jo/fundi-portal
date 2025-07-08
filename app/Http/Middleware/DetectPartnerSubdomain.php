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

        // Log the host and its parts for debugging
        \Log::info('DetectPartnerSubdomain middleware', [
            'host' => $host,
            'parts' => $parts
        ]);

        /*
         * Only treat as partner subdomain if there are 4 parts in the host (e.g., bpr.fundi.electronova.rw)
         * - The first part (e.g., 'bpr') is considered the partner subdomain
         * - If the host has only 3 parts (e.g., fundi.electronova.rw), this is the main portal and partner detection is skipped
         * - 'www', 'admin', and 'localhost' are always skipped
         */
        if (count($parts) === 4 && !in_array($parts[0], ['www', 'admin', 'localhost'])) {
            $subdomain = $parts[0];

            // Attempt to find an active business partner with the given subdomain
            $partner = BusinessPartner::where('subdomain', $subdomain)
                ->where('partnership_status', 'active')
                ->first();

            \Log::info('Partner lookup result', [
                'subdomain' => $subdomain,
                'partner_found' => $partner ? true : false,
                'partner_id' => $partner ? $partner->id : null,
                'partner_name' => $partner ? $partner->name : null
            ]);

            // If no partner is found, abort with 404
            if (!$partner) {
                \Log::error('Business partner not found or inactive', [
                    'subdomain' => $subdomain,
                    'all_partners' => BusinessPartner::all(['id', 'name', 'subdomain', 'partnership_status'])->toArray()
                ]);
                abort(404, 'Business partner not found or inactive.');
            }

            // Attach the partner to the request and session for downstream use
            $request->attributes->set('business_partner', $partner);
            session(['current_business_partner' => $partner->id]);

            \Log::info('Partner set in request attributes and session', [
                'partner_id' => $partner->id,
                'session_partner_id' => session('current_business_partner')
            ]);
        }

        // If not a partner subdomain, just continue (main portal logic applies)
        return $next($request);
    }
} 