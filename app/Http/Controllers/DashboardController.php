<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InspectionRequest;
use App\Models\InspectionPackage;
use App\Models\Property;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get user's inspection requests
        $inspectionRequests = $user->inspectionRequests()
            ->with(['package', 'inspector.user', 'property', 'report'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_requests' => $inspectionRequests->count(),
            'pending_requests' => $inspectionRequests->where('status', 'pending')->count(),
            'in_progress_requests' => $inspectionRequests->whereIn('status', ['assigned', 'in_progress'])->count(),
            'completed_requests' => $inspectionRequests->where('status', 'completed')->count(),
            'cancelled_requests' => $inspectionRequests->where('status', 'cancelled')->count(),
            'total_spent' => $inspectionRequests->sum('total_cost'),
            'urgent_requests' => $inspectionRequests->whereIn('urgency', ['urgent', 'emergency'])->count(),
        ];

        // Get recent requests (last 5)
        $recentRequests = $inspectionRequests->take(5);

        // Get available packages for quick request
        $availablePackages = InspectionPackage::where('is_active', true)
            ->where(function($query) {
                $query->where('target_client_type', 'individual')
                      ->orWhere('target_client_type', 'both');
            })
            ->get();

        // Get user's properties
        $userProperties = Property::where('owner_email', $user->email)
            ->orWhere('owner_phone', $user->phone)
            ->get();

        // Get monthly trends
        $monthlyTrends = $this->getMonthlyTrends($user);

        return view('dashboard', compact(
            'stats',
            'recentRequests', 
            'availablePackages',
            'userProperties',
            'monthlyTrends'
        ));
    }

    /**
     * Get monthly trends for the user
     */
    private function getMonthlyTrends($user)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $currentMonthRequests = $user->inspectionRequests()
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();

        $lastMonthRequests = $user->inspectionRequests()
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $currentMonthSpent = $user->inspectionRequests()
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('total_cost');

        $lastMonthSpent = $user->inspectionRequests()
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('total_cost');

        return [
            'requests' => [
                'current' => $currentMonthRequests,
                'last' => $lastMonthRequests,
                'change' => $lastMonthRequests > 0 ? (($currentMonthRequests - $lastMonthRequests) / $lastMonthRequests) * 100 : 0
            ],
            'spending' => [
                'current' => $currentMonthSpent,
                'last' => $lastMonthSpent,
                'change' => $lastMonthSpent > 0 ? (($currentMonthSpent - $lastMonthSpent) / $lastMonthSpent) * 100 : 0
            ]
        ];
    }
}
