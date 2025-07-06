<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BusinessPartner;

class MultiTenancyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the host from the request
        $host = $request->getHost();
        
        // Skip for localhost or main domain
        if ($host === 'localhost' || $host === '127.0.0.1' || $host === config('app.domain', 'fundi.info')) {
            return $next($request);
        }

        // Extract subdomain
        $subdomain = $this->extractSubdomain($host);
        
        if (!$subdomain) {
            // No subdomain found, continue with main application
            return $next($request);
        }

        // Find the business partner by subdomain
        $businessPartner = BusinessPartner::where('subdomain', $subdomain)->first();
        
        if ($businessPartner) {
            // Check if business partner is active
            if (!$businessPartner->is_active) {
                abort(403, 'This business partner account is inactive.');
            }
            
            // Store business partner in the request for use in controllers
            $request->attributes->set('business_partner', $businessPartner);
            
            // Also store in session for easy access
            session(['current_business_partner' => $businessPartner]);
            
            // Set partner-specific configuration
            $this->setPartnerConfiguration($businessPartner);
        }

        return $next($request);
    }

    /**
     * Extract subdomain from host
     */
    private function extractSubdomain(string $host): ?string
    {
        $parts = explode('.', $host);
        
        // If we have at least 2 parts and it's not localhost
        if (count($parts) >= 2 && !in_array($parts[0], ['www', 'localhost', '127'])) {
            return $parts[0];
        }
        
        return null;
    }

    /**
     * Set partner-specific configuration
     */
    private function setPartnerConfiguration(BusinessPartner $businessPartner): void
    {
        // Set partner-specific app name
        config(['app.name' => $businessPartner->name]);
        
        // Set partner-specific branding
        config([
            'app.partner' => [
                'id' => $businessPartner->id,
                'name' => $businessPartner->name,
                'subdomain' => $businessPartner->subdomain,
                'logo' => $businessPartner->logo ?? '',
                'favicon' => $businessPartner->favicon ?? '',
                'primary_color' => $businessPartner->primary_color ?? '',
                'secondary_color' => $businessPartner->secondary_color ?? '',
                'branding_settings' => $businessPartner->branding_settings ?? '',
                'feature_access' => $businessPartner->feature_access ?? '',
            ]
        ]);
    }
}
