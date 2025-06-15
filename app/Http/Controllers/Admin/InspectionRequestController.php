<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InspectionRequest;
use App\Models\Property;
use App\Models\InspectionPackage;
use App\Models\Inspector;
use App\Models\BusinessPartner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InspectionRequestController extends Controller
{
    /**
     * Display a listing of inspection requests
     */
    public function index(Request $request)
    {
        $query = InspectionRequest::with([
            'requester', 
            'property', 
            'package', 
            'assignedInspector.user',
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
        
        // Handle special sorting
        if ($sortBy === 'urgency') {
            $query->orderByRaw("FIELD(urgency, 'emergency', 'urgent', 'normal')");
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        $requests = $query->paginate(20)->withQueryString();

        // Get filter options
        $businessPartners = BusinessPartner::active()->pluck('name', 'id');
        $inspectors = Inspector::with('user')->get()->pluck('user.full_name', 'id');
        $packages = InspectionPackage::active()->pluck('display_name', 'id');

        // Calculate statistics
        $stats = [
            'total_requests' => InspectionRequest::count(),
            'pending_requests' => InspectionRequest::where('status', 'pending')->count(),
            'assigned_requests' => InspectionRequest::where('status', 'assigned')->count(),
            'in_progress_requests' => InspectionRequest::where('status', 'in_progress')->count(),
            'completed_requests' => InspectionRequest::where('status', 'completed')->count(),
            'urgent_requests' => InspectionRequest::whereIn('urgency', ['urgent', 'emergency'])->count(),
        ];

        return view('admin.inspection-requests.index', compact(
            'requests', 
            'stats', 
            'businessPartners', 
            'inspectors', 
            'packages'
        ));
    }

    /**
     * Show the form for creating a new inspection request
     */
    public function create()
    {
        $properties = Property::all();
        $packages = InspectionPackage::active()->get();
        $businessPartners = BusinessPartner::active()->get();

        // Active individual clients
        $individualUsers = User::active()->byRole('individual_client')->get();

        // Map of partner ID => active users for form dropdowns
        $businessPartnerUsers = [];
        foreach ($businessPartners as $partner) {
            $businessPartnerUsers[$partner->id] = $partner->users()
                ->active()
                ->get()
                ->map(function (User $user) {
                    return [
                        'id' => $user->id,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                    ];
                });
        }

        return view('admin.inspection-requests.create', compact(
            'properties',
            'packages',
            'businessPartners',
            'individualUsers',
            'businessPartnerUsers'
        ));
    }

    /**
     * Store a newly created inspection request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'requester_type' => 'required|in:individual,business_partner',
            'requester_user_id' => 'required|exists:users,id',
            'business_partner_id' => 'nullable|exists:business_partners,id',
            'property_id' => 'required|exists:properties,id',
            'package_id' => 'required|exists:inspection_packages,id',
            'purpose' => 'required|in:rental,sale,purchase,loan_collateral,insurance,maintenance,other',
            'urgency' => 'required|in:normal,urgent,emergency',
            'preferred_date' => 'nullable|date|after:today',
            'preferred_time_slot' => 'required|in:morning,afternoon,evening,flexible',
            'special_instructions' => 'nullable|string|max:1000',
            'loan_amount' => 'nullable|numeric|min:0',
            'loan_reference' => 'nullable|string|max:100',
            'applicant_name' => 'nullable|string|max:255',
            'applicant_phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Generate request number
            $requestNumber = $this->generateRequestNumber();

            // Calculate total cost
            $package = InspectionPackage::findOrFail($request->package_id);
            $totalCost = $package->price;

            // Apply business partner discount if applicable
            if ($request->business_partner_id) {
                $businessPartner = BusinessPartner::findOrFail($request->business_partner_id);
                if ($businessPartner->discount_percentage > 0) {
                    $totalCost = $totalCost * (1 - ($businessPartner->discount_percentage / 100));
                }
            }

            // Create inspection request
            $inspectionRequest = InspectionRequest::create([
                'request_number' => $requestNumber,
                'requester_type' => $request->requester_type,
                'requester_user_id' => $request->requester_user_id,
                'business_partner_id' => $request->business_partner_id,
                'property_id' => $request->property_id,
                'package_id' => $request->package_id,
                'purpose' => $request->purpose,
                'urgency' => $request->urgency,
                'preferred_date' => $request->preferred_date,
                'preferred_time_slot' => $request->preferred_time_slot,
                'special_instructions' => $request->special_instructions,
                'loan_amount' => $request->loan_amount,
                'loan_reference' => $request->loan_reference,
                'applicant_name' => $request->applicant_name,
                'applicant_phone' => $request->applicant_phone,
                'total_cost' => $totalCost,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('admin.inspection-requests.show', $inspectionRequest)
                ->with('success', 'Inspection request created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create inspection request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified inspection request
     */
    public function show(InspectionRequest $inspectionRequest)
    {
        $inspectionRequest->load([
            'requester',
            'property',
            'package',
            'assignedInspector.user',
            'businessPartner',
            'payments'
        ]);

        // Get available inspectors for assignment
        $availableInspectors = Inspector::with('user')
            ->where('availability_status', 'available')
            ->get();

        return view('admin.inspection-requests.show', compact(
            'inspectionRequest', 
            'availableInspectors'
        ));
    }

    /**
     * Show the form for editing the inspection request
     */
    public function edit(InspectionRequest $inspectionRequest)
    {
        $properties = Property::all();
        $packages = InspectionPackage::active()->get();
        $businessPartners = BusinessPartner::active()->get();
        $users = User::where('status', 'active')->get();

        return view('admin.inspection-requests.edit', compact(
            'inspectionRequest',
            'properties', 
            'packages', 
            'businessPartners', 
            'users'
        ));
    }

    /**
     * Update the specified inspection request
     */
    public function update(Request $request, InspectionRequest $inspectionRequest)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'package_id' => 'required|exists:inspection_packages,id',
            'purpose' => 'required|in:rental,sale,purchase,loan_collateral,insurance,maintenance,other',
            'urgency' => 'required|in:normal,urgent,emergency',
            'preferred_date' => 'nullable|date',
            'preferred_time_slot' => 'required|in:morning,afternoon,evening,flexible',
            'special_instructions' => 'nullable|string|max:1000',
            'loan_amount' => 'nullable|numeric|min:0',
            'loan_reference' => 'nullable|string|max:100',
            'applicant_name' => 'nullable|string|max:255',
            'applicant_phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $inspectionRequest->update($request->only([
                'property_id', 'package_id', 'purpose', 'urgency',
                'preferred_date', 'preferred_time_slot', 'special_instructions',
                'loan_amount', 'loan_reference', 'applicant_name', 'applicant_phone'
            ]));

            return redirect()->route('admin.inspection-requests.show', $inspectionRequest)
                ->with('success', 'Inspection request updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update inspection request: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified inspection request
     */
    public function destroy(InspectionRequest $inspectionRequest)
    {
        try {
            // Check if request can be deleted
            if (in_array($inspectionRequest->status, ['in_progress', 'completed'])) {
                return redirect()->back()
                    ->with('error', 'Cannot delete inspection request that is in progress or completed.');
            }

            $inspectionRequest->delete();

            return redirect()->route('admin.inspection-requests.index')
                ->with('success', 'Inspection request deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete inspection request: ' . $e->getMessage());
        }
    }

    /**
     * Show pending inspection requests
     */
    public function pending(Request $request)
    {
        $query = InspectionRequest::with([
            'requester', 
            'property', 
            'package', 
            'businessPartner'
        ])->where('status', 'pending');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhereHas('requester', function ($subQ) use ($search) {
                      $subQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort by urgency and creation date
        $requests = $query->orderByRaw("FIELD(urgency, 'emergency', 'urgent', 'normal')")
                         ->orderBy('created_at', 'asc')
                         ->paginate(20)
                         ->withQueryString();

        return view('admin.inspection-requests.pending', compact('requests'));
    }

    /**
     * Show assignment interface
     */
    public function assign(Request $request)
    {
        // Get pending requests
        $pendingRequests = InspectionRequest::with([
            'requester', 
            'property', 
            'package',
            'businessPartner'
        ])
        ->where('status', 'pending')
        ->orderByRaw("FIELD(urgency, 'emergency', 'urgent', 'normal')")
        ->orderBy('created_at')
        ->get();

        // Get available inspectors
        $availableInspectors = Inspector::with(['user'])
            ->where('availability_status', 'available')
            ->orderBy('rating', 'desc')
            ->get();

        // Get assignment statistics
        $stats = [
            'pending_requests' => $pendingRequests->count(),
            'available_inspectors' => $availableInspectors->count(),
            'urgent_requests' => $pendingRequests->whereIn('urgency', ['urgent', 'emergency'])->count(),
        ];

        return view('admin.inspection-requests.assign', compact(
            'pendingRequests', 
            'availableInspectors', 
            'stats'
        ));
    }

    /**
     * Assign inspector to inspection request
     */
    public function assignInspector(Request $request, InspectionRequest $inspectionRequest)
    {
        $validator = Validator::make($request->all(), [
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
            DB::beginTransaction();

            // Check if inspector is available
            $inspector = Inspector::findOrFail($request->inspector_id);
            if ($inspector->availability_status !== 'available') {
                throw new \Exception('Inspector is not available for assignment.');
            }

            // Update inspection request
            $inspectionRequest->update([
                'assigned_inspector_id' => $request->inspector_id,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'status' => 'assigned'
            ]);

            // Update inspector status
            $inspector->update(['availability_status' => 'busy']);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inspector assigned successfully.'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Inspector assigned successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to assign inspector: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to assign inspector: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update inspection request status
     */
    public function updateStatus(Request $request, InspectionRequest $inspectionRequest)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,assigned,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $oldStatus = $inspectionRequest->status;
            $newStatus = $request->status;

            // Update status
            $inspectionRequest->update(['status' => $newStatus]);

            // Handle status-specific logic
            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                $inspectionRequest->update(['completed_at' => now()]);
                
                // Free up inspector
                if ($inspectionRequest->assignedInspector) {
                    $inspectionRequest->assignedInspector->update(['availability_status' => 'available']);
                }
            }

            if ($newStatus === 'in_progress' && $oldStatus !== 'in_progress') {
                $inspectionRequest->update(['started_at' => now()]);
            }

            if ($newStatus === 'cancelled') {
                // Free up inspector
                if ($inspectionRequest->assignedInspector) {
                    $inspectionRequest->assignedInspector->update(['availability_status' => 'available']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods

    /**
     * Generate unique request number
     */
    private function generateRequestNumber(): string
    {
        $date = Carbon::now()->format('Ymd');
        $sequence = InspectionRequest::whereDate('created_at', Carbon::today())->count() + 1;
        return 'REQ' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}