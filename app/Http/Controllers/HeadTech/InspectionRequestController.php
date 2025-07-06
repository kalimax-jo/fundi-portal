<?php

namespace App\Http\Controllers\HeadTech;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InspectionRequest;
use App\Models\Inspector;
use App\Models\InspectionStatusHistory;
use Barryvdh\DomPDF\Facade\Pdf;

class InspectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\InspectionRequest::with([
            'requester',
            'property',
            'package',
            'inspector.user',
            'businessPartner'
        ]);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhereHas('requester', function ($subQ) use ($search) {
                      $subQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('property', function ($subQ) use ($search) {
                      $subQ->where('address', 'like', "%{$search}%")
                           ->orWhere('property_code', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by requester type
        if ($request->has('requester_type') && $request->requester_type) {
            $query->where('requester_type', $request->requester_type);
        }

        // Filter by urgency
        if ($request->has('urgency') && $request->urgency) {
            $query->where('urgency', $request->urgency);
        }

        // Filter by business partner
        if ($request->has('business_partner') && $request->business_partner) {
            $query->where('business_partner_id', $request->business_partner);
        }

        // Filter by inspector
        if ($request->has('inspector') && $request->inspector) {
            $query->where('assigned_inspector_id', $request->inspector);
        }

        // Date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        if ($sortBy === 'urgency') {
            $query->orderByRaw("FIELD(urgency, 'emergency', 'urgent', 'normal')");
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        $requests = $query->paginate(20)->withQueryString();

        // Get filter options
        $businessPartners = \App\Models\BusinessPartner::active()->pluck('name', 'id');
        $inspectors = \App\Models\Inspector::with('user')->get();
        $packages = \App\Models\InspectionPackage::active()->pluck('display_name', 'id');

        // Calculate statistics
        $stats = [
            'total_requests' => \App\Models\InspectionRequest::count(),
            'pending_requests' => \App\Models\InspectionRequest::where('status', 'pending')->count(),
            'assigned_requests' => \App\Models\InspectionRequest::where('status', 'assigned')->count(),
            'in_progress_requests' => \App\Models\InspectionRequest::where('status', 'in_progress')->count(),
            'completed_requests' => \App\Models\InspectionRequest::where('status', 'completed')->count(),
            'urgent_requests' => \App\Models\InspectionRequest::whereIn('urgency', ['urgent', 'emergency'])->count(),
        ];

        return view('headtech.inspection-requests.index', compact(
            'requests',
            'stats',
            'businessPartners',
            'inspectors',
            'packages'
        ));
    }

    public function create()
    {
        // You may want to pass packages, properties, etc.
        return view('headtech.inspection-requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'package_id' => 'required|exists:inspection_packages,id',
            'purpose' => 'required|string',
            'urgency' => 'required|in:normal,urgent,emergency',
            'preferred_date' => 'required|date|after:today',
            'preferred_time_slot' => 'required|in:morning,afternoon,evening,flexible',
        ]);
        $validated['requester_type'] = 'individual'; // or business_partner
        $validated['requester_user_id'] = auth()->id();
        $validated['status'] = 'pending';
        InspectionRequest::create($validated);
        return redirect()->route('headtech.inspection-requests.index')->with('success', 'Inspection request created.');
    }

    public function show(InspectionRequest $inspectionRequest)
    {
        $inspectionRequest->load(['property', 'package', 'inspector.user']);
        return view('headtech.inspection-requests.show', compact('inspectionRequest'));
    }

    public function edit(InspectionRequest $inspectionRequest)
    {
        $inspectionRequest->load(['property', 'package']);
        return view('headtech.inspection-requests.edit', compact('inspectionRequest'));
    }

    public function update(Request $request, InspectionRequest $inspectionRequest)
    {
        $validated = $request->validate([
            'purpose' => 'required|string',
            'urgency' => 'required|in:normal,urgent,emergency',
            'preferred_date' => 'required|date|after:today',
            'preferred_time_slot' => 'required|in:morning,afternoon,evening,flexible',
        ]);
        $inspectionRequest->update($validated);
        return redirect()->route('headtech.inspection-requests.index')->with('success', 'Inspection request updated.');
    }

    public function destroy(InspectionRequest $inspectionRequest)
    {
        $inspectionRequest->delete();
        return redirect()->route('headtech.inspection-requests.index')->with('success', 'Inspection request deleted.');
    }

    public function assignInspector(Request $request, InspectionRequest $inspectionRequest)
    {
        $validator = \Validator::make($request->all(), [
            'inspector_id' => 'required|exists:inspectors,id',
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $inspector = Inspector::findOrFail($request->inspector_id);
            if ($inspector->availability_status === 'offline') {
                throw new \Exception('Inspector is offline and cannot be assigned.');
            }

            // Use the model's assignInspector method
            $inspectionRequest->assignInspector($inspector, auth()->user());
            
            // Update scheduling information
            $inspectionRequest->update([
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
            ]);

            \DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inspector assigned successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Inspector assigned successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to assign inspector: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to assign inspector: ' . $e->getMessage())->withInput();
        }
    }

    public function reassignInspector(Request $request, InspectionRequest $inspectionRequest)
    {
        $request->validate([
            'assigned_inspector_id' => 'required|exists:inspectors,id',
        ]);

        $originalInspector = $inspectionRequest->inspector;
        $newInspector = Inspector::findOrFail($request->input('assigned_inspector_id'));

        // Update the inspector
        $inspectionRequest->assigned_inspector_id = $newInspector->id;
        $inspectionRequest->save();

        // Log the reassignment with inspector names
        $originalName = $originalInspector ? $originalInspector->user->full_name : 'Unassigned';
        $newName = $newInspector->user->full_name;
        
        $inspectionRequest->recordStatusChange(
            $inspectionRequest->status, 
            $inspectionRequest->status, 
            auth()->id(),
            "Inspector reassigned from {$originalName} to {$newName}"
        );

        return redirect()->route('headtech.assignments.index')->with('success', 'Inspector has been successfully reassigned.');
    }

    public function downloadReport(InspectionRequest $inspectionRequest)
    {
        $report = $inspectionRequest->report()->with('inspectionRequest.property', 'inspectionRequest.requester', 'inspectionRequest.package.services')->firstOrFail();

        if ($report->status !== 'completed') {
            return redirect()->back()->with('error', 'Only completed reports can be downloaded.');
        }
        
        $services = $report->inspectionRequest->package->services;

        $pdf = Pdf::loadView('inspectors.reports.pdf', compact('report', 'services'));
        
        return $pdf->download('inspection-report-'.$report->inspectionRequest->request_number.'.pdf');
    }
} 