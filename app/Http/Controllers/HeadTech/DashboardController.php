<?php

namespace App\Http\Controllers\HeadTech;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Inspector stats
        $availableInspectors = \App\Models\Inspector::available()->count();
        $busyInspectors = \App\Models\Inspector::busy()->count();
        $offlineInspectors = \App\Models\Inspector::offline()->count();
        $totalInspectors = \App\Models\Inspector::count();
        // Inspection request stats
        $totalRequests = \App\Models\InspectionRequest::count();
        $pendingRequests = \App\Models\InspectionRequest::where('status', 'pending')->count();
        $assignedRequests = \App\Models\InspectionRequest::where('status', 'assigned')->count();
        $inProgressRequests = \App\Models\InspectionRequest::where('status', 'in_progress')->count();
        $completedRequests = \App\Models\InspectionRequest::where('status', 'completed')->count();
        $urgentRequests = \App\Models\InspectionRequest::whereIn('urgency', ['urgent', 'emergency'])->count();
        $recentActivity = \App\Models\InspectionStatusHistory::with(['inspectionRequest', 'changedByUser'])
            ->orderByDesc('changed_at')
            ->limit(10)
            ->get();
        return view('headtech.dashboard', compact(
            'availableInspectors', 'busyInspectors', 'offlineInspectors', 'totalInspectors',
            'totalRequests', 'pendingRequests', 'assignedRequests', 'inProgressRequests', 'completedRequests', 'urgentRequests',
            'recentActivity'
        ));
    }

    public function assignments()
    {
        // Debug: Check if user is authenticated and has correct role
        $user = auth()->user();
        if (!$user) {
            abort(401, 'User not authenticated');
        }
        
        if (!$user->isHeadTechnician() && !$user->isAdmin()) {
            abort(403, 'Access denied. Head Technician privileges required.');
        }

        // Fetch all requests, ordered by status and urgency
        $activities = \App\Models\InspectionRequest::with(['property', 'package', 'requester', 'inspector.user'])
            ->orderByRaw("
                CASE 
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'assigned' THEN 2
                    WHEN status = 'in_progress' THEN 3
                    ELSE 4
                END
            ")
            ->orderByRaw("FIELD(urgency, 'emergency', 'urgent', 'normal')")
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch inspectors for the assignment dropdowns
        $inspectors = \App\Models\Inspector::with('user')->available()->get();
        $allInspectors = \App\Models\Inspector::with('user')->get();

        // Fetch recent activity for the sidebar
        $recentActivity = \App\Models\InspectionStatusHistory::with(['inspectionRequest', 'changedByUser'])
            ->orderByDesc('changed_at')
            ->limit(15)
            ->get();

        // Calculate statistics
        $stats = [
            'total_requests' => \App\Models\InspectionRequest::count(),
            'pending_requests' => \App\Models\InspectionRequest::where('status', 'pending')->count(),
            'assigned_requests' => \App\Models\InspectionRequest::where('status', 'assigned')->count(),
            'in_progress_requests' => \App\Models\InspectionRequest::where('status', 'in_progress')->count(),
            'completed_requests' => \App\Models\InspectionRequest::where('status', 'completed')->count(),
        ];

        return view('headtech.assignments.index', [
            'activities' => $activities,
            'inspectors' => $inspectors,
            'allInspectors' => $allInspectors,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }

    public function profile()
    {
        $user = auth()->user();
        return view('headtech.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        $user->update($request->only('first_name', 'last_name', 'email', 'phone'));
        return redirect()->route('headtech.profile')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user->update([
            'password' => bcrypt($request->new_password),
        ]);
        return redirect()->route('headtech.profile')->with('success', 'Password updated successfully.');
    }
} 