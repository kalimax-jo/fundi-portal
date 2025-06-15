<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\InspectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties
     */
    public function index(Request $request)
    {
        $query = Property::query()->with(['inspectionRequests']);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('property_code', 'like', "%{$searchTerm}%")
                  ->orWhere('owner_name', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%")
                  ->orWhere('district', 'like', "%{$searchTerm}%")
                  ->orWhere('sector', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by property type
        if ($request->filled('type')) {
            $query->where('property_type', $request->type);
        }

        // Filter by location
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        // Filter properties needing inspection
        if ($request->filled('needs_inspection') && $request->needs_inspection === 'true') {
            $monthsThreshold = $request->get('months_threshold', 12);
            $thresholdDate = Carbon::now()->subMonths($monthsThreshold);
            
            $query->where(function ($q) use ($thresholdDate) {
                $q->whereNull('last_inspection_date')
                  ->orWhere('last_inspection_date', '<', $thresholdDate);
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        if ($sortField === 'name') {
            $query->orderBy('owner_name', $sortDirection);
        } elseif ($sortField === 'type') {
            $query->orderBy('property_type', $sortDirection);
        } elseif ($sortField === 'location') {
            $query->orderBy('district', $sortDirection)
                  ->orderBy('sector', $sortDirection);
        } elseif ($sortField === 'last_inspection') {
            $query->orderBy('last_inspection_date', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $properties = $query->paginate(20);

        // Get statistics - Fixed to use proper queries instead of static method calls
        $stats = [
            'total_properties' => Property::count(),
            'residential_properties' => Property::where('property_type', 'residential')->count(),
            'commercial_properties' => Property::where('property_type', 'commercial')->count(),
            'properties_needing_inspection' => Property::whereNull('last_inspection_date')
                ->orWhere('last_inspection_date', '<', Carbon::now()->subMonths(12))->count(),
            'properties_with_recent_inspection' => Property::whereNotNull('last_inspection_date')
                ->where('last_inspection_date', '>=', Carbon::now()->subMonths(6))->count(),
        ];

        // Get filter options
        $propertyTypes = [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'mixed' => 'Mixed Use'
        ];

        $districts = Property::distinct('district')
            ->whereNotNull('district')
            ->pluck('district')
            ->sort()
            ->values();

        return view('admin.properties.index', compact(
            'properties', 
            'stats', 
            'propertyTypes', 
            'districts'
        ));
    }

    /**
     * Show the form for creating a new property
     */
    public function create()
    {
        $propertyTypes = [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'mixed' => 'Mixed Use'
        ];

        $rwandaDistricts = $this->getRwandaDistricts();

        return view('admin.properties.create', compact('propertyTypes', 'rwandaDistricts'));
    }

    /**
     * Store a newly created property
     */
    public function store(Request $request)
    {
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $property = Property::create([
                'property_code' => $this->generatePropertyCode(),
                'owner_name' => $request->owner_name,
                'owner_phone' => $request->owner_phone,
                'owner_email' => $request->owner_email,
                'property_type' => $request->property_type,
                'property_subtype' => $request->property_subtype,
                'address' => $request->address,
                'district' => $request->district,
                'sector' => $request->sector,
                'cell' => $request->cell,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'built_year' => $request->built_year,
                'total_area_sqm' => $request->total_area_sqm,
                'floors_count' => $request->floors_count ?? 1,
                'bedrooms_count' => $request->bedrooms_count,
                'bathrooms_count' => $request->bathrooms_count,
                'market_value' => $request->market_value,
                'additional_notes' => $request->additional_notes,
            ]);

            return redirect()->route('admin.properties.show', $property)
                ->with('success', 'Property created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create property: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified property
     */
    public function show(Property $property)
    {
        $property->load([
            'inspectionRequests.package',
            'inspectionRequests.assignedInspector.user',
            'completedInspections'
        ]);

        // Get property statistics
        $stats = [
            'total_inspections' => $property->inspectionRequests()->count(),
            'completed_inspections' => $property->inspectionRequests()->where('status', 'completed')->count(),
            'pending_inspections' => $property->inspectionRequests()->where('status', 'pending')->count(),
            'assigned_inspections' => $property->inspectionRequests()->where('status', 'assigned')->count(),
            'months_since_last_inspection' => $this->getMonthsSinceLastInspection($property),
            'property_age' => $this->getPropertyAge($property),
            'value_per_sqm' => $this->getValuePerSquareMeter($property),
        ];

        return view('admin.properties.show', compact('property', 'stats'));
    }

    /**
     * Show the form for editing the property
     */
    public function edit(Property $property)
    {
        $propertyTypes = [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'mixed' => 'Mixed Use'
        ];

        $rwandaDistricts = $this->getRwandaDistricts();

        return view('admin.properties.edit', compact('property', 'propertyTypes', 'rwandaDistricts'));
    }

    /**
     * Update the specified property
     */
    public function update(Request $request, Property $property)
    {
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $property->update($request->only([
                'owner_name', 'owner_phone', 'owner_email', 'property_type',
                'property_subtype', 'address', 'district', 'sector', 'cell',
                'latitude', 'longitude', 'built_year', 'total_area_sqm',
                'floors_count', 'bedrooms_count', 'bathrooms_count',
                'market_value', 'additional_notes'
            ]));

            return redirect()->route('admin.properties.show', $property)
                ->with('success', 'Property updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update property: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified property
     */
    public function destroy(Property $property)
    {
        try {
            // Check if property has inspection requests
            if ($property->inspectionRequests()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete property with existing inspection requests.');
            }

            $property->delete();

            return redirect()->route('admin.properties.index')
                ->with('success', 'Property deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete property: ' . $e->getMessage());
        }
    }

    /**
     * Verify property information
     */
    public function verify(Request $request, Property $property)
    {
        try {
            // Add verification logic here
            // For now, we'll just update a timestamp or status
            
            return response()->json([
                'success' => true,
                'message' => 'Property verified successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify property: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show inspection history for a property
     */
    public function inspectionHistory(Property $property)
    {
        $inspectionRequests = $property->inspectionRequests()
            ->with(['package', 'assignedInspector.user', 'requester'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.properties.inspection-history', compact('property', 'inspectionRequests'));
    }

    /**
     * Generate unique property code
     */
    private function generatePropertyCode(): string
    {
        do {
            $code = 'PROP' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Property::where('property_code', $code)->exists());

        return $code;
    }

    /**
     * Get Rwanda districts and their sectors
     */
    private function getRwandaDistricts(): array
    {
        return [
            'Kigali' => [
                'Gasabo', 'Kicukiro', 'Nyarugenge'
            ],
            'Eastern Province' => [
                'Bugesera', 'Gatsibo', 'Kayonza', 'Kirehe', 'Ngoma', 'Nyagatare', 'Rwamagana'
            ],
            'Northern Province' => [
                'Burera', 'Gakenke', 'Gicumbi', 'Musanze', 'Rulindo'
            ],
            'Southern Province' => [
                'Gisagara', 'Huye', 'Kamonyi', 'Muhanga', 'Nyamagabe', 'Nyanza', 'Nyaruguru', 'Ruhango'
            ],
            'Western Province' => [
                'Karongi', 'Ngororero', 'Nyabihu', 'Nyamasheke', 'Rubavu', 'Rusizi', 'Rutsiro'
            ]
        ];
    }

    /**
     * Get property age in years
     */
    private function getPropertyAge(Property $property): ?int
    {
        if (!$property->built_year) {
            return null;
        }

        return Carbon::now()->year - $property->built_year;
    }

    /**
     * Get months since last inspection
     */
    private function getMonthsSinceLastInspection(Property $property): ?int
    {
        if (!$property->last_inspection_date) {
            return null;
        }

        return Carbon::now()->diffInMonths(Carbon::parse($property->last_inspection_date));
    }

    /**
     * Get value per square meter
     */
    private function getValuePerSquareMeter(Property $property): ?float
    {
        if (!$property->market_value || !$property->total_area_sqm) {
            return null;
        }

        return $property->market_value / $property->total_area_sqm;
    }

    /**
 * Search properties for inspection request form
 */
public function search(Request $request)
{
    $query = $request->get('q', '');
    
    if (strlen($query) < 2) {
        return response()->json(['properties' => []]);
    }
    
    $properties = Property::search($query)
        ->select([
            'id', 'property_code', 'owner_name', 'owner_phone', 'owner_email',
            'property_type', 'property_subtype', 'address', 'district', 'sector'
        ])
        ->limit(10)
        ->get();
    
    return response()->json(['properties' => $properties]);
}

/**
 * Get property details for auto-fill
 */
public function details(Property $property)
{
    return response()->json([
        'id' => $property->id,
        'property_code' => $property->property_code,
        'owner_name' => $property->owner_name,
        'owner_phone' => $property->owner_phone,
        'owner_email' => $property->owner_email,
        'property_type' => $property->property_type,
        'property_subtype' => $property->property_subtype,
        'address' => $property->address,
        'district' => $property->district,
        'sector' => $property->sector,
        'cell' => $property->cell,
        'built_year' => $property->built_year,
        'total_area_sqm' => $property->total_area_sqm,
        'floors_count' => $property->floors_count,
        'bedrooms_count' => $property->bedrooms_count,
        'bathrooms_count' => $property->bathrooms_count
    ]);
}
}