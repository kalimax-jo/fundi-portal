<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionRequest;
use App\Models\Property;
use App\Models\InspectionPackage;
use App\Models\Inspector;
use App\Models\BusinessPartner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InspectionRequestController extends Controller
{
    /**
     * Get all inspection requests (with filtering and permissions)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $query = InspectionRequest::with([
                'requester', 'property', 'package', 'assignedInspector.user', 'businessPartner'
            ]);

            // Apply user-based filtering based on role
            if ($user->isIndividualClient()) {
                // Individual clients can only see their own requests
                $query->where('requester_user_id', $user->id);
            } elseif ($user->isBusinessPartner()) {
                // Business partners can see requests from their organization
                $partnerIds = $user->businessPartners->pluck('id');
                $query->whereIn('business_partner_id', $partnerIds);
            } elseif ($user->isInspector()) {
                // Inspectors can see requests assigned to them
                $query->where('assigned_inspector_id', $user->inspector->id);
            }
            // Admins and head technicians can see all requests (no filter)

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->search($request->search);
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->byStatus($request->status);
            }

            // Filter by requester type
            if ($request->has('requester_type') && $request->requester_type) {
                $query->byRequesterType($request->requester_type);
            }

            // Filter by urgency
            if ($request->has('urgency') && $request->urgency) {
                $query->byUrgency($request->urgency);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Filter by scheduled date
            if ($request->has('scheduled_date') && $request->scheduled_date) {
                $query->whereDate('scheduled_date', $request->scheduled_date);
            }

            // Special filters
            if ($request->has('overdue') && $request->overdue === 'true') {
                $query->overdue();
            }

            if ($request->has('today') && $request->today === 'true') {
                $query->today();
            }

            if ($request->has('this_week') && $request->this_week === 'true') {
                $query->thisWeek();
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Special sorting for urgency
            if ($sortBy === 'urgency') {
                $query->orderByRaw("CASE urgency WHEN 'emergency' THEN 1 WHEN 'urgent' THEN 2 ELSE 3 END");
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $requests = $query->paginate($perPage);

            // Transform the data
            $transformedRequests = $requests->getCollection()->map(function ($inspectionRequest) {
                return $this->transformInspectionRequest($inspectionRequest);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'inspection_requests' => $transformedRequests,
                    'pagination' => [
                        'current_page' => $requests->currentPage(),
                        'last_page' => $requests->lastPage(),
                        'per_page' => $requests->perPage(),
                        'total' => $requests->total(),
                        'from' => $requests->firstItem(),
                        'to' => $requests->lastItem()
                    ],
                    'filters' => [
                        'search' => $request->search,
                        'status' => $request->status,
                        'requester_type' => $request->requester_type,
                        'urgency' => $request->urgency,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inspection requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new inspection request
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validate inspection request data
            $validator = Validator::make($request->all(), [
                'property_id' => 'required|integer|exists:properties,id',
                'package_id' => 'required|integer|exists:inspection_packages,id',
                'purpose' => 'required|in:rental,sale,purchase,loan_collateral,insurance,maintenance,other',
                'urgency' => 'nullable|in:normal,urgent,emergency',
                'preferred_date' => 'nullable|date|after:today',
                'preferred_time_slot' => 'nullable|in:morning,afternoon,evening,flexible',
                'special_instructions' => 'nullable|string|max:1000',
                
                // Business partner specific fields
                'business_partner_id' => 'nullable|integer|exists:business_partners,id',
                'loan_amount' => 'nullable|numeric|min:0',
                'loan_reference' => 'nullable|string|max:100',
                'applicant_name' => 'nullable|string|max:255',
                'applicant_phone' => 'nullable|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Determine requester type
            $requesterType = 'individual';
            $businessPartnerId = null;

            if ($user->isBusinessPartner() && $request->has('business_partner_id')) {
                // Verify user belongs to this business partner
                if ($user->businessPartners->contains($request->business_partner_id)) {
                    $requesterType = 'business_partner';
                    $businessPartnerId = $request->business_partner_id;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have access to this business partner'
                    ], 403);
                }
            }

            // Validate property and package
            $property = Property::findOrFail($request->property_id);
            $package = InspectionPackage::findOrFail($request->package_id);

            // Check if package is available for client type
            if (!$package->isAvailableForClientType($requesterType)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This package is not available for your client type'
                ], 400);
            }

            // Create the inspection request
            $inspectionRequest = InspectionRequest::create([
                'requester_type' => $requesterType,
                'requester_user_id' => $user->id,
                'business_partner_id' => $businessPartnerId,
                'property_id' => $property->id,
                'package_id' => $package->id,
                'purpose' => $request->purpose,
                'urgency' => $request->get('urgency', 'normal'),
                'preferred_date' => $request->preferred_date,
                'preferred_time_slot' => $request->get('preferred_time_slot', 'flexible'),
                'special_instructions' => $request->special_instructions,
                'loan_amount' => $request->loan_amount,
                'loan_reference' => $request->loan_reference,
                'applicant_name' => $request->applicant_name,
                'applicant_phone' => $request->applicant_phone,
                'status' => 'pending'
            ]);

            // Load relationships
            $inspectionRequest->load(['requester', 'property', 'package', 'businessPartner']);

            return response()->json([
                'success' => true,
                'message' => 'Inspection request created successfully',
                'data' => [
                    'inspection_request' => $this->transformInspectionRequest($inspectionRequest)
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create inspection request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific inspection request
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            $inspectionRequest = InspectionRequest::with([
                'requester', 'property', 'package.services', 'assignedInspector.user',
                'businessPartner', 'statusHistory.changedByUser', 'payments'
            ])->findOrFail($id);

            // Check permissions
            if (!$this->canAccessRequest($user, $inspectionRequest)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view this inspection request'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'inspection_request' => $this->transformInspectionRequestDetailed($inspectionRequest)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inspection request not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inspection request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign inspector to inspection request (Head Technician/Admin only)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function assignInspector(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();

            // Check permissions
            if (!$user->isAdmin() && !$user->isHeadTechnician()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin or Head Technician access required.'
                ], 403);
            }

            $inspectionRequest = InspectionRequest::findOrFail($id);

            // Validate assignment data
            $validator = Validator::make($request->all(), [
                'inspector_id' => 'required|integer|exists:inspectors,id',
                'scheduled_date' => 'nullable|date|after:today',
                'scheduled_time' => 'nullable|date_format:H:i'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $inspector = Inspector::with('user')->findOrFail($request->inspector_id);

            // Check if inspector is available
            if (!$inspector->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inspector is not available'
                ], 400);
            }

            // Assign inspector
            $inspectionRequest->assignInspector($inspector, $user);

            // Schedule if date/time provided
            if ($request->has('scheduled_date')) {
                $scheduledDateTime = Carbon::parse($request->scheduled_date);
                if ($request->has('scheduled_time')) {
                    $time = Carbon::parse($request->scheduled_time);
                    $scheduledDateTime->setTime($time->hour, $time->minute);
                }
                $inspectionRequest->schedule($scheduledDateTime, $scheduledDateTime);
            }

            // Load relationships
            $inspectionRequest->load(['assignedInspector.user']);

            return response()->json([
                'success' => true,
                'message' => 'Inspector assigned successfully',
                'data' => [
                    'inspection_request' => $this->transformInspectionRequest($inspectionRequest),
                    'assigned_inspector' => [
                        'id' => $inspector->id,
                        'name' => $inspector->user->full_name,
                        'inspector_code' => $inspector->inspector_code,
                        'phone' => $inspector->user->phone,
                        'specializations' => $inspector->specializations
                    ]
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inspection request or inspector not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign inspector',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start inspection (Inspector only)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function startInspection(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $inspectionRequest = InspectionRequest::findOrFail($id);

            // Check if user is the assigned inspector
            if (!$user->isInspector() || $inspectionRequest->assigned_inspector_id !== $user->inspector->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to start this inspection'
                ], 403);
            }

            // Start the inspection
            $inspectionRequest->start();

            return response()->json([
                'success' => true,
                'message' => 'Inspection started successfully',
                'data' => [
                    'inspection_request' => $this->transformInspectionRequest($inspectionRequest)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inspection request not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start inspection',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete inspection (Inspector only)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function completeInspection(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $inspectionRequest = InspectionRequest::findOrFail($id);

            // Check if user is the assigned inspector
            if (!$user->isInspector() || $inspectionRequest->assigned_inspector_id !== $user->inspector->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to complete this inspection'
                ], 403);
            }

            // Validate completion data
            $validator = Validator::make($request->all(), [
                'total_cost' => 'nullable|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Complete the inspection
            $totalCost = $request->get('total_cost', $inspectionRequest->calculateTotalCost());
            $inspectionRequest->complete($totalCost);

            return response()->json([
                'success' => true,
                'message' => 'Inspection completed successfully',
                'data' => [
                    'inspection_request' => $this->transformInspectionRequest($inspectionRequest)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inspection request not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete inspection',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel inspection request
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $inspectionRequest = InspectionRequest::findOrFail($id);

            // Check permissions
            if (!$this->canModifyRequest($user, $inspectionRequest)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to cancel this inspection request'
                ], 403);
            }

            // Validate cancellation data
            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cancel the inspection
            $reason = $request->get('reason', 'Cancelled by user');
            $inspectionRequest->cancel($user, $reason);

            return response()->json([
                'success' => true,
                'message' => 'Inspection request cancelled successfully',
                'data' => [
                    'inspection_request' => $this->transformInspectionRequest($inspectionRequest)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inspection request not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel inspection request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getDashboardStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $query = InspectionRequest::query();

            // Apply user-based filtering
            if ($user->isIndividualClient()) {
                $query->where('requester_user_id', $user->id);
            } elseif ($user->isBusinessPartner()) {
                $partnerIds = $user->businessPartners->pluck('id');
                $query->whereIn('business_partner_id', $partnerIds);
            } elseif ($user->isInspector()) {
                $query->where('assigned_inspector_id', $user->inspector->id);
            }

            // Calculate statistics
            $totalRequests = $query->count();
            $pendingRequests = $query->clone()->pending()->count();
            $inProgressRequests = $query->clone()->inProgress()->count();
            $completedRequests = $query->clone()->completed()->count();
            $urgentRequests = $query->clone()->urgent()->count();
            $todayRequests = $query->clone()->today()->count();
            $overdueRequests = $query->clone()->overdue()->count();

            // Recent requests
            $recentRequests = $query->clone()
                ->with(['property', 'package', 'assignedInspector.user'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($request) {
                    return $this->transformInspectionRequest($request);
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => [
                        'total_requests' => $totalRequests,
                        'pending_requests' => $pendingRequests,
                        'in_progress_requests' => $inProgressRequests,
                        'completed_requests' => $completedRequests,
                        'urgent_requests' => $urgentRequests,
                        'today_requests' => $todayRequests,
                        'overdue_requests' => $overdueRequests,
                        'completion_rate' => $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100, 1) : 0
                    ],
                    'recent_requests' => $recentRequests
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can access the inspection request
     * 
     * @param User $user
     * @param InspectionRequest $request
     * @return bool
     */
    private function canAccessRequest($user, $request): bool
    {
        // Admins and head technicians can access all requests
        if ($user->isAdmin() || $user->isHeadTechnician()) {
            return true;
        }

        // Individual clients can access their own requests
        if ($user->isIndividualClient() && $request->requester_user_id === $user->id) {
            return true;
        }

        // Business partners can access requests from their organization
        if ($user->isBusinessPartner() && $user->businessPartners->contains($request->business_partner_id)) {
            return true;
        }

        // Inspectors can access requests assigned to them
        if ($user->isInspector() && $request->assigned_inspector_id === $user->inspector->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can modify the inspection request
     * 
     * @param User $user
     * @param InspectionRequest $request
     * @return bool
     */
    private function canModifyRequest($user, $request): bool
    {
        // Admins can modify all requests
        if ($user->isAdmin()) {
            return true;
        }

        // Requesters can cancel their own pending requests
        if ($request->requester_user_id === $user->id && $request->status === 'pending') {
            return true;
        }

        // Head technicians can modify most requests
        if ($user->isHeadTechnician()) {
            return true;
        }

        return false;
    }

    /**
     * Transform inspection request for API response
     * 
     * @param InspectionRequest $request
     * @return array
     */
    private function transformInspectionRequest($request): array
    {
        return [
            'id' => $request->id,
            'request_number' => $request->request_number,
            'requester_type' => $request->requester_type,
            'requester' => [
                'id' => $request->requester->id,
                'name' => $request->requester->full_name,
                'email' => $request->requester->email,
                'phone' => $request->requester->phone
            ],
            'business_partner' => $request->businessPartner ? [
                'id' => $request->businessPartner->id,
                'name' => $request->businessPartner->name,
                'type' => $request->businessPartner->type
            ] : null,
            'property' => [
                'id' => $request->property->id,
                'property_code' => $request->property->property_code,
                'address' => $request->property->address,
                'type' => $request->property->property_type,
                'district' => $request->property->district
            ],
            'package' => [
                'id' => $request->package->id,
                'name' => $request->package->name,
                'display_name' => $request->package->display_name,
                'price' => $request->package->price,
                'formatted_price' => $request->package->getFormattedPrice()
            ],
            'request_details' => [
                'purpose' => $request->purpose,
                'purpose_display' => $request->getPurposeDisplayName(),
                'urgency' => $request->urgency,
                'urgency_info' => $request->getUrgencyInfo(),
                'preferred_date' => $request->preferred_date,
                'preferred_time_slot' => $request->preferred_time_slot,
                'special_instructions' => $request->special_instructions
            ],
            'loan_details' => $request->loan_amount ? [
                'loan_amount' => $request->loan_amount,
                'loan_reference' => $request->loan_reference,
                'applicant_name' => $request->applicant_name,
                'applicant_phone' => $request->applicant_phone
            ] : null,
            'assignment' => [
                'status' => $request->status,
                'status_display' => $request->getStatusDisplayName(),
                'assigned_inspector' => $request->assignedInspector ? [
                    'id' => $request->assignedInspector->id,
                    'name' => $request->assignedInspector->user->full_name,
                    'inspector_code' => $request->assignedInspector->inspector_code,
                    'phone' => $request->assignedInspector->user->phone
                ] : null,
                'assigned_at' => $request->assigned_at,
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time
            ],
            'timeline' => [
                'started_at' => $request->started_at,
                'completed_at' => $request->completed_at,
                'created_at' => $request->created_at
            ],
            'financial' => [
                'total_cost' => $request->total_cost,
                'payment_status' => $request->payment_status,
                'payment_status_display' => $request->getPaymentStatusDisplayName()
            ],
            'statistics' => $request->getStatistics()
        ];
    }

    /**
     * Transform inspection request with detailed information
     * 
     * @param InspectionRequest $request
     * @return array
     */
    private function transformInspectionRequestDetailed($request): array
    {
        $basic = $this->transformInspectionRequest($request);
        
        // Add detailed package information
        $basic['package']['services'] = $request->package->services->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'category' => $service->category,
                'estimated_duration_minutes' => $service->estimated_duration_minutes,
                'is_mandatory' => $service->pivot->is_mandatory
            ];
        });

        // Add status history
        $basic['status_history'] = $request->statusHistory->map(function ($history) {
            return [
                'old_status' => $history->old_status,
                'new_status' => $history->new_status,
                'changed_by' => $history->changedByUser->full_name,
                'change_reason' => $history->change_reason,
                'changed_at' => $history->changed_at
            ];
        });

        // Add payment information
        $basic['payments'] = $request->payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'transaction_reference' => $payment->transaction_reference,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'initiated_at' => $payment->initiated_at,
                'completed_at' => $payment->completed_at
            ];
        });

        return $basic;
    }
}