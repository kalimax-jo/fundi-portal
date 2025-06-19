<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionPackage;
use App\Models\InspectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class InspectionPackageController extends Controller
{
    /**
     * Get all inspection packages
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = InspectionPackage::with(['services']);

            // Filter by client type
            if ($request->has('client_type') && $request->client_type) {
                $query->forClientType($request->client_type);
            }

            // Filter active packages only
            if ($request->get('active_only', true)) {
                $query->active();
            }

            // Filter by pricing type
            if ($request->has('pricing_type')) {
                if ($request->pricing_type === 'fixed') {
                    $query->fixedPrice();
                } elseif ($request->pricing_type === 'custom') {
                    $query->customQuote();
                }
            }

            // Ordering
            $packages = $query->orderByPrice('asc')->get();

            // Transform the data
            $transformedPackages = $packages->map(function ($package) {
                return $this->transformPackage($package);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'packages' => $transformedPackages,
                    'filters' => [
                        'client_type' => $request->client_type,
                        'active_only' => $request->get('active_only', true),
                        'pricing_type' => $request->pricing_type
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve packages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific inspection package with detailed information
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $package = InspectionPackage::with([
                'services' => function ($query) {
                    $query->orderBy('package_services.sort_order');
                },
                'inspectionRequests' => function ($query) {
                    $query->latest()->limit(10);
                }
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'package' => $this->transformPackageDetailed($package)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve package',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new inspection package (Admin only)
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

            // Validate package data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:inspection_packages',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'duration_hours' => 'nullable|integer|min:1|max:24',
                'is_custom_quote' => 'nullable|boolean',
                'target_client_type' => 'required|in:individual,business,both',
                'is_active' => 'nullable|boolean',
                'services' => 'nullable|array',
                'services.*' => 'integer|exists:inspection_services,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create the package
            $package = InspectionPackage::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'price' => $request->price,
                'currency' => $request->get('currency', 'RWF'),
                'duration_hours' => $request->get('duration_hours', 4),
                'is_custom_quote' => $request->get('is_custom_quote', false),
                'target_client_type' => $request->target_client_type,
                'is_active' => $request->get('is_active', true)
            ]);

            // Attach services if provided
            if ($request->has('services') && is_array($request->services)) {
                foreach ($request->services as $index => $serviceId) {
                    $package->services()->attach($serviceId, [
                        'is_mandatory' => true,
                        'sort_order' => $index + 1
                    ]);
                }
            }

            // Load relationships
            $package->load('services');

            return response()->json([
                'success' => true,
                'message' => 'Package created successfully',
                'data' => [
                    'package' => $this->transformPackage($package)
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create package',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an inspection package (Admin only)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $package = InspectionPackage::findOrFail($id);

            // Validate update data
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:100|unique:inspection_packages,name,' . $id,
                'display_name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'currency' => 'sometimes|nullable|string|max:3',
                'duration_hours' => 'sometimes|nullable|integer|min:1|max:24',
                'is_custom_quote' => 'sometimes|nullable|boolean',
                'target_client_type' => 'sometimes|required|in:individual,business,both',
                'is_active' => 'sometimes|nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update the package
            $package->update($validator->validated());

            // Load relationships
            $package->load('services');

            return response()->json([
                'success' => true,
                'message' => 'Package updated successfully',
                'data' => [
                    'package' => $this->transformPackage($package)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update package',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update package price (Admin only)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updatePrice(Request $request, int $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $package = InspectionPackage::findOrFail($id);

            // Validate price data
            $validator = Validator::make($request->all(), [
                'price' => 'required|numeric|min:0',
                'is_custom_quote' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update price
            if ($request->has('is_custom_quote') && $request->is_custom_quote) {
                $package->setAsCustomQuote();
            } else {
                $package->setAsFixedPrice($request->price);
            }

            return response()->json([
                'success' => true,
                'message' => 'Package price updated successfully',
                'data' => [
                    'package' => [
                        'id' => $package->id,
                        'name' => $package->name,
                        'price' => $package->price,
                        'formatted_price' => $package->getFormattedPrice(),
                        'is_custom_quote' => $package->is_custom_quote
                    ]
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update price',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add service to package (Admin only)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function addService(Request $request, int $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $package = InspectionPackage::findOrFail($id);

            // Validate service data
            $validator = Validator::make($request->all(), [
                'service_id' => 'required|integer|exists:inspection_services,id',
                'is_mandatory' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $service = InspectionService::findOrFail($request->service_id);

            // Check if service is already in package
            if ($package->services()->where('service_id', $service->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service is already included in this package'
                ], 400);
            }

            // Add service to package
            $package->addService(
                $service,
                $request->get('is_mandatory', true),
                $request->get('sort_order', $package->services()->count() + 1)
            );

            return response()->json([
                'success' => true,
                'message' => 'Service added to package successfully',
                'data' => [
                    'service' => [
                        'id' => $service->id,
                        'name' => $service->name,
                        'category' => $service->category,
                        'is_mandatory' => $request->get('is_mandatory', true),
                        'sort_order' => $request->get('sort_order', $package->services()->count())
                    ]
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Package or service not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove service from package (Admin only)
     * 
     * @param Request $request
     * @param int $id
     * @param int $serviceId
     * @return JsonResponse
     */
    public function removeService(Request $request, int $id, int $serviceId): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $package = InspectionPackage::findOrFail($id);
            $service = InspectionService::findOrFail($serviceId);

            // Check if service is in package
            if (!$package->services()->where('service_id', $service->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service is not included in this package'
                ], 400);
            }

            // Remove service from package
            $package->removeService($service);

            return response()->json([
                'success' => true,
                'message' => 'Service removed from package successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Package or service not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get package comparison data
     * 
     * @return JsonResponse
     */
    public function getComparison(): JsonResponse
    {
        try {
            $comparison = InspectionPackage::getPackageComparison();

            return response()->json([
                'success' => true,
                'data' => [
                    'comparison' => $comparison
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get package comparison',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get package usage statistics (Admin only)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getStatistics(Request $request, int $id): JsonResponse
    {
        try {
            // Check if user is admin or head technician
            if (!$request->user()->isAdmin() && !$request->user()->isHeadTechnician()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin or Head Technician access required.'
                ], 403);
            }

            $package = InspectionPackage::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $package->getUsageStatistics()
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
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
     * Calculate price with discount for business partner
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function calculatePrice(Request $request, int $id): JsonResponse
    {
        try {
            $package = InspectionPackage::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'business_partner_id' => 'nullable|integer|exists:business_partners,id',
                'discount_percentage' => 'nullable|numeric|min:0|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $originalPrice = $package->price;
            $discountPercentage = 0;

            // Get discount from business partner if provided
            if ($request->has('business_partner_id')) {
                $businessPartner = \App\Models\BusinessPartner::find($request->business_partner_id);
                if ($businessPartner) {
                    $discountPercentage = $businessPartner->discount_percentage;
                }
            } elseif ($request->has('discount_percentage')) {
                $discountPercentage = $request->discount_percentage;
            }

            $discountedPrice = $package->getDiscountedPrice($discountPercentage);
            $discountAmount = $originalPrice - $discountedPrice;

            return response()->json([
                'success' => true,
                'data' => [
                    'package' => [
                        'id' => $package->id,
                        'name' => $package->name,
                        'display_name' => $package->display_name
                    ],
                    'pricing' => [
                        'original_price' => $originalPrice,
                        'discount_percentage' => $discountPercentage,
                        'discount_amount' => $discountAmount,
                        'final_price' => $discountedPrice,
                        'currency' => $package->currency,
                        'formatted_original_price' => number_format($originalPrice, 0, '.', ',') . ' ' . $package->currency,
                        'formatted_final_price' => number_format($discountedPrice, 0, '.', ',') . ' ' . $package->currency,
                        'is_custom_quote' => $package->is_custom_quote
                    ]
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate price',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform package for API response
     * 
     * @param InspectionPackage $package
     * @return array
     */
    private function transformPackage(InspectionPackage $package): array
    {
        return [
            'id' => $package->id,
            'name' => $package->name,
            'display_name' => $package->display_name,
            'description' => $package->description,
            'pricing' => [
                'price' => $package->price,
                'formatted_price' => $package->getFormattedPrice(),
                'currency' => $package->currency,
                'is_custom_quote' => $package->is_custom_quote
            ],
            'specifications' => [
                'duration_hours' => $package->duration_hours,
                'estimated_duration_minutes' => $package->getTotalEstimatedDuration(),
                'services_count' => $package->getServicesCount(),
                'mandatory_services_count' => $package->getMandatoryServicesCount()
            ],
            'target_client_type' => $package->target_client_type,
            'target_client_type_display' => $package->getClientTypeDisplayName(),
            'recommended_use_cases' => $package->getRecommendedUseCases(),
            'services' => $package->services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'category' => $service->category,
                    'category_display' => $service->getCategoryDisplayName(),
                    'estimated_duration_minutes' => $service->estimated_duration_minutes,
                    'is_mandatory' => $service->pivot->is_mandatory,
                    'sort_order' => $service->pivot->sort_order
                ];
            }),
            'is_active' => $package->is_active,
            'created_at' => $package->created_at,
            'updated_at' => $package->updated_at
        ];
    }

    /**
     * Transform package with detailed information
     * 
     * @param InspectionPackage $package
     * @return array
     */
    private function transformPackageDetailed(InspectionPackage $package): array
    {
        $basic = $this->transformPackage($package);
        
        $basic['usage_statistics'] = $package->getUsageStatistics();
        $basic['services_by_category'] = $package->getServicesByCategory();
        
        $basic['recent_requests'] = $package->inspectionRequests->map(function ($request) {
            return [
                'id' => $request->id,
                'request_number' => $request->request_number,
                'requester_type' => $request->requester_type,
                'status' => $request->status,
                'created_at' => $request->created_at
            ];
        });

        return $basic;
    }
}