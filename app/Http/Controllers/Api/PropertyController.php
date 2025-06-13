<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Get all properties (with filtering and search)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Property::query()->with(['inspectionRequests', 'completedInspections']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->search($request->search);
            }

            // Filter by property type
            if ($request->has('type') && $request->type) {
                $query->byType($request->type);
            }

            // Filter by location
            if ($request->has('district') && $request->district) {
                $query->byLocation($request->district);
            }

            if ($request->has('sector') && $request->sector) {
                $query->byLocation($request->get('district'), $request->sector);
            }

            // Filter properties needing inspection
            if ($request->has('needs_inspection') && $request->needs_inspection === 'true') {
                $monthsThreshold = $request->get('months_threshold', 12);
                $query->needsInspection($monthsThreshold);
            }

            // Location-based filtering (find properties within radius)
            if ($request->has('latitude') && $request->has('longitude') && $request->has('radius')) {
                $query->withinRadius(
                    $request->latitude,
                    $request->longitude,
                    $request->radius
                );
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100); // Max 100 items per page
            $properties = $query->paginate($perPage);

            // Transform the data
            $transformedProperties = $properties->getCollection()->map(function ($property) {
                return $this->transformProperty($property);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'properties' => $transformedProperties,
                    'pagination' => [
                        'current_page' => $properties->currentPage(),
                        'last_page' => $properties->lastPage(),
                        'per_page' => $properties->perPage(),
                        'total' => $properties->total(),
                        'from' => $properties->firstItem(),
                        'to' => $properties->lastItem()
                    ],
                    'filters' => [
                        'search' => $request->search,
                        'type' => $request->type,
                        'district' => $request->district,
                        'sector' => $request->sector,
                        'needs_inspection' => $request->needs_inspection
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve properties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new property
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate property data
            $validator = Validator::make($request->all(), [
                'owner_name' => 'required|string|max:255',
                'owner_phone' => 'nullable|string|max:20',
                'owner_email' => 'nullable|email|max:255',
                'property_type' => 'required|in:residential,commercial,industrial,mixed',
                'property_subtype' => 'nullable|string|max:100',
                'address' => 'required|string',
                'district' => 'required|string|max:100',
                'sector' => 'nullable|string|max:100',
                'cell' => 'nullable|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'built_year' => 'nullable|integer|min:1800|max:' . (date('Y') + 5),
                'total_area_sqm' => 'nullable|numeric|min:0',
                'floors_count' => 'nullable|integer|min:1|max:100',
                'bedrooms_count' => 'nullable|integer|min:0|max:50',
                'bathrooms_count' => 'nullable|integer|min:0|max:50',
                'market_value' => 'nullable|numeric|min:0',
                'additional_notes' => 'nullable|string',
                'property_photos' => 'nullable|array',
                'property_photos.*' => 'string' // Base64 or URL strings
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create the property
            $property = Property::create($validator->validated());

            // Handle photo uploads if provided
            if ($request->has('property_photos') && is_array($request->property_photos)) {
                $uploadedPhotos = [];
                foreach ($request->property_photos as $photo) {
                    $uploadedPhotos[] = $this->handlePhotoUpload($photo, $property->property_code);
                }
                $property->update(['property_photos' => $uploadedPhotos]);
            }

            // Load relationships
            $property->load(['inspectionRequests', 'completedInspections']);

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully',
                'data' => [
                    'property' => $this->transformProperty($property)
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific property
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $property = Property::with([
                'inspectionRequests.package',
                'inspectionRequests.assignedInspector.user',
                'completedInspections'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'property' => $this->transformPropertyDetailed($property)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a property
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $property = Property::findOrFail($id);

            // Validate update data
            $validator = Validator::make($request->all(), [
                'owner_name' => 'sometimes|required|string|max:255',
                'owner_phone' => 'sometimes|nullable|string|max:20',
                'owner_email' => 'sometimes|nullable|email|max:255',
                'property_type' => 'sometimes|required|in:residential,commercial,industrial,mixed',
                'property_subtype' => 'sometimes|nullable|string|max:100',
                'address' => 'sometimes|required|string',
                'district' => 'sometimes|required|string|max:100',
                'sector' => 'sometimes|nullable|string|max:100',
                'cell' => 'sometimes|nullable|string|max:100',
                'latitude' => 'sometimes|nullable|numeric|between:-90,90',
                'longitude' => 'sometimes|nullable|numeric|between:-180,180',
                'built_year' => 'sometimes|nullable|integer|min:1800|max:' . (date('Y') + 5),
                'total_area_sqm' => 'sometimes|nullable|numeric|min:0',
                'floors_count' => 'sometimes|nullable|integer|min:1|max:100',
                'bedrooms_count' => 'sometimes|nullable|integer|min:0|max:50',
                'bathrooms_count' => 'sometimes|nullable|integer|min:0|max:50',
                'market_value' => 'sometimes|nullable|numeric|min:0',
                'additional_notes' => 'sometimes|nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update the property
            $property->update($validator->validated());

            // Load relationships
            $property->load(['inspectionRequests', 'completedInspections']);

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully',
                'data' => [
                    'property' => $this->transformProperty($property)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a property
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $property = Property::findOrFail($id);

            // Check if property has inspection requests
            if ($property->inspectionRequests()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete property with existing inspection requests'
                ], 400);
            }

            // Delete property photos from storage
            if ($property->property_photos) {
                foreach ($property->property_photos as $photo) {
                    Storage::delete($photo);
                }
            }

            $property->delete();

            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload property photos
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function uploadPhotos(Request $request, int $id): JsonResponse
    {
        try {
            $property = Property::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'photos' => 'required|array|max:10',
                'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120' // 5MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $uploadedPhotos = [];
            $existingPhotos = $property->property_photos ?? [];

            foreach ($request->file('photos') as $photo) {
                $filename = $property->property_code . '_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('properties/' . $property->property_code, $filename, 'public');
                $uploadedPhotos[] = $path;
            }

            // Merge with existing photos
            $allPhotos = array_merge($existingPhotos, $uploadedPhotos);
            $property->update(['property_photos' => $allPhotos]);

            return response()->json([
                'success' => true,
                'message' => 'Photos uploaded successfully',
                'data' => [
                    'uploaded_photos' => $uploadedPhotos,
                    'total_photos' => count($allPhotos)
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a property photo
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function deletePhoto(Request $request, int $id): JsonResponse
    {
        try {
            $property = Property::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'photo_path' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $photos = $property->property_photos ?? [];
            $photoToDelete = $request->photo_path;

            if (in_array($photoToDelete, $photos)) {
                // Remove from array
                $photos = array_filter($photos, fn($photo) => $photo !== $photoToDelete);
                
                // Update property
                $property->update(['property_photos' => array_values($photos)]);
                
                // Delete from storage
                Storage::delete($photoToDelete);

                return response()->json([
                    'success' => true,
                    'message' => 'Photo deleted successfully',
                    'data' => [
                        'remaining_photos' => count($photos)
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Photo not found'
                ], 404);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete photo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get property statistics
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getStatistics(int $id): JsonResponse
    {
        try {
            $property = Property::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $property->getStatistics()
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
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
     * Get Rwanda districts and sectors
     * 
     * @return JsonResponse
     */
    public function getLocations(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'districts' => Property::getRwandaDistricts(),
                    'property_types' => [
                        'residential' => 'Residential',
                        'commercial' => 'Commercial',
                        'industrial' => 'Industrial',
                        'mixed' => 'Mixed Use'
                    ],
                    'property_subtypes' => Property::getSubtypesByType()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get locations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find properties near a location
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function findNearby(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'nullable|numeric|min:0.1|max:100',
                'limit' => 'nullable|integer|min:1|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $radius = $request->get('radius', 10); // Default 10km
            $limit = $request->get('limit', 20);

            $properties = Property::withinRadius(
                $request->latitude,
                $request->longitude,
                $radius
            )
            ->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", 
                [$request->latitude, $request->longitude, $request->latitude])
            ->orderBy('distance')
            ->limit($limit)
            ->get();

            $transformedProperties = $properties->map(function ($property) {
                $transformed = $this->transformProperty($property);
                $transformed['distance_km'] = round($property->distance, 2);
                return $transformed;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'properties' => $transformedProperties,
                    'search_params' => [
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'radius_km' => $radius,
                        'total_found' => $properties->count()
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to find nearby properties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform property for API response
     * 
     * @param Property $property
     * @return array
     */
    private function transformProperty(Property $property): array
    {
        return [
            'id' => $property->id,
            'property_code' => $property->property_code,
            'owner_name' => $property->owner_name,
            'owner_phone' => $property->owner_phone,
            'owner_email' => $property->owner_email,
            'property_type' => $property->property_type,
            'property_type_display' => $property->getTypeDisplayName(),
            'property_subtype' => $property->property_subtype,
            'property_subtype_display' => $property->getSubtypeDisplayName(),
            'address' => $property->address,
            'location' => [
                'district' => $property->district,
                'sector' => $property->sector,
                'cell' => $property->cell,
                'full_location' => $property->full_location,
                'coordinates' => [
                    'latitude' => $property->latitude,
                    'longitude' => $property->longitude
                ]
            ],
            'specifications' => [
                'built_year' => $property->built_year,
                'property_age' => $property->getPropertyAge(),
                'total_area_sqm' => $property->total_area_sqm,
                'floors_count' => $property->floors_count,
                'bedrooms_count' => $property->bedrooms_count,
                'bathrooms_count' => $property->bathrooms_count,
                'market_value' => $property->market_value,
                'value_per_sqm' => $property->getValuePerSquareMeter()
            ],
            'inspection_info' => [
                'last_inspection_date' => $property->last_inspection_date,
                'months_since_last_inspection' => $property->getMonthsSinceLastInspection(),
                'needs_inspection' => $property->needsInspection(),
                'total_inspections' => $property->getInspectionCount(),
                'recommended_package' => $property->getRecommendedPackage()
            ],
            'photos' => $property->property_photos ? array_map(function($photo) {
                return Storage::url($photo);
            }, $property->property_photos) : [],
            'additional_notes' => $property->additional_notes,
            'created_at' => $property->created_at,
            'updated_at' => $property->updated_at
        ];
    }

    /**
     * Transform property with detailed information
     * 
     * @param Property $property
     * @return array
     */
    private function transformPropertyDetailed(Property $property): array
    {
        $basic = $this->transformProperty($property);
        
        $basic['inspection_history'] = $property->inspectionRequests->map(function ($request) {
            return [
                'id' => $request->id,
                'request_number' => $request->request_number,
                'package' => $request->package->display_name,
                'status' => $request->status,
                'status_display' => $request->getStatusDisplayName(),
                'inspector' => $request->assignedInspector ? [
                    'name' => $request->assignedInspector->user->full_name,
                    'code' => $request->assignedInspector->inspector_code
                ] : null,
                'scheduled_date' => $request->scheduled_date,
                'completed_at' => $request->completed_at,
                'total_cost' => $request->total_cost
            ];
        });

        $basic['statistics'] = $property->getStatistics();

        return $basic;
    }

    /**
     * Handle photo upload (base64 or file)
     * 
     * @param mixed $photo
     * @param string $propertyCode
     * @return string
     */
    private function handlePhotoUpload($photo, string $propertyCode): string
    {
        // This is a simple implementation for base64 strings
        // You can expand this to handle different upload types
        if (is_string($photo) && strpos($photo, 'data:image') === 0) {
            // Base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photo));
            $filename = $propertyCode . '_' . time() . '_' . uniqid() . '.jpg';
            $path = 'properties/' . $propertyCode . '/' . $filename;
            Storage::put($path, $imageData);
            return $path;
        }
        
        return $photo; // Return as-is if it's already a path
    }
}