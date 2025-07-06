<?php

namespace App\Helpers;

class PartnerAccess
{
    public static function can($action, $partner, $user = null)
    {
        $user = $user ?: auth()->user();
        $pivot = $partner->users()->where('users.id', $user->id)->first()->pivot ?? null;
        if (!$pivot) return false;

        // Enhanced logic for inspection request creation
        if ($action === 'create_request') {
            // Only admin/user roles
            if (!in_array($pivot->access_level, ['admin', 'user'])) return false;
            // Check for active tier
            $activeTier = \App\Models\PartnerTier::where('business_partner_id', $partner->id)
                ->where('status', 'active')
                ->with('tier.inspectionPackages')
                ->latest('started_at')
                ->first();
            if (!$activeTier) return false;
            // Check quota
            $quota = $activeTier->tier->request_quota;
            $used = $partner->inspectionRequests()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            if ($used >= $quota) return false;
            // Check allowed packages
            if ($activeTier->tier->inspectionPackages->isEmpty()) return false;
            return true;
        }

        $permissions = [
            'manage_users' => ['admin'],
            'create_user' => ['admin'],
            'edit_user' => ['admin'],
            'remove_user' => ['admin'],
            'create_request' => ['admin', 'user'],
            'view_request' => ['admin', 'user', 'viewer'],
            // Add more as needed
        ];
        return in_array($pivot->access_level, $permissions[$action] ?? []);
    }
} 