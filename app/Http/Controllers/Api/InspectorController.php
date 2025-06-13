<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inspector;
use App\Models\User;
use App\Models\InspectorCertification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class InspectorController extends Controller
{
    /**
     * Get all inspectors (Admin/Head Technician only)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Check permissions
            if (!$request->user()->isAdmin() && !$request->user()->isHeadTechnician()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin or Head Technician access required.'
                ], 403);
            }

            $query = Inspector::with(['user', 'certifications']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->search($request->search);
            }

            // Filter by availability
            if ($request->has('availability') && $request->availability) {
                $query->where('availability_status', $request->availability);
            }

            // Filter by certification level
            if ($request->has('certification_level') && $request->certification_level) {
                $query->byCertificationLevel($request->certification_level);
            }

            // Filter by specialization
            if ($request->has('specialization') && $request->specialization) {
                $query->withSpecialization($request->specialization);
            }

            // Filter by rating
            if ($request->has('min_rating') && $request->min_rating) {
                $query->highlyRated($request->min_rating);
            }

            // Location-based filtering
            if ($request->has('latitude') && $request->has('longitude') && $request->has('radius')) {
                $query->withinRadius(
                    $request->latitude,
                    $request->longitude,
                    $request->radius
                );
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'rating');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $inspectors = $query->paginate($perPage);

            // Transform the data
            $transformedInspectors = $inspectors->getCollection()->map(function ($inspector) {
                return $this->transformInspector($inspector);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'inspectors' => $transformedInspectors,
                    'pagination' => [
                        'current_page' => $inspectors->currentPage(),
                        'last_page' => $inspectors->lastPage(),
                        'per_page' => $inspectors->perPage(),
                        'total' => $inspectors->total()
                    ],
                    'filters' => [
                        'search' => $request->search,
                        'availability' => $request->availability,
                        'certification_level' => $request->certification_level,
                        'specialization' => $request->specialization
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inspectors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new inspector (Admin only)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            // Validate inspector data
            $validator = Validator::make($request->all(), [
                // User details
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:20|unique:users',
                'password' => 'required|string|min:8',
                
                // Inspector specific
                'certification_level' => 'required|in:basic,advanced,expert',
                'specializations' => 'nullable|array',
                'specializations.*' => 'string|in:residential,commercial,industrial,thermal_imaging,environmental,foundation,electrical,plumbing,safety,renovation',
                'experience_years' => 'nullable|integer|min:0|max:50',
                'certification_expiry' => 'nullable|date|after:today',
                'equipment_assigned' => 'nullable|array',
                'equipment_assigned.*' => 'string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create user account
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => 'active'
            ]);

            // Assign inspector role
            $inspectorRole = \App\Models\Role::where('name', 'inspector')->first();
            if ($inspectorRole) {
                $user->roles()->attach($inspectorRole->id, [
                    'assigned_at' => now(),
                    'assigned_by' => $request->user()->id
                ]);
            }

            // Create inspector profile
            $inspector = Inspector::create([
                'user_id' => $user->id,
                'certification_level' => $request->certification_level,
                'specializations' => $request->get('specializations', []),
                'experience_years' => $request->get('experience_years', 0),
                'certification_expiry' => $request->certification_expiry,
                'equipment_assigned' => $request->get('equipment_assigned', []),
                'availability_status' => 'available',
                'rating' => 0.00,
                'total_inspections' => 0
            ]);

            // Load relationships
            $inspector->load(['user', 'certifications']);

            return response()->json([
                'success' => true,
                'message' => 'Inspector created successfully',
                'data' => [
                    'inspector' => $this->transformInspector($inspector),
                    'login_credentials' => [
                        'email' => $user->email,
                        'temporary_password' => $request->password
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create inspector',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific inspector
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            
            $inspector = Inspector::with([
                'user', 'certifications', 'inspectionRequests.property', 
                'inspectionRequests.package', 'activeInspections'
            ])->findOrFail($id);

            // Check permissions
            if (!$this->canAccessInspector($user, $inspector)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view this inspector'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'inspector' => $this->transformInspectorDetailed($inspector)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inspector not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inspector',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update inspector profile
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $inspector = Inspector::with('user')->findOrFail($id);

            // Check permissions
            if (!$this->canModifyInspector($user, $inspector)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to update this inspector'
                ], 403);
            }

            // Validate update data
            $validator = Validator::make($request->all(), [
                'certification_level' => 'sometimes|required|in:basic,advanced,expert',
                'specializations' => 'sometimes|nullable|array',
                'specializations.*' => 'string|in:residential,commercial,industrial,thermal_imaging,environmental,foundation,electrical,plumbing,safety,renovation',
                'experience_years' => 'sometimes|nullable|integer|min:0|max:50',
                'certification_expiry' => 'sometimes|nullable|date|after:today',
                'equipment_assigned' => 'sometimes|nullable|array',
                'equipment_assigned.*' => 'string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update inspector profile
            $inspector->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Inspector updated successfully',
                'data' => [
                    'inspector' => $this->transformInspector($inspector)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inspector not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update inspector',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update inspector location (Inspector only)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function updateLocation(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Check if user is an inspector
            if (!$user->isInspector()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only inspectors can update their location'
                ], 403);
            }

            // Validate location data
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update location
            $inspector = $user->inspector;
            $inspector->updateLocation($request->latitude, $request->longitude);

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully',
                'data' => [
                    'location' => [
                        'latitude' => $inspector->current_location_lat,
                        'longitude' => $inspector->current_location_lng,
                        'updated_at' => $inspector->updated_at
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update inspector availability status
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAvailability(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Check if user is an inspector
            if (!$user->isInspector()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only inspectors can update their availability'
                ], 403);
            }

            // Validate availability data
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:available,busy,offline'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update availability
            $inspector = $user->inspector;
            $inspector->update(['availability_status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Availability updated successfully',
                'data' => [
                    'availability_status' => $inspector->availability_status,
                    'availability_display' => $inspector->getAvailabilityDisplayName(),
                    'updated_at' => $inspector->updated_at
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inspector assignments (current and upcoming)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAssignments(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Check if user is an inspector
            if (!$user->isInspector()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only inspectors can view their assignments'
                ], 403);
            }

            $inspector = $user->inspector;
            
            // Get assignments with filters
            $query = $inspector->inspectionRequests()
                ->with(['property', 'package', 'requester', 'businessPartner']);

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            } else {
                // Default: show active assignments
                $query->whereIn('status', ['assigned', 'in_progress']);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('scheduled_date', '>=', $request->start_date);
            }
            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('scheduled_date', '<=', $request->end_date);
            }

            // Get today's assignments by default
            if (!$request->has('status') && !$request->has('start_date')) {
                $query->whereDate('scheduled_date', today());
            }

            $assignments = $query->orderBy('scheduled_date')
                ->orderBy('scheduled_time')
                ->get();

            // Transform assignments
            $transformedAssignments = $assignments->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'request_number' => $assignment->request_number,
                    'status' => $assignment->status,
                    'status_display' => $assignment->getStatusDisplayName(),
                    'urgency' => $assignment->urgency,
                    'urgency_info' => $assignment->getUrgencyInfo(),
                    'property' => [
                        'id' => $assignment->property->id,
                        'property_code' => $assignment->property->property_code,
                        'address' => $assignment->property->address,
                        'type' => $assignment->property->property_type,
                        'coordinates' => [
                            'latitude' => $assignment->property->latitude,
                            'longitude' => $assignment->property->longitude
                        ]
                    ],
                    'package' => [
                        'id' => $assignment->package->id,
                        'name' => $assignment->package->display_name,
                        'estimated_duration_minutes' => $assignment->package->getTotalEstimatedDuration()
                    ],
                    'client' => [
                        'name' => $assignment->requester->full_name,
                        'phone' => $assignment->requester->phone,
                        'type' => $assignment->requester_type,
                        'business_partner' => $assignment->businessPartner ? $assignment->businessPartner->name : null
                    ],
                    'schedule' => [
                        'scheduled_date' => $assignment->scheduled_date,
                        'scheduled_time' => $assignment->scheduled_time,
                        'preferred_time_slot' => $assignment->preferred_time_slot
                    ],
                    'special_instructions' => $assignment->special_instructions,
                    'created_at' => $assignment->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'assignments' => $transformedAssignments,
                    'summary' => [
                        'total_assignments' => $assignments->count(),
                        'pending_today' => $assignments->where('status', 'assigned')->where('scheduled_date', today())->count(),
                        'in_progress' => $assignments->where('status', 'in_progress')->count(),
                        'urgent_assignments' => $assignments->whereIn('urgency', ['urgent', 'emergency'])->count()
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve assignments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inspector performance statistics
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getStatistics(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $inspector = Inspector::findOrFail($id);

            // Check permissions
            if (!$this->canAccessInspector($user, $inspector)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view this inspector\'s statistics'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $inspector->getPerformanceStatistics()
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inspector not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find best inspector for assignment
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function findBestInspector(Request $request): JsonResponse
    {
        try {
            // Check permissions
            if (!$request->user()->isAdmin() && !$request->user()->isHeadTechnician()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin or Head Technician access required.'
                ], 403);
            }

            // Validate search criteria
            $validator = Validator::make($request->all(), [
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'property_type' => 'nullable|string|in:residential,commercial,industrial,mixed',
                'required_specializations' => 'nullable|array',
                'required_specializations.*' => 'string',
                'max_distance' => 'nullable|numeric|min:1|max:200'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $bestInspector = Inspector::findBestInspectorForAssignment(
                $request->latitude,
                $request->longitude,
                $request->property_type,
                $request->get('required_specializations', []),
                $request->get('max_distance', 50)
            );

            if (!$bestInspector) {
                return response()->json([
                    'success' => false,
                    'message' => 'No suitable inspector found for the given criteria'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'inspector' => $this->transformInspector($bestInspector),
                    'match_criteria' => [
                        'distance_km' => $request->latitude && $request->longitude ? 
                            $bestInspector->distanceTo($request->latitude, $request->longitude) : null,
                        'specializations_match' => array_intersect(
                            $bestInspector->specializations ?? [],
                            $request->get('required_specializations', [])
                        ),
                        'current_workload' => $bestInspector->getCurrentWorkload(),
                        'rating' => $bestInspector->rating
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to find best inspector',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available specializations and equipment
     * 
     * @return JsonResponse
     */
    public function getOptions(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'specializations' => Inspector::getAvailableSpecializations(),
                    'equipment' => \App\Models\InspectionService::getAvailableEquipment(),
                    'certification_levels' => [
                        'basic' => 'Basic Inspector',
                        'advanced' => 'Advanced Inspector',
                        'expert' => 'Expert Inspector'
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get options',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can access inspector information
     * 
     * @param User $user
     * @param Inspector $inspector
     * @return bool
     */
    private function canAccessInspector($user, $inspector): bool
    {
        // Admins and head technicians can access all inspectors
        if ($user->isAdmin() || $user->isHeadTechnician()) {
            return true;
        }

        // Inspectors can access their own profile
        if ($user->isInspector() && $inspector->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can modify inspector information
     * 
     * @param User $user
     * @param Inspector $inspector
     * @return bool
     */
    private function canModifyInspector($user, $inspector): bool
    {
        // Admins can modify all inspectors
        if ($user->isAdmin()) {
            return true;
        }

        // Inspectors can modify their own profile (limited fields)
        if ($user->isInspector() && $inspector->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Transform inspector for API response
     * 
     * @param Inspector $inspector
     * @return array
     */
    private function transformInspector($inspector): array
    {
        return [
            'id' => $inspector->id,
            'inspector_code' => $inspector->inspector_code,
            'user' => [
                'id' => $inspector->user->id,
                'name' => $inspector->user->full_name,
                'email' => $inspector->user->email,
                'phone' => $inspector->user->phone,
                'status' => $inspector->user->status
            ],
            'certification' => [
                'level' => $inspector->certification_level,
                'level_display' => $inspector->getCertificationLevelDisplayName(),
                'expiry_date' => $inspector->certification_expiry,
                'days_until_expiry' => $inspector->getDaysUntilCertificationExpiry(),
                'is_expiring' => $inspector->isCertificationExpiring()
            ],
            'specializations' => $inspector->specializations ?? [],
            'specializations_display' => $inspector->getSpecializationsList(),
            'experience_years' => $inspector->experience_years,
            'equipment_assigned' => $inspector->equipment_assigned ?? [],
            'availability' => [
                'status' => $inspector->availability_status,
                'status_display' => $inspector->getAvailabilityDisplayName(),
                'is_available' => $inspector->isAvailable()
            ],
            'location' => [
                'latitude' => $inspector->current_location_lat,
                'longitude' => $inspector->current_location_lng
            ],
            'performance' => [
                'rating' => $inspector->rating,
                'total_inspections' => $inspector->total_inspections,
                'current_workload' => $inspector->getCurrentWorkload(),
                'this_month_inspections' => $inspector->getThisMonthInspections()
            ],
            'created_at' => $inspector->created_at,
            'updated_at' => $inspector->updated_at
        ];
    }

    /**
     * Transform inspector with detailed information
     * 
     * @param Inspector $inspector
     * @return array
     */
    private function transformInspectorDetailed($inspector): array
    {
        $basic = $this->transformInspector($inspector);
        
        // Add detailed performance statistics
        $basic['detailed_statistics'] = $inspector->getPerformanceStatistics();
        
        // Add certifications
        $basic['certifications'] = $inspector->certifications->map(function ($cert) {
            return [
                'id' => $cert->id,
                'name' => $cert->certification_name,
                'issuing_body' => $cert->issuing_body,
                'issue_date' => $cert->issue_date,
                'expiry_date' => $cert->expiry_date,
                'is_active' => $cert->is_active
            ];
        });

        // Add recent assignments
        $basic['recent_assignments'] = $inspector->inspectionRequests()
            ->with(['property', 'package'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'request_number' => $assignment->request_number,
                    'property_address' => $assignment->property->address,
                    'package_name' => $assignment->package->display_name,
                    'status' => $assignment->status,
                    'scheduled_date' => $assignment->scheduled_date,
                    'completed_at' => $assignment->completed_at
                ];
            });

        return $basic;
    }
}