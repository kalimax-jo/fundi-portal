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
        $pendingRequests = \App\Models\InspectionRequest::with(['property', 'package', 'requester'])
            ->where('status', 'pending')
            ->orderByRaw("FIELD(urgency, 'emergency', 'urgent', 'normal')")
            ->orderBy('created_at')
            ->get();
        $inspectors = \App\Models\Inspector::with('user')
            ->orderBy('rating', 'desc')
            ->get();
        $availableCount = \App\Models\Inspector::available()->count();
        $busyCount = \App\Models\Inspector::busy()->count();
        $offlineCount = \App\Models\Inspector::offline()->count();
        $totalCount = \App\Models\Inspector::count();
        return view('headtech.assignments.index', compact('pendingRequests', 'inspectors', 'availableCount', 'busyCount', 'offlineCount', 'totalCount'));
    }
} 