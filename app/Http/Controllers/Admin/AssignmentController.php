<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inspector;
use App\Models\InspectionRequest;
use App\Models\Property;
use App\Models\InspectionPackage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    /**
     * Show the assignment workflow interface
     */
    public function index()
    {
        // Get available inspectors
        $inspectors = Inspector::with(['user'])
            ->where('availability_status', '!=', 'offline')
            ->orderBy('availability_status')
            ->orderBy('rating', 'desc')
            ->get();

        // Get pending inspection requests
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

        // Get assignment statistics
        $stats = [
            'pending_requests' => InspectionRequest::where('status', 'pending')->count(),
            'available_inspectors' => Inspector::where('availability_status', 'available')->count(),
            'urgent_requests' => InspectionRequest::where('status', 'pending')
                ->whereIn('urgency', ['urgent', 'emergency'])->count(),
            'assigned_today' => InspectionRequest::where('status', 'assigned')
                ->whereDate('assigned_at', today())->count(),
            'busy_inspectors' => Inspector::where('availability_status', 'busy')->count(),
            'total_inspectors' => Inspector::count()
        ];

        return view('admin.assignments.index', compact('inspectors', 'pendingRequests', 'stats'));
    }

    /**
     * Assign an inspector to a request
     */
    public function assign(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'request_id' => 'required|integer|exists:inspection_requests,id',
            'inspector_id' => 'required|integer|exists:inspectors,id',
            'scheduled_date' => 'nullable|date|after:today',
            'scheduled_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000'
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

            // Get the inspection request and inspector
            $inspectionRequest = InspectionRequest::with(['requester', 'property', 'package'])
                ->findOrFail($request->request_id);
            $inspector = Inspector::with('user')->findOrFail($request->inspector_id);

            // Check if request can be assigned
            if (!$inspectionRequest->canBeAssigned()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This request cannot be assigned. Current status: ' . $inspectionRequest->status
                ], 400);
            }

            // Check if inspector is available
            if ($inspector->availability_status !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'Inspector is not available for assignment'
                ], 400);
            }

            // Assign inspector using the model method
            $inspectionRequest->assignInspector($inspector, auth()->user());

            // Schedule if date/time provided
            if ($request->scheduled_date) {
                $scheduledDate = Carbon::parse($request->scheduled_date);
                $scheduledTime = $request->scheduled_time ? 
                    Carbon::parse($request->scheduled_date . ' ' . $request->scheduled_time) : 
                    Carbon::parse($request->scheduled_date . ' 09:00');
                
                $inspectionRequest->schedule($scheduledDate, $scheduledTime);
            }

            // Add notes if provided
            if ($request->notes) {
                $inspectionRequest->recordStatusChange(
                    'assigned', 
                    'assigned', 
                    auth()->id(), 
                    'Assignment notes: ' . $request->notes
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inspector assigned successfully',
                'data' => [
                    'request' => [
                        'id' => $inspectionRequest->id,
                        'request_number' => $inspectionRequest->request_number,
                        'property_address' => $inspectionRequest->property->address,
                        'status' => $inspectionRequest->status
                    ],
                    'inspector' => [
                        'id' => $inspector->id,
                        'name' => $inspector->user->full_name,
                        'code' => $inspector->inspector_code
                    ],
                    'schedule' => [
                        'date' => $inspectionRequest->scheduled_date,
                        'time' => $inspectionRequest->scheduled_time
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Assignment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-assign pending requests to best available inspectors
     */
    public function autoAssign(Request $request)
    {
        try {
            DB::beginTransaction();

            // Get pending requests ordered by urgency
            $pendingRequests = InspectionRequest::with(['property', 'package'])
                ->where('status', 'pending')
                ->orderByRaw("FIELD(urgency, 'emergency', 'urgent', 'normal')")
                ->orderBy('created_at')
                ->get();

            if ($pendingRequests->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending requests found for auto-assignment'
                ], 400);
            }

            // Get available inspectors ordered by rating and workload
            $availableInspectors = Inspector::with('user')
                ->where('availability_status', 'available')
                ->orderByDesc('rating')
                ->orderBy('total_inspections') // Prefer inspectors with fewer jobs for balance
                ->get();

            if ($availableInspectors->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No available inspectors for auto-assignment'
                ], 400);
            }

            $assignedCount = 0;
            $inspectorIndex = 0;

            foreach ($pendingRequests as $inspectionRequest) {
                if ($inspectorIndex >= $availableInspectors->count()) {
                    break; // No more available inspectors
                }

                $inspector = $availableInspectors[$inspectorIndex];
                
                // Try to find best match based on specializations
                $bestInspector = $this->findBestInspectorMatch($inspectionRequest, $availableInspectors);
                if ($bestInspector) {
                    $inspector = $bestInspector;
                    // Remove from available list to avoid double assignment
                    $availableInspectors = $availableInspectors->reject(function ($item) use ($bestInspector) {
                        return $item->id === $bestInspector->id;
                    });
                } else {
                    $inspectorIndex++;
                }

                // Assign inspector
                $inspectionRequest->assignInspector($inspector, auth()->user());

                // Auto-schedule for tomorrow
                $scheduledDate = Carbon::tomorrow();
                $scheduledTime = $this->getOptimalTimeSlot($inspector, $scheduledDate);
                $inspectionRequest->schedule($scheduledDate, $scheduledTime);

                $assignedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Auto-assignment completed successfully",
                'assigned_count' => $assignedCount,
                'available_inspectors' => $availableInspectors->count(),
                'pending_requests' => $pendingRequests->count()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Auto-assignment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unassign an inspector from a request
     */
    public function unassign(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'request_id' => 'required|integer|exists:inspection_requests,id',
            'reason' => 'nullable|string|max:500'
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

            $inspectionRequest = InspectionRequest::with('assignedInspector')
                ->findOrFail($request->request_id);

            if (!$inspectionRequest->assigned_inspector_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No inspector assigned to this request'
                ], 400);
            }

            $inspector = $inspectionRequest->assignedInspector;

            // Record status change
            $reason = $request->reason ?? 'Unassigned by admin';
            $inspectionRequest->recordStatusChange(
                'assigned', 
                'pending', 
                auth()->id(), 
                $reason
            );

            // Update request status
            $inspectionRequest->update([
                'assigned_inspector_id' => null,
                'assigned_by' => null,
                'assigned_at' => null,
                'scheduled_date' => null,
                'scheduled_time' => null,
                'status' => 'pending'
            ]);

            // Make inspector available again
            if ($inspector) {
                $inspector->update(['availability_status' => 'available']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Assignment removed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assignment statistics
     */
    public function statistics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'pending_requests' => InspectionRequest::where('status', 'pending')->count(),
            'assigned_today' => InspectionRequest::where('status', 'assigned')
                ->whereDate('assigned_at', $today)->count(),
            'completed_today' => InspectionRequest::where('status', 'completed')
                ->whereDate('completed_at', $today)->count(),
            'available_inspectors' => Inspector::where('availability_status', 'available')->count(),
            'busy_inspectors' => Inspector::where('availability_status', 'busy')->count(),
            'assignments_this_month' => InspectionRequest::where('status', 'assigned')
                ->whereDate('assigned_at', '>=', $thisMonth)->count(),
            'urgent_pending' => InspectionRequest::where('status', 'pending')
                ->whereIn('urgency', ['urgent', 'emergency'])->count(),
            'overdue_requests' => InspectionRequest::whereIn('status', ['assigned', 'in_progress'])
                ->where('scheduled_date', '<', $today)->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Find best inspector match based on specializations and location
     */
    private function findBestInspectorMatch(InspectionRequest $request, $availableInspectors)
    {
        // Get property type and determine required specializations
        $propertyType = $request->property->property_type ?? 'residential';
        $packageServices = $request->package->services ?? [];

        $bestMatch = null;
        $bestScore = 0;

        foreach ($availableInspectors as $inspector) {
            $score = 0;
            $specializations = $inspector->specializations ?? [];

            // Score based on property type match
            if (in_array($propertyType, $specializations)) {
                $score += 10;
            }

            // Score based on package service requirements
            foreach ($packageServices as $service) {
                if (in_array($service, $specializations)) {
                    $score += 5;
                }
            }

            // Score based on rating
            $score += $inspector->rating;

            // Score based on workload (prefer less busy inspectors)
            $currentWorkload = $inspector->inspectionRequests()
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count();
            $score -= $currentWorkload * 2;

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $inspector;
            }
        }

        return $bestMatch;
    }

    /**
     * Get optimal time slot for inspector based on their schedule
     */
    private function getOptimalTimeSlot($inspector, $date)
    {
        // Check existing assignments for the date
        $existingAssignments = InspectionRequest::where('assigned_inspector_id', $inspector->id)
            ->where('scheduled_date', $date->toDateString())
            ->orderBy('scheduled_time')
            ->pluck('scheduled_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        // Preferred time slots
        $timeSlots = ['09:00', '11:00', '14:00', '16:00'];

        // Find first available slot
        foreach ($timeSlots as $slot) {
            if (!in_array($slot, $existingAssignments)) {
                return Carbon::parse($date->toDateString() . ' ' . $slot);
            }
        }

        // If all preferred slots are taken, schedule 2 hours after last appointment
        if (!empty($existingAssignments)) {
            $lastTime = max($existingAssignments);
            return Carbon::parse($date->toDateString() . ' ' . $lastTime)->addHours(2);
        }

        // Default to 9 AM
        return Carbon::parse($date->toDateString() . ' 09:00');
    }

    /**
     * Reassign a request to a different inspector
     */
    public function reassign(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'request_id' => 'required|integer|exists:inspection_requests,id',
            'new_inspector_id' => 'required|integer|exists:inspectors,id',
            'reason' => 'nullable|string|max:500'
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

            // Unassign current inspector
            $this->unassign(new Request([
                'request_id' => $request->request_id,
                'reason' => 'Reassigned: ' . ($request->reason ?? 'No reason provided')
            ]));

            // Assign to new inspector
            $result = $this->assign(new Request([
                'request_id' => $request->request_id,
                'inspector_id' => $request->new_inspector_id,
                'notes' => 'Reassigned from previous inspector'
            ]));

            DB::commit();

            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Reassignment failed: ' . $e->getMessage()
            ], 500);
        }
    }
}