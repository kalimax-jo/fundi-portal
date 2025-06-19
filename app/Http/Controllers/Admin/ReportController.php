<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InspectionRequest;
use App\Models\InspectionPackage;
use App\Models\Inspector;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function inspectionRequests(Request $request)
    {
        $query = InspectionRequest::with(['package', 'assignedInspector.user', 'property']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }
        if ($request->filled('inspector_id')) {
            $query->where('assigned_inspector_id', $request->inspector_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        $packages = InspectionPackage::all();
        $inspectors = Inspector::with('user')->get();

        // Summary stats
        $stats = [
            'total' => InspectionRequest::count(),
            'completed' => InspectionRequest::where('status', 'completed')->count(),
            'pending' => InspectionRequest::where('status', 'pending')->count(),
            'cancelled' => InspectionRequest::where('status', 'cancelled')->count(),
        ];

        return view('admin.reports.inspection-requests', compact('requests', 'packages', 'inspectors', 'stats'));
    }
}
